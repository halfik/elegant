<?php namespace Netinteractive\Elegant\Model;


/**
 * Interface MapperInterface
 * @package Netinteractive\Elegant\Model
 */
interface MapperInterface
{

    /**
     * Delete record
     *
     * @param \Netinteractive\Elegant\Model\Record $record
     * @return int
     */
     function delete(Record $record);

    /**
     * Save model
     *
     * @param Model $model
     * @return $this
     */
    function save(Record $model);

    /**
     * Find one model
     *
     * @param $id
     * @param array $columns
     * @return Model
     */
    function find($id, array $columns);

    /**
     * Find collection of models
     *
     * @param array $params
     * @return mixed
     */
    function findMany(array $params);

    /**
     * Create new record
     *
     * @param array $data
     * @return Netinteractive\Elegant\Model\Record;
     */
    function createRecord(array $data = array());

    /**
     * Returns record class name
     *
     * @return string
     */
    function getRecordClass();

} 