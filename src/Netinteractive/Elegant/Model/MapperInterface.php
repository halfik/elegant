<?php namespace Netinteractive\Elegant\Model;


/**
 * Interface MapperInterface
 * @package Netinteractive\Elegant\Models
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
     * @param \Netinteractive\Elegant\Model\Record $record
     * @return $this
     */
    function save(Record $record);


    /**
     * Saves collection of records
     *
     * @param \Netinteractive\Elegant\Model\Collection $records
     * @return $this
     */
    function saveMany(Collection $records);

    /**
     * Find one model
     *
     * @param $id
     * @param array $columns
     * @return \Netinteractive\Elegant\Model\Record
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
     * Find first record
     * @param array $columns
     * @return \Netinteractive\Elegant\Model\Record|static|null
     */
    function first(array $columns = array('*'));

    /**
     * Create new record
     *
     * @param array $data
     * @return \Netinteractive\Elegant\Model\Record;
     */
    function createRecord(array $data = array());



    /**
     * Create Collection of records
     *
     * @param array $data
     * @return \Netinteractive\Elegant\Model\Collection
     */
     function createMany(array $data=array());


} 