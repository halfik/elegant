<?php namespace Netinteractive\Elegant\Model;

/**
 * Class Provider
 * Abstract class that should be use to implement business layer for specific model
 * @package Netinteractive\Elegant\Model
 */
abstract class Provider
{
    /**
     * @var \Netinteractive\Elegant\Mapper\MapperInterface
     */
    protected $mapper;


    /**
     * The Elegant record class
     *
     * @var string
     */
    protected $recordClass = null;

    /**
     * Constructor
     * @param \Netinteractive\Elegant\Model\Record $record
     */
    public function __construct($recordClass)
    {
        $this->recordClass = $recordClass;
        $this->mapper = \App::make('ni.elegant.mapper.db', array($this->getRecordClass()));
    }

    /**
     * @param \Netinteractive\Elegant\Mapper\MapperInterface $mapper
     * @return $this
     */
    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * @return mixed|\Netinteractive\Elegant\Mapper\MapperInterface
     */
    public function getMapper()
    {
        return $this->mapper;
    }

    /**
     * Create a new instance of the record.
     *
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function createRecord()
    {
        return \App::make($this->getRecordClass());
    }

    /**
     * Returns record class name
     * @return string
     */
    public function getRecordClass()
    {
        return $this->recordClass;
    }

    /**
     * Creates and saves record.
     *
     * @param  array $credentials
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function create(array $credentials)
    {
        $record = $this->createRecord();
        $record->fill($credentials);

        $this->getMapper()->save($record);

        return $record;
    }
}