<?php namespace Netinteractive\Elegant\Model\Mapper;

use Netinteractive\Elegant\Exception\PrimaryKeyException;
use Netinteractive\Elegant\Model\Collection;
use Netinteractive\Elegant\Model\MapperInterface;
use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Model\Query\Builder;


/**
 * Class DbMapper
 * @package Netinteractive\Elegant\Model\Mapper
 */
class DbMapper implements MapperInterface
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
     * @var \Netinteractive\Elegant\Model\Record
     */
    protected $emptyRecord = null;

    /**
     * @var \Netinteractive\Elegant\Model\Blueprint
     */
    protected $blueprint = null;

    /**
     * @var \Netinteractive\Elegant\Model\Query\Builder
     */
    protected $query = null;


    /**
     * Create a new db mapper
     *
     * @return void
     */
    public function __construct($recordClass, $connection=null)
    {
        $this->connection = \App::make('db')->connection($connection);
        $this->setRecordClass($recordClass);
    }


    /**
     * Create new model
     *
     * @param array $data
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function createRecord(array $data = array())
    {
        $record = clone $this->emptyRecord;

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
     * Sets record class name
     * @param string $name
     * @return $this
     */
    public function setRecordClass($name)
    {
        $this->recordName = $name;

        $this->emptyRecord = \App::make($this->getRecordClass());
        $this->blueprint = $this->emptyRecord->getBlueprint();

        #we check if there is registered db relationship translator and we pass QueryBuilder
        if ($this->emptyRecord ->getBlueprint()->getRelationManager()->hasTranslator('db')){
            $this->emptyRecord->getBlueprint()->getRelationManager()->getTranslator('db')->setQuery($this->getQuery());
        }

        $this->query = null;

        return $this;
    }


    /**
     * Returns model Blueprint
     * @return \Netinteractive\Elegant\Model\Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
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

        return $this->getQuery()->from($this->getBlueprint()->getStorageName())->delete($ids);
    }

    /**
     * Save model
     *
     * @param \Netinteractive\Elegant\Model\Record $record
     * @return $this
     */
    public function save(Record $record)
    {
        $dirty = $record->getDirty();

        #check if anything has changed
        if (count($dirty) == 0){
            return $this;
        }

        $record->validate($record->getDirty());

        $query = $this->getQuery();
        $query->from($record->getBlueprint()->getStorageName());

        #check if we are editing or creating
        if (!$record->exists){
            $attributes = $record->getAttributes();

            #check if we have autoincrementing on PK
            if ($record->getBlueprint()->incrementingPk){
                $primaryKey = $record->getBlueprint()->incrementingPk;

                $id = $query->insertGetId($attributes, $primaryKey);

                $record->setAttribute($primaryKey, $id);;
            }else{
                $query->insert($attributes);
            }
        }
        else{
            $this->setKeysForSaveQuery($query, $record)->update($dirty);
        }

        $record->syncOriginal();

        return $this;
    }


    /**
     * Find one model
     *
     * @param $ids
     * @param array $columns
     * @return \Netinteractive\Elegant\Model\Record
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
            $columns[] = $this->getBlueprint()->getStorageName(). '.*';
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

        #we wrap all ours search wheres  because of others wheres that can be add later (or where added before)
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

        #search translator
        $translator = \App::make('ElegantSearchDbTranslator');

        if (isSet($searchableFields[$fieldKey])){
            $searchable = $translator->translate($fieldKey, $searchableFields[$fieldKey]['searchable']);
            $searchable($q, $keyword, $operator);
        }

        return $q;
    }


    /**
     * @return \Netinteractive\Elegant\Db\Query\Builder
     */
    public function getQuery()
    {
        #query builder object init
        if ($this->query == null){
            $this->query = \App::make('ElegantModelQueryBuilder', array($this->connection,  $this->connection->getQueryGrammar(), $this->connection->getPostProcessor()));
            $this->query->from($this->getBlueprint()->getStorageName());
        }

        #we pass record  to query builder
        if ($this->query->getRecord() == null){
            $this->query->setRecord($this->createRecord());
        }

        return clone $this->query;
    }


    /**
     * Method checks if blueprint primary keys are same with input array keys
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
     * @param Record $model
     * @return Builder
     *
     */
    protected function setKeysForSaveQuery(Builder $query, Record $model)
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


    /**
     * Adding relation to the query object
     *
     * @param  array|string  $relations
     * @return \Netinteractive\Elegant\Db\Query\Builder
     */
    public function with($relations)
    {
        if (is_string($relations)) $relations = func_get_args();

        return $this->getQuery()->with($relations);
    }
} 