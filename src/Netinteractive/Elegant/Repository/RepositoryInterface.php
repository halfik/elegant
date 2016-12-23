<?php

namespace Netinteractive\Elegant\Repository;


use \Netinteractive\Elegant\Model\Record;
use \Netinteractive\Elegant\Model\Collection;

/**
 * Interface RepositoryInterfaceInterface
 * @package Netinteractive\Elegant\Repository
 */
interface RepositoryInterface
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
     * @param \Netinteractive\Elegant\Model\Collection|array $records
     * @return $this
     */
    function saveMany($records);

    /**
     * Find one model
     *
     * @param $id
     * @param array $columns
     * @return \Netinteractive\Elegant\Model\Record
     */
    function find($id, array $columns=array('*'));

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