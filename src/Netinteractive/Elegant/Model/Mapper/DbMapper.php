<?php namespace Netinteractive\Elegant\Model\Mapper;

use Illuminate\Database\ConnectionInterface;
use Netinteractive\Elegant\Exception\PrimaryKeyException;
use Netinteractive\Elegant\Exception\RecordNotFoundException;
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
     * @param string $recordClass
     * @param \Illuminate\Database\ConnectionInterface $connection
     * @return void
     */
    public function __construct($recordClass, ConnectionInterface $connection=null)
    {
        if (!$connection){
            $this->connection = \App::make('db')->connection($connection);
        }else{
            $this->connection = $connection;
        }

        $this->setRecordClass($recordClass);
    }


    /**
     * Create new record
     *
     * @param array $data
     * @param bool $exists
     *
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function createRecord(array $data = array(), $exists = false)
    {
        $record = clone $this->emptyRecord;

        $record->fill($data);
        $record->syncOriginal();
        $record->exists = $exists;

        return $record;
    }


    /**
     * Create Collection of records
     *
     * @param array $data
     * @return \Netinteractive\Elegant\Model\Collection
     */
    public function createMany(array $data=array())
    {
        $collection = \App::make('ni.elegant.model.collection', array());

        foreach ($data AS $row){
            $collection->add($this->createRecord($row));
        }

        return $collection;
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
     * @param \Netinteractive\Elegant\Model\Record $record
     * @return int
     */
    public function delete(Record $record)
    {
        \Event::fire('ni.elegant.mapper.deleting.'.$this->getRecordClass(), $record);

        $query  = $this->getQuery()->from($this->getBlueprint()->getStorageName());

        $pkList = $record->getBlueprint()->getPrimaryKey();

        foreach ($pkList AS $pk){
            $query->where($pk, $record->$pk);
        }

        if ($record->getBlueprint()->softDelete()){
            $result = $this->softDelete($record, $query);
        }else{
            $result =  $query->delete();
        }


        #it dosn't we just deleted it
        $record->exists = false;

        \Event::fire('ni.elegant.mapper.deleted.'.$this->getRecordClass(), $record);

        return $result;
    }


    /**
     * @param \Netinteractive\Elegant\Model\Record $record
     * @param \Netinteractive\Elegant\Db\Query\Builder $query
     * @return mixed
     */
    protected function softDelete(Record $record, Builder $query)
    {
        $record->setDeletedAt( $time = $record->freshTimestamp() );

        return $query->update(array($record->getBlueprint()->getDeletedAt() => $record->fromDateTime($time, $this->getDateFormat())));
    }


    /**
     * Save model
     *
     * @param \Netinteractive\Elegant\Model\Record $record
     * @param bool $touchRelated
     * @return $this
     */
    public function save(Record $record, $touchRelated = false)
    {
        #we want to check if anything has changed with record only when we are updating
        #if we do this for new record it won't let us to read record from one database and save it to another one
        if ($record->exists == true){
            $dirty = $record->getDirty();

            #check if anything has changed and if we dont have to touch related
            if (count($dirty) == 0 && $touchRelated === false){
                return $this;
            }
        }
        
        if (count($dirty) > 0){
            #we prepare database query object
            $query = $this->getQuery();
            $query->from($record->getBlueprint()->getStorageName());

            #here we prepare obj that will be passed to mapper events
            $obj = new \stdClass();
            $obj->data = array();
            $obj->record = $record;

            \Event::fire('ni.elegant.mapper.saving.'.$this->getRecordClass(), $record);

            #we check if record has created_at and updated_at fields, if so we allow record to set proper values for this fields
            if ($record->getBlueprint()->hasTimestamps()){
                $record->updateTimestamps();
            }

            #check if we are editing or creating
            if (!$record->exists){
                $obj->data = $record->getAttributes();

                \Event::fire('ni.elegant.mapper.before.save', $obj);

                \Event::fire('ni.elegant.mapper.creating.'.$this->getRecordClass(), $record);

                $attributes = $obj->data;

                #we always should validate all data not only that actually was changed
                $record->validate($attributes);

                #check if we have autoincrementing on PK
                if ($record->getBlueprint()->incrementingPk){
                    $primaryKey = $record->getBlueprint()->incrementingPk;

                    $id = $query->insertGetId($attributes, $primaryKey);

                    $record->setAttribute($primaryKey, $id);
                }else{
                    $query->insert($attributes);
                }

                \Event::fire('ni.elegant.mapper.created.'.$this->getRecordClass(), $record);
            }
            else{
                $obj->data = $record->getDirty();

                \Event::fire('ni.elegant.mapper.before.save', $obj);

                $dirty =  $obj->data;

                #we always should validate all data not only that actually was changed
                $record->validate(array_merge($record->getAttributes(), $dirty));

                \Event::fire('ni.elegant.mapper.updating.'.$this->getRecordClass(), $record);

                $this->setKeysForSaveQuery($query, $record)->update( $dirty );

                \Event::fire('ni.elegant.mapper.updated.'.$this->getRecordClass(), $record);
            }

            $record->syncOriginal();

            \Event::fire('ni.elegant.mapper.saved.'.$this->getRecordClass(), $record);
        }



        #we touch related records
        if ($touchRelated === true && $record->hasRelated()){
            foreach ($record->getRelated() AS $records){
                if ($records instanceof Record){
                    $this->save($records, true);
                }
                elseif ($record instanceof Collection){
                    $this->saveMany($records, true);
                }
            }
        }

        return $this;
    }



    /**
     * Saves collection of records
     *
     * @param \Netinteractive\Elegant\Model\Collection $records
     * @param bool $touchRelated
     * @return $this
     */
    public function saveMany(Collection $records, $touchRelated = false)
    {
        foreach ($records AS $record){
            $this->save($record, $touchRelated);
        }
        return $this;
    }


    /**
     * Finds single record
     *
     * @param $ids
     * @param array $columns
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function find($ids, array $columns=array('*'))
    {
        $this->checkPrimaryKey($ids);

        $data = $this->getQuery()->find($ids, $columns);

        if (!is_array($data) && $data instanceof \Illuminate\Contracts\Support\Arrayable){
            $data = $data->toArray();
        }

        $record = $this->createRecord((array) $data);
        $record->exists = true;

        return $record;
    }

    /**
     * Find collection of records
     *
     * @param array $params
     * @param array $columns
     * @param string $operator
     * @param bool $defaultJoin
     * @return \Netinteractive\Elegant\Model\Collection
     */
    public function findMany(array $params, $columns = array('*'), $operator = 'and', $defaultJoin = true)
    {
        return \App::make('ni.elegant.model.collection', array($this->search($params, $columns, $operator, $defaultJoin)->get()));
    }


    /**
     * Execute the query and get the first result.
     *
     * @param  array  $columns
     * @return \Netinteractive\Elegant\Model\Record|static|null
     */
    public function first(array $columns = array('*'))
    {
        return $this->getQuery()->take(1)->get($columns)->first();
    }


    /**
     * Search
     *
     * @param $input
     * @param array $columns
     * @param string $operator
     * @return mixed
     */
    public function search($input, $columns = array(), $operator = 'and')
    {
        $query = $this->getQuery();

        #if we have empty columns, we take all from table
        if (empty($columns)) {
            $columns[] = $this->getBlueprint()->getStorageName(). '.*';
        }
        $query->select($columns);

        #here we clean up incoming input from empty values
        foreach ($input as $recordName => $fields) {
            if (is_array($fields)) {
                foreach ($fields AS $name => $val) {
                    if (is_array($val) && in_array('null', $val)) {
                        unset($input[$recordName][$name]);
                    }
                    if (empty($input[$recordName][$name])) {
                        unset($input[$recordName][$name]);
                    }
                }
            }
        }

        #we wrap all ours search wheres  because of others wheres that can be add later (or where added before)
        $query->where(function ($query) use ($input, $operator) {
            foreach ($input AS $recordName => $fields) {
                if (!empty($fields) && is_array($fields)) {
                    $record = \App::make($recordName);
                    foreach ($fields AS $field => $value) {
                        $query = $this->queryFieldSearch($record, $field, $value, $query, $operator);
                    }
                }
            }
        });

        #we add to search query default join defined my developer in searchJoins method
        \Event::fire('ni.elegant.mapper.search.'.$this->getRecordClass(), $query);

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
    public function queryFieldSearch(Record $record, $fieldKey, $keyword, $q, $operator = 'or')
    {
        $searchableFields = $record->getBlueprint()->getSearchableFields();

        #search translator
        $translator = \App::make('ni.elegant.search.db.translator');

        if (isSet($searchableFields[$fieldKey])){
            $searchable = $translator->translate($fieldKey, $searchableFields[$fieldKey]['searchable']);
            $searchable($q, $keyword, $operator);
        }

        return $q;
    }


    /**
     * Returns query builder object
     * @return \Netinteractive\Elegant\Db\Query\Builder
     */
    public function getQuery()
    {
        #query builder object init
        if ($this->query == null){
            $this->query = \App::make('ni.elegant.model.query.builder', array($this->connection,  $this->connection->getQueryGrammar(), $this->connection->getPostProcessor()));
            $this->query->from($this->getBlueprint()->getStorageName());
        }

        #we pass record  to query builder
        if ($this->query->getRecord() == null){
            $this->query->setRecord($this->createRecord());

            # if record is softDelete type we ensure that query builder will take only none deleted records
            if ($this->query->getRecord()->getBlueprint()->softDelete() === true){
                $deletedAtColumns =  $this->query->getFrom().'.'. $this->query->getRecord()->getBlueprint()->getDeletedAt();
                $this->query->whereNull($deletedAtColumns);
            }
        }

        $this->query->setConnection($this->connection);

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
     * Returns database connection
     * @return \Illuminate\Database\Connection|ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }



    /**
     * Sets database connection
     *
     * @param \Illuminate\Database\ConnectionInterface\ConnectionInterface $connection
     * @return $this
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;

        if ($this->query){
            $this->query->setConnection($connection);
        }
        return $this;
    }


    /**
     * Set the keys for a save update query.
     * @param \Netinteractive\Elegant\Model\Query\Builder $query
     * @param \Netinteractive\Elegant\Model\Record $record
     * @return \Netinteractive\Elegant\Model\Query\Builder
     *
     */
    protected function setKeysForSaveQuery(Builder $query, Record $record)
    {
        $pk = $record->getBlueprint()->getPrimaryKey();

        foreach ($pk AS $part){
            $query->where($query->getFrom().'.'.$part, '=', $record->$part);
        }

        return $query;
    }


    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    protected function getDateFormat()
    {
        return $this->getConnection()->getQueryGrammar()->getDateFormat();
    }




    /**
     * Adding relation to the query object
     *
     * @param  array|string  $relations
     * @return \Netinteractive\Elegant\Db\Query\Builder
     */
    public function with($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }

        return $this->getQuery()->with($relations);
    }
} 