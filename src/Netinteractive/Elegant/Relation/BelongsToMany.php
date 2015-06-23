<?php namespace Netinteractive\Elegant\Relation;

use Netinteractive\Elegant\Model\Query\Builder;
use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Model\Collection;


class BelongsToMany extends Relation
{

	/**
	 * The intermediate table for the relation.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The foreign key of the parent model.
	 *
	 * @var string
	 */
	protected $foreignKey;

	/**
	 * The associated key of the relation.
	 *
	 * @var string
	 */
	protected $otherKey;

	/**
	 * The "name" of the relationship.
	 *
	 * @var string
	 */
	protected $relationName;

	/**
	 * The pivot table columns to retrieve.
	 *
	 * @var array
	 */
	protected $pivotColumns = array();

	/**
	 * Any pivot table restrictions.
	 *
	 * @var array
	 */
	protected $pivotWheres = [];

	/**
	 * Create a new belongs to many relationship instance.
	 *
	 * @param  \Netinteractive\Elegant\Model\Query\Builder  $query
     * @param  \Netinteractive\Elegant\Model\Record $related
	 * @param  \Netinteractive\Elegant\Model\Record $parent
	 * @param  string  $table
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @param  string  $relationName
	 * @return void
	 */
	public function __construct(Builder $query, Record $related, Record $parent, $table, $foreignKey, $otherKey, $relationName = null)
	{

        if (!is_array($foreignKey)) {
            $foreignKey = array($foreignKey);
        }

        if (!is_array($otherKey)) {
            $otherKey = array($otherKey);
        }

		$this->table = $table;
		$this->otherKey = $otherKey;
		$this->foreignKey = $foreignKey;
		$this->relationName = $relationName;
        $this->related = $related;

		parent::__construct($query, $parent);
	}

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        $this->setJoin();

