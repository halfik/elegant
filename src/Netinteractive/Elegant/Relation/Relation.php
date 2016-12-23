<?php

namespace Netinteractive\Elegant\Relation;

use Closure;
use Netinteractive\Elegant\Model\Query\Builder;
use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Model\Collection;

abstract class Relation
{

	/**
	 * The Elegant query builder instance.
	 *
	 * @var \Netinteractive\Elegant\Model\Query\Builder
	 */
	protected $query;

	/**
	 * The parent record instance.
	 *
	 * @var \Netinteractive\Elegant\Model\Record
	 */
	protected $parent;

    /**
     * The related model instance.
     *
     * @var \Netinteractive\Elegant\Model\Record
     */
    protected $related;

    /**
     * @var string
     */
    public static $postFk = '__id';


	/**
	 * Indicates if the relation is adding constraints.
	 *
	 * @var bool
	 */
	protected static $constraints = true;

	/**
	 * Create a new relation instance.
	 *
	 * @param  \Netinteractive\Elegant\Model\Query\Builder  $query
	 * @param  \Netinteractive\Elegant\Model\Record  $parent
	 * @return void
	 */
	public function __construct(Builder $query, Record $parent)
	{
		$this->query = $query;
		$this->parent = $parent;

		$this->addConstraints();
	}

    /**
     * @param \Netinteractive\Elegant\Model\Record $related
     * @return $this
     */
    public function setRelated(Record $related)
    {
        $this->related = $related;
        return $this;
    }

    /**
     * Returns related record
     * @return Record
     */
    public function getRelated()
    {
        return $this->related;
    }

	/**
	 * Set the base constraints on the relation query.
	 *
	 * @return void
	 */
	abstract public function addConstraints();

	/**
	 * Set the constraints for an eager load of the relation.
	 *
	 * @param  \Netinteractive\Elegant\Model\Collection  $records
	 * @return void
	 */
	abstract public function addEagerConstraints(Collection $records);

	/**
	 * Initialize the relation on a set of records.
	 *
	 * @param  \Netinteractive\Elegant\Model\Collection   $records
	 * @param  string  $relation
	 * @return array
	 */
	abstract public function initRelation(Collection $records, $relation);

	/**
	 * Match the eagerly loaded results to their parents.
	 *
	 * @param  \Netinteractive\Elegant\Model\Collection   $records
	 * @param  \Netinteractive\Elegant\Model\Collection  $results
	 * @param  string  $relation
	 * @return array
	 */
	abstract public function match( Collection $records, Collection $results, $relation);

	/**
	 * Get the results of the relationship.
	 *
	 * @return mixed
	 */
	abstract public function getResults();


	/**
	 * Get the relationship for eager loading.
	 *
	 * @return \Netinteractive\Elegant\Model\Collection
	 */
	public function getEager()
	{
        #we have to set proper record on query so builder can clone and return proper objects
        $this->getQuery()->setRecord($this->getRelated());

		return $this->get(array('*'));
	}


    public function get($columns = array('*'))
    {
        #we have to set proper record on query so builder can clone and return proper objects
        $this->getQuery()->setRecord($this->getRelated());

        return $this->getQuery()->get($columns = array('*'));
    }

    /**
     * @param array $columns
     * @return mixed|null
     */
    public function first($columns = ['*'])
    {
        #we have to set proper record on query so builder can clone and return proper objects
        $this->getQuery()->setRecord($this->getRelated());

        return $this->getQuery()->first($columns);
    }

	/**
	 * Run a raw update against the base query.
	 *
	 * @param  array  $attributes
	 * @return int
	 */
	public function rawUpdate(array $attributes = array())
	{
		return $this->getQuery()->update($attributes);
	}

	/**
	 * Run a callback with constraints disabled on the relation.
	 *
	 * @param  \Closure  $callback
	 * @return mixed
	 */
	public static function noConstraints(Closure $callback)
	{
		static::$constraints = false;

		// When resetting the relation where clause, we want to shift the first element
		// off of the bindings, leaving only the constraints that the developers put
		// as "extra" on the relationships, and not original relation constraints.
		$results = call_user_func($callback);

		static::$constraints = true;

		return $results;
	}

	/**
	 * Get the underlying query for the relation.
	 *
	 * @return \Netinteractive\Elegant\Model\Query\Builder
	 */
	public function getQuery()
	{
		return $this->query;
	}

    /**
     * Get the underlying query for the relation.
     *
     * @param \Netinteractive\Elegant\Model\Query\Builder $query
     * @return $this
     */
    public function setQuery(Builder $query)
    {
        $this->query = $query;
        return $this;
    }

	/**
	 * Get the parent model of the relation.
	 *
	 * @return \Netinteractive\Elegant\Model\Record
	 */
	public function getParent()
	{
		return $this->parent;
	}


    /**
     * Get the default foreign key name for the record.
     * @param \Netinteractive\Elegant\Model\Record $record
     * @return string
     */
    public function getForeignKey(Record $record)
    {
        return snake_case($record->getBlueprint()->getStorageName().self::$postFk);
    }

    /**
     * Create a new pivot model instance.
     *
     * @param  \Netinteractive\Elegant\Model\Record $parent
     * @param  array   $attributes
     * @param  string  $table
     * @param  bool    $exists
     * @return \Netinteractive\Elegant\Relation\Pivot
     */
    public function newPivot(Record $parent, array $attributes, $table, $exists)
    {
        return new Pivot($parent, $attributes, $table, $exists);
    }



    /**
     * Get all of the primary keys of records.
     *
     * @param  \Netinteractive\Elegant\Model\Collection   $records
     * @param  string  $keys
     * @return array
     */
    protected function getKeys(Collection $records, $keys = array())
    {
        $responseKeys = array();

        if (empty($keys)){
            foreach ($records AS $record){
                $recordKeys = $record->getBlueprint()->getPrimaryKey();
                foreach ($recordKeys AS $key){
                    $keys[] = $key;
                }
            }
        }
        ;
        #keys array init
        foreach ($keys AS $key){
            if (!isSet($responseKeys[$key])){
                $responseKeys[$key] = array();
            }
        }

        #gathering and grouping keys
        foreach ($records AS $record){
            foreach ($keys AS $key){
                if (isSet($record->{$key})){
                    if (!in_array($record->{$key},  $responseKeys[$key])){
                        $responseKeys[$key][] = $record->{$key};
                    }

                }
            }
        }

        #unique on key values
        foreach ($responseKeys AS $key=>$val){
            $responseKeys[$key] = array_unique($responseKeys[$key]);
        }

        return $responseKeys;
    }

    /**
     * Handle dynamic method calls to the relationship.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $result = call_user_func_array(array($this->getQuery(), $method), $parameters);

        if ($result === $this->getQuery()) return $this;

        return $result;
    }

}
