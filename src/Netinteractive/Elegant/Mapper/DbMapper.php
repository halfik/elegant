<?php namespace Netinteractive\Elegant\Mapper;

use Illuminate\Database\ConnectionInterface;
use Netinteractive\Elegant\Exception\PrimaryKeyException;
use Netinteractive\Elegant\Model\Collection;

use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Model\Query\Builder;

use Netinteractive\Elegant\Helper;


/**
 * Class DbMapper
 * @package Netinteractive\Elegant\Mapper
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
     * @var \Netinteractive\Elegant\Model\Record
     */
    protected $emptyRecord = null;

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
     * Informs mapper to work with specified row class
     * @param string $recordClass
     * @return $this
     */
    public function setRecordClass($recordClass)
    {
        $this->emptyRecord = \App::make($recordClass);
        $this->query = $this->getNewQuery();

        #we check if there is registered db relationship translator and we pass QueryBuilder
        if ($this->emptyRecord ->getBlueprint()->getRelationManager()->hasTranslator('db')){
            $this->emptyRecord->getBlueprint()->getRelationManager()->getTranslator('db')->setQuery($this->getNewQuery());
        }

        return $this;
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
     * Delete record
     *
     * @param \Netinteractive\Elegant\Model\Record $record
     * @return int
     */
    public function delete(Record $record)
    {
        \Event::fire('ni.elegant.mapper.deleting.'.\classDotNotation($record), $record);

        $query  = $this->getNewQuery();

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

        \Event::fire('ni.elegant.mapper.deleted.'.\classDotNotation($record), $record);

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
     * Saves record to database
     *
     * @param \Netinteractive\Elegant\Model\Record $record
     * @param bool $touchRelated
     * @return $this
     */
    public function save(Record $record, $touchRelated = false)
    {
        #we want to check if anything has changed with record only when we are updating
        #if we do this for new record it won't let us to read record from one database and save it to another one
        if ($record->isNew()){
            $this->performInsert($record, $touchRelated);
        }
        else{
            $this->performUpdate($record ,$touchRelated);
        }

        return $this;
    }

    /**
     * Insert record
     *
     * @param \Netinteractive\Elegant\Model\Record $record
     * @param bool $touchRelated
     * @return $this
     */
    protected function performInsert(Record $record, $touchRelated = false)
    {
        $dirty = $record->getDirty();

        #check if anything has changed and if we don't have to touch related
        if (count($dirty) == 0 && $touchRelated === false){
            return $this;
        }

        if (count($dirty) > 0){
            #we prepare database query object
            $query = $this->getQuery();
            $query->from($record->getBlueprint()->getStorageName());

            #we check if record has created_at and updated_at fields, if so we allow record to set proper values for this fields
            if ($record->getBlueprint()->hasTimestamps()){
                $record->updateTimestamps();
            }

            #here we prepare obj that will be passed to mapper events
            $obj = new \stdClass();
            $obj->data = $record->getAttributes();
            $obj->record = $record;

            \Event::fire('ni.elegant.mapper.saving.'.\classDotNotation($record), $record);

            \Event::fire('ni.elegant.mapper.before.save', $obj);

            \Event::fire('ni.elegant.mapper.creating.'.\classDotNotation($record), $record);

            #we override data we are going to insert
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

            \Event::fire('ni.elegant.mapper.created.'.\classDotNotation($record), $record);
        }

        #we touch related records
        if ($touchRelated === true && $record->hasRelated()){
           $this->touchRelated($record);
        }
    }

    /**
     * Update record
     *
     * @param \Netinteractive\Elegant\Model\Record $record
     * @param bool $touchRelated
     * @return $this
     */
    protected function performUpdate(Record $record, $touchRelated = false)
    {
        #we prepare database query object
        $query = $this->getQuery();
        $query->from($record->getBlueprint()->getStorageName());

        #we check if record has created_at and updated_at fields, if so we allow record to set proper values for this fields
        if ($record->getBlueprint()->hasTimestamps()){
            $record->updateTimestamps();
        }

        #here we prepare obj that will be passed to mapper events
        $obj = new \stdClass();
        $obj->data = $record->getDirty();
        $obj->record = $record;

        \Event::fire('ni.elegant.mapper.saving.'.\classDotNotation($record), $record);

        \Event::fire('ni.elegant.mapper.before.save', $obj);

        #we override data we are going to update
        $dirty =  $obj->data;

        #we always should validate all data not only that actually was changed
        $record->validate(array_merge($record->getAttributes(), $dirty));

        \Event::fire('ni.elegant.mapper.updating.'.\classDotNotation($record), $record);

        $this->setKeysForSaveQuery($query, $record)->update( $dirty );

        \Event::fire('ni.elegant.mapper.updated.'.\classDotNotation($record), $record);

        #we touch related records
        if ($touchRelated === true && $record->hasRelated()){
            $this->touchRelated($record);
        }
    }

    /**
     * Touches related records
     * @param \Netinteractive\Elegant\Model\Record $record
     * @return $this
     */
    public function touchRelated(Record $record)
    {
        foreach ($record->getRelated() AS $records){
            if ($records instanceof Record){
                \Event::fire('ni.elegant.mapper.touching.'.\classDotNotation($record), $record);
                $this->save($records, true);
                \Event::fire('ni.elegant.mapper.touched.'.\classDotNotation($record), $record);
            }
            elseif ($records instanceof Collection){
                $this->saveMany($records, true);
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
            if ($touchRelated === true){
                \Event::fire('ni.elegant.mapper.touching.'.\classDotNotation($record), $record);
            }
            $this->save($record, $touchRelated);

            if ($touchRelated === true){
                \Event::fire('ni.elegant.mapper.touched.'.\classDotNotation($record), $record);
            }
        }
        return $this;
    }


    /**
     * Finds single record
     *
     * @param $ids
     * @param array $columns
     * @return \Netinteractive\Elegant\Model\Record|null
     */
    public function find($ids, array $columns=array('*'))
    {
        $record = null;

        if (!is_array($ids)){
            $ids = array('id' => $ids);
        }

        $this->checkPrimaryKey($ids);

        $q = $this->getNewQuery();


        $bluePrint =  $this->emptyRecord->getBlueprint();
        if ($bluePrint->softDelete()){
            $q->whereNull($bluePrint->getStorageName().'.'.$bluePrint->getDeletedAt());
        }

        $data = $q->find($ids, $columns);

        if (!is_array($data) && $data instanceof \Illuminate\Contracts\Support\Arrayable){
            $data = $data->toArray();
        }

        if (!empty($data)){
            $record = $this->createRecord((array) $data);
            $record->exists = true;
        }


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
        $query = $this->getNewQuery();

        #if we have empty columns, we take all from table
        if (empty($columns)) {
            $columns[] = $this->emptyRecord->getBlueprint()->getStorageName(). '.*';
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
        \Event::fire('ni.elegant.mapper.search.'.\classDotNotation($this->emptyRecord), $query);

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
        #we pass record  to query builder
        if ($this->query->getRecord() == null){
            $this->query->setRecord($this->emptyRecord);

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
     * creates new query builder object
     * @return mixed
     */
    public function getNewQuery()
    {
        $q = \App::make('ni.elegant.model.query.builder', array($this->connection,  $this->connection->getQueryGrammar(), $this->connection->getPostProcessor()));

        $q->from($this->emptyRecord->getBlueprint()->getStorageName());
        $q->setRecord($this->emptyRecord);

        return $q;
    }


    /**
     * Method checks if blueprint primary keys are same with input array keys
     *
     * @param int|array $ids
     * @throws \Netinteractive\Elegant\Exception\PrimaryKeyException
     */
    protected function checkPrimaryKey($ids)
    {
        $primaryKey = $this->emptyRecord->getBlueprint()->getPrimaryKey();
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


    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->query, $method), $parameters);
    }
} 