        if (static::$constraints){
            $this->setWhere();
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $records
     * @return void
     */
    public function addEagerConstraints(array $records)
    {
        $keys = $this->getForeignKey();
        $fkValueList = $this->getKeys($records);

        $parentPk = $this->parent->getBlueprint()->getPrimaryKey();

        foreach ($keys AS $index=>$fk){
            if (isSet($parentPk[$index])){
                $this->query->whereIn($fk, $fkValueList[$parentPk[$index]]);
            }
        }
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array   $records
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $records, $relation)
    {

        foreach ($records as $record){
            $record->setRelated($relation, \App('ni.elegant.model.collection', array()));
        }

        return $records;
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->get();
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return \Netinteractive\Elegant\Model\Collection
     */
    public function get($columns = array('*'))
    {

        // First we'll add the proper select columns onto the query so it is run with
        // the proper columns. Then, we will get the results and hydrate out pivot
        // models with the result of those columns as a separate model relation.
        $columns = $this->query->columns ? array() : $columns;

        $select = $this->getSelectColumns($columns);

        $records = $this->query->addSelect($select)->get();

        $this->hydratePivotRelation($records);

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded. This will solve the
        // n + 1 query problem for the developer and also increase performance.
        if (count($records) > 0) {
            $records = $this->query->eagerLoadRelations($records->toArray());
        }


        return \App::make('ni.elegant.model.collection', array($records));
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $records
     * @param  \Netinteractive\Elegant\Model\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $records, Collection $results, $relation)
    {
        $dictionary = $this->buildDictionary($results);

        // Once we have an array dictionary of child objects we can easily match the
        // children back to their parent using the dictionary and the keys on the
        // the parent models. Then we will return the hydrated models back out.
        foreach ($records as $record){
            $recordPkList = $record->getKey();

            foreach ($recordPkList AS $recordPk){
                if (isset($dictionary[$key = $recordPk])){
                    $collection = \App::make('ni.elegant.model.collection', array($dictionary[$key]));
                    $record->setRelated($relation, $collection);
                }
            }
        }

        return $records;
    }



    /**
     * Get the fully qualified foreign key for the relation.
     *
     * @return array
     */
    public function getForeignKey(Record $record=null)
    {
        $response = array();

        foreach ($this->foreignKey AS $fk){
            $response[] = $this->table.'.'.$fk;
        }

        return $response;
    }

    /**
     * Get the fully qualified "other key" for the relation.
     *
     * @return array
     */
    public function getOtherKey()
    {
        $response = array();

        foreach ($this->otherKey AS $fk){
            $response[] = $this->table.'.'.$fk;
        }

        return $response;
    }

    /**
     * Get the intermediate table for the relationship.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Create a new existing pivot model instance.
     *
     * @param  array  $attributes
     * @return \Netinteractive\Elegant\Relation\Pivot
     */
    public function newExistingPivot(array $attributes = array())
    {
        return $this->createNewPivot($attributes, true);
    }

    /**
     * Create a new pivot model instance.
     *
     * @param  array  $attributes
     * @param  bool   $exists
     * @return \Netinteractive\Elegant\Relation\Pivot
     */
    public function createNewPivot(array $attributes = array(), $exists = false)
    {
        $pivot = $this->newPivot($this->parent, $attributes, $this->table, $exists);

        return $pivot->setPivotKeys($this->foreignKey, $this->otherKey);
    }

    /**
     * Set the join clause for the relation query.
     *
     * @param  \Netinteractive\Elegant\Model\Query\Builder|null
     * @return $this
     */
    protected function setJoin($query = null)
    {
        $query = $query ?: $this->query;

        // We need to join to the intermediate table on the related model's primary
        // key column with the intermediate table's foreign key for the related
        // model instance. Then we can set the "where" for the parent models.
        $baseTable = $this->related->getBlueprint()->getStorageName();

        $keys = $this->related->getBlueprint()->getPrimaryKey();
        $otherKeys = $this->getOtherKey();


        $query->join($this->table, function($join) use($keys, $baseTable, $otherKeys){
            foreach ($keys AS $index=>$key){
                if (isSet($otherKeys[$index])){
                    $key = $baseTable.'.'.$key;
                    $join->on($key, '=', $otherKeys[$index]);
                }
            }
        });

        return $this;
    }


    /**
     * Set the where clause for the relation query.
     *
     * @return $this
     */
    protected function setWhere()
    {
        $fkList = $this->getForeignKey();
        $pkList = $this->parent->getKey();

        foreach ($fkList AS $index=>$fk){
            $this->query->where($fk, '=', $pkList[$index]);
        }

        return $this;
    }


    /**
     * Set the select clause for the relation query.
     *
     * @param  array  $columns
     * @return \Netinteractive\Elegant\Relation\BelongsToMany
     */
    protected function getSelectColumns(array $columns = array('*'))
    {
        if ($columns == array('*'))
        {
            $columns = array($this->related->getBlueprint()->getStorageName().'.*');
        }

        return array_merge($columns, $this->getAliasedPivotColumns());
    }

    /**
     * Get the pivot columns for the relation.
     *
     * @return array
     */
    protected function getAliasedPivotColumns()
    {
        $defaults = array_merge($this->foreignKey, $this->otherKey);

        // We need to alias all of the pivot columns with the "pivot_" prefix so we
        // can easily extract them out of the models and put them into the pivot
        // relationships when they are retrieved and hydrated into the models.
        $columns = array();

        foreach (array_merge($defaults, $this->pivotColumns) as $column){
            $columns[] = $this->table.'.'.$column.' as pivot_'.$column;
        }

        return array_unique($columns);
    }


    /**
     * Hydrate the pivot table relationship on the records.
     *
     * @param  \Netinteractive\Elegant\Model\Collection  $records
     * @return void
     */
    protected function hydratePivotRelation(Collection $records)
    {
        // To hydrate the pivot relationship, we will just gather the pivot attributes
        // and create a new Pivot model, which is basically a dynamic model that we
        // will set the attributes, table, and connections on so it they be used.
        foreach ($records as $record){
            $pivot = $this->newExistingPivot($this->cleanPivotAttributes($record));

            $record->setRelated('pivot', $pivot);
        }
    }

    /**
     * Get the pivot attributes from a record.
     *
     * @param  \Netinteractive\Elegant\Model\Record  $record
     * @return array
     */
    protected function cleanPivotAttributes(Record $record)
    {
        $values = array();

        foreach ($record->getAttributes() as $key => $value){
            // To get the pivots attributes we will just take any of the attributes which
            // begin with "pivot_" and add those to this arrays, as well as unsetting
            // them from the parent's models since they exist in a different table.
            if (strpos($key, 'pivot_') === 0){
                $values[substr($key, 6)] = $value;
                unset($record->$key);
            }
        }

        return $values;
    }

    /**
     * Build record dictionary keyed by the relation's foreign key.
     *
     * @param  \Netinteractive\Elegant\Model\Collection  $results
     * @return array
     */
    protected function buildDictionary(Collection $results)
    {
        $fkList = $this->foreignKey;

        // First we will build a dictionary of child records keyed by the foreign key
        // of the relation so that we will easily and quickly match them to their
        // parents without having a possibly slow inner loops for every models.
        $dictionary = array();

        foreach ($results as $result){
           foreach ($fkList AS $fk){
               $dictionary[$result->pivot->$fk][] = $result;
           }
        }


        return $dictionary;
    }


}
