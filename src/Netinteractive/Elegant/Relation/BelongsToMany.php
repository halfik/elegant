<?php

namespace Netinteractive\Elegant\Relation;

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
     * Get a new plain query builder for the pivot table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function newPivotStatement()
    {
        return $this->query->getQuery()->newQuery()->from($this->table);
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
     * @param  \Netinteractive\Elegant\Model\Collection   $records
     * @return void
     */
    public function addEagerConstraints(Collection $records)
    {
        $fkList = $this->getForeignKey();
        $keys = $this->getKeys($records);

        $this->getQuery()->from($this->getRelated()->getBlueprint()->getStorageName());

        foreach ($fkList AS $index=>$fk){
            $this->getQuery()->whereIn($this->getTable().'.'.$fk, array_shift($keys));
        }
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  \Netinteractive\Elegant\Model\Collection   $records
     * @param  string  $relation
     * @return array
     */
    public function initRelation(Collection $records, $relation)
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
        $columns = $this->getQuery()->columns ? array() : $columns;

        $select = $this->getSelectColumns($columns);

        $this->getQuery()->setRecord($this->getRelated());
        $records = $this->getQuery()->addSelect($select)->get();

        $this->hydratePivotRelation($records);

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded. This will solve the
        // n + 1 query problem for the developer and also increase performance.
        if (count($records) > 0) {
            $records = $this->getQuery()->eagerLoadRelations($records);
        }

        return \App::make('ni.elegant.model.collection', array($records));
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  \Netinteractive\Elegant\Model\Collection   $records
     * @param  \Netinteractive\Elegant\Model\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(Collection $records, Collection $results, $relation)
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
        return $this->foreignKey;
    }

    /**
     * Get the fully qualified foreign key of the relationship.
     *
     * @return array
     */
    public function getQualifiedForeignKey()
    {
        $fkList = $this->getForeignKey();
        $response = array();

        foreach ($fkList AS $fk){
            $response[] = $this->getTable().'.'.$fk;
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
        return $this->otherKey;
    }

    /**
     * Get the fully qualified other key of the relationship.
     *
     * @return string
     */
    public function getQualifiedOtherKey()
    {
        $okList = $this->getOtherKey();
        $response = array();

        foreach ($okList AS $fk){
            $response[] = $this->getTable().'.'.$fk;
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
        $pivot = $this->newPivot($this->getParent(), $attributes, $this->getTable(), $exists);

        return $pivot->setPivotKeys($this->getForeignKey(), $this->getOtherKey());
    }


    /**
     * Set the join clause for the relation query.
     *
     * @param  \Netinteractive\Elegant\Models\Query\Builder|null
     * @return $this
     */
    protected function setJoin($query = null)
    {
        $query = $query ?: $this->getQuery();

        // We need to join to the intermediate table on the related model's primary
        // key column with the intermediate table's foreign key for the related
        // model instance. Then we can set the "where" for the parent models.
        $baseTable = $this->getRelated()->getBlueprint()->getStorageName();

        $keys = $this->getRelated()->getBlueprint()->getPrimaryKey();
        $otherKeys = $this->getQualifiedOtherKey();


        $query->join($this->getTable(), function($join) use($keys, $baseTable, $otherKeys){

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
        $fkList = $this->getQualifiedForeignKey();
        $pkList = $this->getParent()->getKey();

        foreach ($fkList AS $index=>$fk){
            $this->getQuery()->where($fk, '=',array_shift($pkList));
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
        if ($columns == array('*')) {
            $columns = array($this->getRelated()->getBlueprint()->getStorageName().'.*');
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
        $defaults = array_merge($this->getForeignKey(), $this->getOtherKey());

        // We need to alias all of the pivot columns with the "pivot_" prefix so we
        // can easily extract them out of the models and put them into the pivot
        // relationships when they are retrieved and hydrated into the models.
        $columns = array();

        foreach (array_merge($defaults, $this->pivotColumns) as $column){
            $columns[] = $this->getTable().'.'.$column.' as '.Pivot::PREFIX.'_'.$column;
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

            $record->setRelated(Pivot::PREFIX, $pivot);
        }
    }

    /**
     * Clear and return the pivot attributes from a record.
     *
     * @param  \Netinteractive\Elegant\Model\Record  $record
     * @return array
     */
    protected function cleanPivotAttributes(Record $record)
    {
        $values = array();
        foreach ($record->toArray() as $key => $value){

            // To get the pivots attributes we will just take any of the attributes which
            // begin with "pivot_" and add those to this arrays, as well as unsetting
            // them from the parent's models since they exist in a different table.
            if (strpos($key, Pivot::PREFIX.'_') === 0){
                $values[substr($key, strlen(Pivot::PREFIX)+1)] = $value;
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
        $fkList = $this->getForeignKey();

        // First we will build a dictionary of child records keyed by the foreign key
        // of the relation so that we will easily and quickly match them to their
        // parents without having a possibly slow inner loops for every models.
        $dictionary = array();

        foreach ($results as $result){
            foreach ($fkList AS $fk){
               $dictionary[$result->{Pivot::PREFIX}->$fk][] = $result;
            }
        }


        return $dictionary;
    }


    /**
     * Create a data for pivot table
     *
     * @return array
     */
    public function createPivotData()
    {
        $data = array();
        $fkList = $this->getForeignKey();
        $pkList = $this->getOtherKey();

        $i = 0;
        $parentKeys = $this->parent->getKey();
        foreach ($parentKeys AS $val){
            if (isSet($fkList[$i])){
                $data[$fkList[$i]] = $val;
            }
            $i++;
        }

        $i = 0;
        $parentKeys = $this->related->getKey();
        foreach ($parentKeys AS $val){
            if (isSet($pkList[$i])){
                $data[$pkList[$i]] = $val;
            }
            $i++;
        }

        return $data;
    }


    /**
     * Attach a model to the parent.
     *
     * @param  mixed  $id
     * @param  array  $attributes
     * @param  bool   $touch
     * @return void
     */
   /* public function attach($id, array $attributes = array(), $touch = true)
    {
        if ($id instanceof Model) $id = $id->getKey();

        $query = $this->newPivotStatement();

        $query->insert($this->createAttachRecords((array) $id, $attributes));

        if ($touch) $this->touchIfTouching();
    }*/

    /**
     * Create an array of records to insert into the pivot table.
     *
     * @param  array  $ids
     * @param  array  $attributes
     * @return array
     */
   /* protected function createAttachRecords($ids, array $attributes)
    {
        $records = array();

        $timed = ($this->hasPivotColumn($this->createdAt()) || $this->hasPivotColumn($this->updatedAt()));

        // To create the attachment records, we will simply spin through the IDs given
        // and create a new record to insert for each ID. Each ID may actually be a
        // key in the array, with extra attributes to be placed in other columns.
        foreach ($ids as $key => $value)
        {
            $records[] = $this->attacher($key, $value, $attributes, $timed);
        }

        return $records;
    }*/

}
