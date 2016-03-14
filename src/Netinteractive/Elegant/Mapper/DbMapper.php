<?php namespace Netinteractive\Elegant\Mapper;

use Illuminate\Database\ConnectionInterface;
use Netinteractive\Elegant\Exception\PrimaryKeyException;
use Netinteractive\Elegant\Model\Collection;

use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Model\Query\Builder;

use Netinteractive\Elegant\Helper;
use Netinteractive\Elegant\Relation\BelongsToMany;
use Netinteractive\Elegant\Relation\HasOneOrMany;


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
        $record->setExists($exists);

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
        $record->setExists(false);

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
        $record->setDeletedAt( $time = $record->createTimestamp() );

        return $query->update(array($record->getBlueprint()->getDeletedAt() => $record->fromDateTime($time, $this->getDateFormat())));
    }


    /**
     * Saves record to database
     *
     * @param \Netinteractive\Elegant\Model\Record $record
     * @param bool $saveRelated
     * @return $this
     */
    public function save(Record $record, $saveRelated = false)
    {
        #we want to check if anything has changed with record only when we are updating
        #if we do this for new record it won't let us to read record from one database and save it to another one
        if ($record->isNew()){
            $this->performInsert($record, $saveRelated);
        }
        else{
            $this->performUpdate($record ,$saveRelated);
        }

        $record->syncOriginal();


        return $this;
    }

    /**
     * Insert record
     *
     * @param \Netinteractive\Elegant\Model\Record $record
     * @param bool $saveRelated
     * @return $this
     */
    protected function performInsert(Record $record, $saveRelated = false)
    {
        $dirty = $record->getAttributes();

        #check if anything has changed and if we don't have to touch related
        if (count($dirty) == 0 && $saveRelated === false){
            return $this;
        }

        if (count($dirty) > 0){
            #we prepare database query object
            $query = $this->getNewQuery();
            $query->from($record->getBlueprint()->getStorageName());

            #we check if record has created_at and updated_at fields, if so we allow record to set proper values for this fields
            if ($record->getBlueprint()->hasTimestamps()){
                $record->updateTimestamps(true, true);
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
                #we have to clean probably null pk
                unset($attributes[$primaryKey]);

                $id = $query->insertGetId($attributes, $primaryKey);

                $record->setAttribute($primaryKey, $id);
            }else{
                $query->insert($attributes);
            }

            \Event::fire('ni.elegant.mapper.created.'.\classDotNotation($record), $record);
        }

        #we touch related records
        if ($saveRelated === true && $record->hasRelated()){
           $this->touchRelated($record);
        }

        $record->setExists(true);
    }

    /**
     * Update record
     *
     * @param \Netinteractive\Elegant\Model\Record $record
     * @param bool $saveRelated
     * @return $this
     */
    protected function performUpdate(Record $record, $saveRelated = false)
    {
        #we prepare database query object
        $query = $this->getNewQuery();
        $query->from($record->getBlueprint()->getStorageName());

        #we check if record has created_at and updated_at fields, if so we allow record to set proper values for this fields
        if ($record->getBlueprint()->hasTimestamps()){
            $record->updateTimestamps(false, true);
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
        if ($saveRelated === true && $record->hasRelated()){
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
        foreach ($record->getRelated() AS $relationName=>$relatedRecords){
            if ($relatedRecords instanceof Record){
                $relatedRecords = array($relatedRecords);
            }

            //$this->saveMany($records, true);
            foreach ($relatedRecords AS $related){
                $this->saveRelated($record, $related, $relationName);
            }
        }

        return $this;
    }

    /**
     * @param Record $record
     * @param $relatedRecord
     * @param $relationName
     */
    private function saveRelated(Record $record, $relatedRecord, $relationName)
    {
        $relation = $record->getRelation($relationName);

        if ($relation instanceof BelongsToMany){
            $this->save($relatedRecord, true);
            $relation->setRelated($relatedRecord);

            $relation->newPivotStatement()->insert($relation->createPivotData());
        }
        elseif ($relation instanceof HasOneOrMany){
            $relation->create($relatedRecord);

            $this->save($relatedRecord, true);
        }
    }


    /**
     * Saves collection of records
     *
     * @param \Netinteractive\Elegant\Model\Collection|array $records
     * @param bool $saveRelated
     * @return \Netinteractive\Elegant\Model\Collection
     */
    public function saveMany($records, $saveRelated = false)
    {
        $response = \App::make('ni.elegant.model.collection');

        foreach ($records AS $record){
            if (is_array($record)){
                $record = $this->createRecord($record);
            }

            if ($saveRelated === true){
                \Event::fire('ni.elegant.mapper.touching.'.\classDotNotation($record), $record);
            }
            $this->save($record, $saveRelated);

            if ($saveRelated === true){
                \Event::fire('ni.elegant.mapper.touched.'.\classDotNotation($record), $record);
            }

            $response->add($record);
        }
        return $response;
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



        $data = $q->find($ids, $columns);

        if (!is_array($data) && $data instanceof \Illuminate\Contracts\Support\Arrayable){
            $data = $data->toArray();
        }

        if (!empty($data)){
            $record = $this->createRecord((array) $data);
            $record->setExists(true);
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
     * @return \Netinteractive\Elegant\Model\Query\Builder
     */
    public function getQuery()
    {
        #we pass record  to query builder
        if ($this->query->getRecord() == null){
            $this->query->setRecord($this->emptyRecord);
        }

        $this->query->setConnection($this->connection);

        return $this->query;
    }

    /**
     * creates new query builder object
     * @return mixed
     */
    public function getNewQuery()
    {
        $q = \App::make('ni.elegant.model.query.builder', array($this->connection,  $this->connection->getQueryGrammar(), $this->connection->getPostProcessor()));
        $q->setRecord($this->emptyRecord);

        return $q;
    }


    /**
     * Sets a new clean query builder
     * @return $this
     */
    public function resetQuery(){
        $this->query = $this->getNewQuery();
        return $this;
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
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return \Netinteractive\Elegant\Model\Collection|static[]
     */
    public function get($columns = array('*'))
    {
        $query = clone $this->query;
        $this->resetQuery();

        return $query->get($columns);
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param  string $column
     * @param  string $operator
     * @param  mixed $value
     * @param  string $boolean
     * @param  string $alias
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and', $alias = null){
        $this->query->where($column, $operator, $value, $boolean, $alias);
        return $this;
    }

    /**
     * Add a raw where clause to the query.
     *
     * @param  string $sql
     * @param  array $bindings
     * @param  string $boolean
     * @param  string $alias
     * @return $this
     */
    public function whereRaw($sql, array $bindings = array(), $boolean = 'and', $alias = null)
    {
        $this->query->whereRaw($sql, $bindings, $boolean, $alias);
        return $this;
    }

    /**
     * Add a where between statement to the query.
     *
     * @param  string $column
     * @param  array $values
     * @param  string $boolean
     * @param  bool $not
     * @param  string $alias
     * @return $this
     */
    public function whereBetween($column, array $values, $boolean = 'and', $not = false, $alias = null)
    {
        $this->query->whereBetween($column, $values, $boolean, $not, $alias);
        return $this;
    }


    /**
     * Add an exists clause to the query.
     *
     * @param  \Closure $callback
     * @param  string $boolean
     * @param  bool $not
     * @param  string $alias
     * @return $this
     */
    public function whereExists(\Closure $callback, $boolean = 'and', $not = false, $alias = null)
    {
        $this->query->whereExists($callback, $boolean, $not, $alias);
        return $this;
    }

    /**
     * Add a "where in" clause to the query.
     *
     * @param  string $column
     * @param  mixed $values
     * @param  string $boolean
     * @param  bool $not
     * @param  string $alias
     * @return $this
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false, $alias = null)
    {
        $this->query->whereIn($column, $values, $boolean, $not, $alias);
        return $this;
    }


    /**
     * Add a nested where statement to the query.
     *
     * @param  \Closure $callback
     * @param  string $boolean
     * @param  string $aliast
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function whereNested(\Closure $callback, $boolean = 'and', $alias = null)
    {
        $this->query->whereNested($callback, $boolean, $alias);
        return $this;
    }

    /**
     * Call the given model scope on the underlying model.
     *
     * @param  string  $scope
     * @param  array   $parameters
     * @return \Illuminate\Database\Query\Builder
     */
    protected function callScope($scopeObj, $scopeMethod, $parameters)
    {
        array_unshift($parameters, $this);

        return call_user_func_array(array($scopeObj, $scopeMethod), $parameters);
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
        $scopeObj = $this->emptyRecord->getBlueprint()->getScopeObject();
        $scope = 'scope'.ucfirst($method);

        if ( $scopeObj != null
            && $scopeObj instanceof \Netinteractive\Elegant\Model\Query\Scope
            && method_exists($scopeObj, $scope)
        ){
            return $this->callScope($scopeObj, $scope, $parameters);
        }

        $result =  call_user_func_array(array($this->query, $method), $parameters);

        if (in_array($method, array('get', 'first'))){
            $this->resetQuery();
            //echo 555; exit;
        }

        return $result;
    }
}