<?php namespace Netinteractive\Elegant\Relation;

use Closure;
use Netinteractive\Elegant\Relation\Pivot;
use Netinteractive\Elegant\Model\Query\Builder;
use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Model\Collection;

abstract class Relation {

	/**
	 * The Elegant query builder instance.
	 *
	 * @var \Netinteractive\Elegant\Db\Query\Builder
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
	 * Indicates if the relation is adding constraints.
	 *
	 * @var bool
	 */
	protected static $constraints = true;

	/**
	 * Create a new relation instance.
	 *
	 * @param  \Netinteractive\Elegant\Db\Query\Builder  $query
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
     * @param Record $related
     * @return $this
     */
    public function setRelated(Record $related)
    {
        $this->related = $related;
        return $this;
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
	 * @param  array  $records
	 * @return void
	 */
	abstract public function addEagerConstraints(array $records);

	/**
	 * Initialize the relation on a set of records.
	 *
	 * @param  array   $records
	 * @param  string  $relation
	 * @return array
	 */
	abstract public function initRelation(array $records, $relation);

	/**
	 * Match the eagerly loaded results to their parents.
	 *
	 * @param  array   $records
	 * @param  \Netinteractive\Elegant\Model\Collection  $results
	 * @param  string  $relation
	 * @return array
	 */
	abstract public function match( array $records, Collection $results, $relation);

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
		return $this->get();
	}



	/**
	 * Run a raw update against the base query.
	 *
	 * @param  array  $attributes
	 * @return int
	 */
	public function rawUpdate(array $attributes = array())
	{
		return $this->query->update($attributes);
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
	 * @return \Netinteractive\Elegant\Model\Record
	 */
	public function getQuery()
	{
		return $this->query;
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
     * @param Record $record
     * @return string
     */
    public function getForeignKey(Record $record)
    {
        return snake_case($record->getBlueprint()->getStorageName().'__id');
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
	 * Handle dynamic method calls to the relationship.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		$result = call_user_func_array(array($this->query, $method), $parameters);

		if ($result === $this->query) return $this;

		return $result;
	}

    /**
     * Get all of the primary keys for an array of records.
     *
     * @param  array   $records
     * @param  string  $keys
     * @return array
     */
    protected function getKeys(array $records, $keys = array())
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

        #keys array init
        foreach ($keys AS $key){
            $responseKeys[$key] = array();
        }

        #gathering and grouping keys
        foreach ($records AS $record){
            foreach ($keys AS $key){
                if (isSet($record->{$key})){
                    $responseKeys[$key][] = $record->{$key};
                }
            }
        }

        #unique on key values
        foreach ($responseKeys AS $key=>$val){
            $responseKeys[$key] = array_unique($responseKeys[$key]);
        }

        return $responseKeys;
    }

}
