<?php

namespace Netinteractive\Elegant\Model\Query;


use Closure;
use Netinteractive\Elegant\Model\Collection;
use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Relation\Relation;


use Netinteractive\Elegant\Db\Query\Builder AS QueryBuilder;
use Illuminate\Database\ConnectionInterface AS ConnectionInterface;
use Netinteractive\Elegant\Db\Query\Grammars\Grammar AS Grammar;
use Netinteractive\Elegant\Db\Query\Processors\Processor AS Processor;



/**
 * Class Builder
 * Models Query builder. It add relations related interface
 * @package Netinteractive\Elegant\Models\Query
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
     * All of the registered builder macros.
     *
     * @var array
     */
    protected static $macros = array();

    /**
    * Create a new query builder instance.
    *
    * @param  \Illuminate\Database\ConnectionInterface $connection
    * @param  \Netinteractive\Elegant\Db\Query\Grammars\Grammar $grammar
    * @param  \Netinteractive\Elegant\Db\Query\Processors\Processor $processor
    */
    public function __construct(ConnectionInterface $connection = null, Grammar $grammar = null, Processor $processor = null)
    {
        parent::__construct($connection, $grammar, $processor);
    }


    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return \Netinteractive\Elegant\Model\Collection|static[]
     */
    public function get($columns = array('*'))
    {
        $this->whereSoftDeleted();

        // First, we will simply get the raw results from the query builders which we
        // can use to populate an array with Elegant records. We will pass columns
        // that should be selected as well, which are typically just everything.
        $results = parent::get($columns);

        if (isset($results[0]))
        {
            $result = array_change_key_case((array) $results[0]);
            if (array_key_exists('aggregate', $result)){
                return $results;
            }
        }

        $records = $this->createRecords($results);

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded, which will solve the
        // n+1 query issue for the developers to avoid running a lot of queries.
        if (count($records) > 0){
            $records = $this->eagerLoadRelations($records);
        }

        return \App::make('ni.elegant.model.collection', array($records));
    }

    /**
     * Method checks if record is soft deleted type and adds proper where statement to the query
     */
    protected function whereSoftDeleted()
    {
        if ($this->getRecord()->getBlueprint()->softDelete() === true){
            $deletedAtColumn =  $this->getFrom().'.'. $this->getRecord()->getBlueprint()->getDeletedAt();
            $this->whereNull($deletedAtColumn);
        }

        return $this;
    }

    ##RECORD
    /**
     * @param Record $record
     * @return $this
     */
    public function setRecord(Record $record)
    {
        $this->record = $record;
        $this->from($this->getRecord()->getBlueprint()->getStorageName());

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
     * @param  array  $results
     * @return \Netinteractive\Elegant\Model\Record[]
     */
    public function createRecords($results=array())
    {
        $records = \App::make('ni.elegant.model.collection', array());

        // Once we have the results, we can spin through them and instantiate a fresh
        // model instance for each records we retrieved from the database. We will
        // also set the proper connection name for the model after we create it.
        foreach ($results as $result)
        {
            $record = $this->getRecord();

            $loadedRecord = clone $record;
            $loadedRecord->fill((array) $result);

            $loadedRecord->syncOriginal();
            $loadedRecord->setExists(true);

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
        if (is_string($relations)){
            $relations = func_get_args();
        }

        $parsed = $this->parseRelations($relations);

        $this->setRelationsToLoad(array_merge($this->getRelationsToLoad(), $parsed));

        return $this;
    }

    /**
     * Eager load the relationships for the records.
     *
     * @param  Collection  $records
     * @return array
     */
    public function eagerLoadRelations(Collection $records)
    {
        $relations = $this->getRelationsToLoad();

        foreach ($relations as $name => $constraints){
            // For nested eager loads we'll skip loading them here and they will be set as an
            // eager load on the query to retrieve the relation so that they will be eager
            // loaded on that query, because that is where they get hydrated as models.
            if (strpos($name, '.') === false){
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
            return $this->getRecord()->getRelation($relation, 'db');
        });

        $nested = $this->nestedRelations($relation);

        // If there are nested relationships set on the query, we will put those onto
        // the query instances so that they can be handled after this relationship
        // is loaded. In this way they will all trickle down as they are loaded.
        if (count($nested) > 0){
            $query->getQuery()->with($nested);
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

        foreach ($relations as $name => $constraints) {

            // If the "name" value is actually a numeric key, we can assume that no
            // constraints have been specified for the eager load and we'll just put
            // an empty Closure with the loader so that we can treat all the same.
            if (is_numeric($name)) {
                $f = function() { };
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
        foreach (explode('.', $name) as $segment) {
            $progress[] = $segment;

            if ( ! isset($results[$last = implode('.', $progress)])) {
                $results[$last] = function() {};
            }
        }

        return $results;
    }

    /**
     * Eagerly load the relationship on a set of models.
     *
     * @param  \Netinteractive\Elegant\Model\Collection      $records
     * @param  string    $name
     * @param  \Closure  $constraints
     * @return array
     */
    protected function loadRelated(Collection $records, $name, Closure $constraints)
    {
        // First we will "back up" the existing where conditions on the query so we can
        // add our eager constraints. Then we will merge the wheres that were on the
        // query back to it in order that any where conditions might be specified.
        $relation = $this->getRelation($name);

        $relation->addEagerConstraints($records);

        call_user_func($constraints, $relation);

        $records =  $relation->initRelation($records, $name);

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
        foreach ($relations AS $name => $constraints) {
            if ($this->isNested($name, $relation)){
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

    /**
     * Extend the builder with a given callback.
     *
     * @param  string    $name
     * @param  \Closure  $callback
     * @return void
     */
    public static function macro($name, callable $callback)
    {
        self::$macros[$name] = $callback;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '';
    }

    /**
     * Get the given macro by name.
     *
     * @param  string  $name
     * @return \Closure
     */
    public function getMacro($name)
    {
        return array_get(self::$macros, $name);
    }

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (isset(self::$macros[$method])){
            array_unshift($parameters, $this);

            return call_user_func_array(self::$macros[$method], $parameters);
        }

        return $this;
    }
}

