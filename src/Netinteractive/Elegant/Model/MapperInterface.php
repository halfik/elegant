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
     * @param integer $id
     * @return $this
     */
    function delete($id);

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