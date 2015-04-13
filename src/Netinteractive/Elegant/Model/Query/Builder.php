<?php namespace Netinteractive\Elegant\Model\Query;


use Netinteractive\Elegant\Query\Builder AS QueryBuilder;
use Closure;
use Netinteractive\Elegant\Model\Collection;
use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Relation\Relation;


/**
 * Class Builder
 * Model Query builder. It add relations related interface
 * @package Netinteractive\Elegant\Model\Query
 */
class Builder extends QueryBuilder
{
    /**
     * Record
     * @var null|Record
     */
    protected $record = null;

    /**
     * The relationships that should be loaded with record.
     *
     * @var array
     */
    protected $relationsToLoad = array();

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return \Netinteractive\Elegant\Model\Collection|static[]
     */
    public function get($columns = array('*'))
    {
        $records = $this->createRecords($columns);

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded, which will solve the
        // n+1 query issue for the developers to avoid running a lot of queries.
        if (count($records) > 0)
        {
            $records = $this->eagerLoadRelations($records);
        }

        return new Collection($records);
    }

    ##RECORD
    /**
     * @param Record $record
     * @return $this
     */
    public function setRecord(Record $record)
    {
        $this->record = $record;
        return $this;
    }

    /**
     * Returns blueprint
     * @return \Netinteractive\Elegant\Model\Record|null
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * Get the hydrated models without eager loading.
     *
     * @param  array  $columns
     * @return \Netinteractive\Elegant\Model\Record[]
     */
    public function createRecords($columns = array('*'))
    {
        // First, we will simply get the raw results from the query builders which we
        // can use to populate an array with Eloquent models. We will pass columns
        // that should be selected as well, which are typically just everything.
        $results = parent::get($columns);

        $records = array();

        // Once we have the results, we can spin through them and instantiate a fresh
        // model instance for each records we retrieved from the database. We will
        // also set the proper connection name for the model after we create it.
        foreach ($results as $result)
        {
            $record = $this->getRecord();

            $loadedRecord = clone $record;
            $loadedRecord->fill((array) $result);
            $loadedRecord->exists = true;

            $records[] = $loadedRecord;
        }

        return $records;
    }


    ##RELATIONS

    /**
     * @param array $relations
     * @return $this
     */
    public function setRelationsToLoad(array $relations)
    {
        $this->relationsToLoad = $relations;
        return $this;
    }

    /**
     * @return array
     */
    public function getRelationsToLoad()
    {
        return $this->relationsToLoad;
    }


    /**
     * Set the relationships that should be eager loaded.
     *
     * @param  mixed  $relations
     * @return $this
     */

    public function with($relations)
    {
        if (is_string($relations)) $relations = func_get_args();

        $parsed = $this->parseRelations($relations);

        $this->setRelationsToLoad(array_merge($this->getRelationsToLoad(), $parsed));

        return $this;
    }

    /**
     * Eager load the relationships for the records.
     *
     * @param  array  $records
     * @return array
     */
    public function eagerLoadRelations(array $records)
    {
        $relations = $this->getRelationsToLoad();
        foreach ($relations as $name => $constraints)
        {
            // For nested eager loads we'll skip loading them here and they will be set as an
            // eager load on the query to retrieve the relation so that they will be eager
            // loaded on that query, because that is where they get hydrated as models.
            if (strpos($name, '.') === false)
            {
                $records = $this->loadRelated($records, $name, $constraints);
            }
        }

        return $records;
    }

    /**
     * Get the relation instance for the given relation name.
     *
     * @param  string  $relation
     * @return \Netinteractive\Elegant\Relation\Relation
     */
    public function getRelation($relation)
    {
        // We want to run a relationship query without any constrains so that we will
        // not have to remove these where clauses manually which gets really hacky
        // and is error prone while we remove the developer's own where clauses.
        $query = Relation::noConstraints(function() use ($relation)
        {

            return $this->getRecord()->getRelation('db', $relation);
        });

        $nested = $this->nestedRelations($relation);

        // If there are nested relationships set on the query, we will put those onto
        // the query instances so that they can be handled after this relationship
        // is loaded. In this way they will all trickle down as they are loaded.
        if (count($nested) > 0)
        {
            //$query->getQuery()->with($nested);
            $query->with($nested);
        }

        return $query;
    }

    /**
     * Parse a list of relations into individuals.
     *
     * @param  array  $relations
     * @return array
     */
    protected function parseRelations(array $relations)
    {
        $results = array();

        foreach ($relations as $name => $constraints)
        {
            // If the "relation" value is actually a numeric key, we can assume that no
            // constraints have been specified for the eager load and we'll just put
            // an empty Closure with the loader so that we can treat all the same.
            if (is_numeric($name))
            {
                $f = function() {};

                list($name, $constraints) = array($constraints, $f);
            }

            // We need to separate out any nested includes. Which allows the developers
            // to load deep relationships using "dots" without stating each level of
            // the relationship with its own key in the array of eager load names.
            $results = $this->parseNested($name, $results);

            $results[$name] = $constraints;
        }

        return $results;
    }

    /**
     * Parse the nested relationships in a relation.
     *
     * @param  string  $name
     * @param  array   $results
     * @return array
     */
    protected function parseNested($name, $results)
    {
        $progress = array();

        // If the relation has already been set on the result array, we will not set it
        // again, since that would override any constraints that were already placed
        // on the relationships. We will only set the ones that are not specified.
        foreach (explode('.', $name) as $segment)
        {
            $progress[] = $segment;

            if ( ! isset($results[$last = implode('.', $progress)]))
            {
                $results[$last] = function() {};
            }
        }

        return $results;
    }

    /**
     * Eagerly load the relationship on a set of models.
     *
     * @param  array     $records
     * @param  string    $name
     * @param  \Closure  $constraints
     * @return array
     */
    protected function loadRelated(array $records, $name, Closure $constraints)
    {
        // First we will "back up" the existing where conditions on the query so we can
        // add our eager constraints. Then we will merge the wheres that were on the
        // query back to it in order that any where conditions might be specified.
        $relation = $this->getRelation($name);

        $relation->addEagerConstraints($records);

        call_user_func($constraints, $relation);

        $records = $relation->initRelation($records, $name);

        // Once we have the results, we just match those back up to their parent models
        // using the relationship instance. Then we just return the finished arrays
        // of models which have been eagerly hydrated and are readied for return.
        $results = $relation->getEager();

        return $relation->match($records, $results, $name);
    }

    /**
     * Get the deeply nested relations for a given top-level relation.
     *
     * @param  string  $relation
     * @return array
     */
    protected function nestedRelations($relation)
    {
        $nested = array();
        $relations = $this->getRelationsToLoad();

        // We are basically looking for any relationships that are nested deeper than
        // the given top-level relationship. We will just check for any relations
        // that start with the given top relations and adds them to our arrays.
        foreach ($relations AS $name => $constraints)
        {
            if ($this->isNested($name, $relation))
            {
                $nested[substr($name, strlen($relation.'.'))] = $constraints;
            }
        }

        return $nested;
    }

    /**
     * Determine if the relationship is nested.
     *
     * @param  string  $name
     * @param  string  $relation
     * @return bool
     */
    protected function isNested($name, $relation)
    {
        $dots = str_contains($name, '.');

        return $dots && starts_with($name, $relation.'.');
    }
}

