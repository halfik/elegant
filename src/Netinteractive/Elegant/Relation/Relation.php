<?php namespace Netinteractive\Elegant\Relation;

use Closure;
use Netinteractive\Elegant\Query\Builder;
use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Model\Collection;
use Illuminate\Database\Query\Expression;

abstract class Relation {

	/**
	 * The Eloquent query builder instance.
	 *
	 * @var \Netinteractive\Elegant\Query\Builder
	 */
	protected $query;

	/**
	 * The parent record instance.
	 *
	 * @var \Netinteractive\Elegant\Model\Record
	 */
	protected $parent;

	/**
	 * Indicates if the relation is adding constraints.
	 *
	 * @var bool
	 */
	protected static $constraints = true;

	/**
	 * Create a new relation instance.
	 *
	 * @param  \Netinteractive\Elegant\Query\Builder  $query
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
	abstract public function match(array $records, Collection $results, $relation);

	/**
	 * Get the results of the relationship.
	 *
	 * @return mixed
	 */
	abstract public function getResults();

	/**
	 * Get the relationship for eager loading.
	 *
	 * @return \Illuminate\Database\Eloquent\Collection
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
	 * Add the constraints for a relationship count query.
	 *
	 * @param  \Netinteractive\Elegant\Query\Builder  $query
	 * @param  \Netinteractive\Elegant\Query\Builder  $parent
	 * @return \Netinteractive\Elegant\Query\Builder
	 */
	public function getRelationCountQuery(Builder $query, Builder $parent)
	{
		$query->select(new Expression('count(*)'));

		$key = $this->wrap($this->getQualifiedParentKeyName());

		return $query->where($this->getHasCompareKey(), '=', new Expression($key));
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
	 * Get all of the primary keys for an array of models.
	 *
	 * @param  array   $models
	 * @param  string  $key
	 * @return array
	 */
	protected function getKeys(array $models, $key = null)
	{
		return array_unique(array_values(array_map(function($value) use ($key)
		{
			return $key ? $value->getAttribute($key) : $value->getKey();

		}, $models)));
	}

	/**
	 * Get the underlying query for the relation.
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function getQuery()
	{
		return $this->query;
	}

	/**
	 * Get the base query builder driving the Eloquent builder.
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function getBaseQuery()
	{
		return $this->query->getQuery();
	}

	/**
	 * Get the parent model of the relation.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Get the fully qualified parent key name.
	 *
	 * @return string
	 */
	public function getQualifiedParentKeyName()
	{
		return $this->parent->getQualifiedKeyName();
	}


	/**
	 * Wrap the given value with the parent query's grammar.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function wrap($value)
	{
		return $this->parent->newQueryWithoutScopes()->getQuery()->getGrammar()->wrap($value);
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

}
