<?php
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 06.03.15
 * Time: 10:58
 */

namespace Netinteractive\Elegant\Model\Mapper;
use Netinteractive\Elegant\Exception\PrimaryKeyException;
use Netinteractive\Elegant\Model\Collection;
use Netinteractive\Elegant\Model\MapperInterface;
use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Query\Builder;

use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class DbMapper
 * @package Netinteractive\Elegant\Model\Mapper
 */
abstract class DbMapper implements MapperInterface
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * Record class name
     * @var string
     */
    protected $recordName;


    /**
     * Create a new db mapper
     *
     * @return void
     */
    public function __construct($connection=null)
    {
        $this->connection = \App('db')->connection($connection);
    }


    /**
     * Create new model
     *
     * @param array $data
     * @return Netinteractive\Elegant\Model\Record
     */
    public function createRecord(array $data = array())
    {
        $record = \App::make($this->getRecordClass());
        $record->fill($data);
        $record->exists = false;

        return $record;
    }

    /**
     * Returns name of moles class
     *
     * @return string
     */
    public function getRecordClass()
    {
        return $this->recordName;
    }

    /**
     * Returns model Blueprint
     * @return \Netinteractive\Elegant\Model\Blueprint
     */
    public function getBlueprint()
    {
        return $this->createRecord()->getBlueprint();
    }


    /**
     * Delete record
     *
     * @param integer $id
     * @return int
     */
    public function delete($ids)
    {
        $this->checkPrimaryKey($ids);

        return $this->getQuery()->from($this->getBlueprint()->getTable())->delete($ids);
    }

    /**
     * Save model
     *
     * @param Netinteractive\Elegant\Model\Record $model
     * @return $this
     */
    public function save(Record $model)
    {
        $dirty = $model->getDirty();

        #check if anything has changed
        if (count($dirty) == 0){
            return $this;
        }

        $model->validate($model->getDirty());

        $query = $this->getQuery();
        $query->from($model->getBlueprint()->getTable());

        #check if we are editing or creating
        if (!$model->exists){
            $attributes = $model->getAttributes();

            #check if we have autoincrementing on PK
            if ($model->getBlueprint()->incrementingPk){
                $primaryKey = $model->getBlueprint()->incrementingPk;

                $id = $query->insertGetId($attributes, $primaryKey);

                $model->setAttribute($primaryKey, $id);;
            }else{
                $query->insert($attributes);
            }
        }
        else{
            $this->setKeysForSaveQuery($query, $model)->update($dirty);
        }

        $model->syncOriginal();

        return $this;
    }


    /**
     * Find one model
     *
     * @param $ids
     * @param array $columns
     * @return Netinteractive\Elegant\Model\Record
     */
    public function find($ids, array $columns=array('*'))
    {
        $this->checkPrimaryKey($ids);

        $data = $this->getQuery()->find($ids, $columns);

        $model = $this->createRecord((array) $data);
        $model->exists = true;

        return $model;
    }

    /**
     * Find collection of models
     *
     * @param array $params
     * @param array $columns
     * @param string $operator
     * @param bool $defaultJoin
     * @return Collection
     */
    public function findMany(array $params, $columns = array('*'), $operator = 'and', $defaultJoin = true)
    {
        return \App::make('ElegantCollection', array($this->search($params, $columns, $operator, $defaultJoin)->get()));
    }


    /**
     * Search
     *
     * @param $input
     * @param array $columns
     * @param string $operator
     * @param bool $defaultJoin
     * @return mixed
     */
    public function search($input, $columns = array(), $operator = 'and', $defaultJoin = true)
    {
        $query = $this->getQuery();

        #if we have empty columns, we take all from table
        if (empty($columns)) {
            $columns[] = $this->getBlueprint()->getTable(). '.*';
        }
        $query->select($columns);

        #here we clean up incoming input from empty values
        foreach ($input as $modelName => $fields) {
            if (is_array($fields)) {
                foreach ($fields AS $name => $val) {
                    if (is_array($val) && in_array('null', $val)) {
                        unset($input[$modelName][$name]);
                    }
                    if (empty($input[$modelName][$name])) {
                        unset($input[$modelName][$name]);
                    }
                }
            }
        }

        #we add to search query default join defined my developer in searchJoins method
        if ($defaultJoin) {
            $query = $this->searchJoins($query);
        }

        #we wrap all ours search wheres  becouse of otherse wheres that can be add later (or where added before)
        $query->where(function ($query) use ($input, $operator) {
            foreach ($input AS $modelName => $fields) {

                if (!empty($fields) && is_array($fields)) {
                    $model = \App::make($modelName);
                    foreach ($fields AS $field => $value) {
                        $query = $this->queryFieldSearch($model, $field, $value, $query, $operator);
                    }
                }
            }
        });

        return $query;
    }

    /**
     * Adds where to query object based on searchable defined in blueprint
     *
     * @param string $fieldKey
     * @param string $keyword
     * @param Query $q
     * @param string $operator
     * @return mixed
     */
    public function queryFieldSearch(Record $model, $fieldKey, $keyword, $q, $operator = 'or')
    {
        $searchableFields = $model->getBlueprint()->getSearchableFields();

        if (isSet($searchableFields[$fieldKey])){
            $searchable = $searchableFields[$fieldKey]['searchable'];
            $searchable($q, $keyword, $operator);
        }

        return $q;
    }


    /**
     * @return mixed`
     */
    public function getQuery()
    {
        $query =  \App::make('Builder', array($this->connection,  $this->connection->getQueryGrammar(), $this->connection->getPostProcessor()));
        $query->from($this->getBlueprint()->getTable());

        return $query;
    }


    /**
     * Method checkcs if blueprint primary keys are same with input array keys
     *
     * @param int|array $ids
     * @throws \Netinteractive\Elegant\Exception\PrimaryKeyException
     */
    protected function checkPrimaryKey($ids)
    {
        $primaryKey = $this->getBlueprint()->getPrimaryKey();
        if (count($primaryKey) > 1){
            if ($primaryKey != array_keys($ids)){
                throw new PrimaryKeyException();
            }
        }
    }

    /**
     * Set the keys for a save update query.
     * @param Builder $query
     * @param Model $model
     * @return Builder
     *
     */
    protected function setKeysForSaveQuery(Builder $query, Model $model)
    {
        $pk = $model->getBlueprint()->getPrimaryKey();

        foreach ($pk AS $part){
            $query->where($part, '=', $model->$part);
        }


        return $query;
    }


    /**
     * Method for serach query modifications (method to be overwriten)
     * @param Builder $query
     * @return mixed
     */
    protected function searchJoins(Builder $query)
    {
        return $query;
    }


    #RELATIONS

    /**
     * Eagerly load the relationship on a set of models.
     *
     * @param  array     $models
     * @param  string    $name
     * @param  \Closure  $constraints
     * @return array
     */
    protected function loadRelation(array $models, $name, Closure $constraints)
    {
        // First we will "back up" the existing where conditions on the query so we can
        // add our eager constraints. Then we will merge the wheres that were on the
        // query back to it in order that any where conditions might be specified.
        $relation = $this->getRelation($name);

        $relation->addEagerConstraints($models);

        call_user_func($constraints, $relation);

        $models = $relation->initRelation($models, $name);

        // Once we have the results, we just match those back up to their parent models
        // using the relationship instance. Then we just return the finished arrays
        // of models which have been eagerly hydrated and are readied for return.
        $results = $relation->getEager();

        return $relation->match($models, $results, $name);
    }

    /**
     * Get the relation instance for the given relation name.
     *
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function getRelation($relation)
    {
        // We want to run a relationship query without any constrains so that we will
        // not have to remove these where clauses manually which gets really hacky
        // and is error prone while we remove the developer's own where clauses.
        $query = Relation::noConstraints(function() use ($relation)
        {
            return $this->createRelation($relation);
        });



        return $query;
    }

    public function createRelation($relation)
    {

    }
} 