<?php namespace Netinteractive\Elegant\Model;
use Netinteractive\Elegant\Repository\RepositoryInterface;

/**
 * Class Provider
 * Abstract class that should be use to implement business layer for specific model
 * @package Netinteractive\Elegant\Model
 */
abstract class Provider
{
    /**
     * @var \Netinteractive\Elegant\Repository\RepositoryInterface
     */
    protected $repository;


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
        $this->repository = \App::make('ni.elegant.repository', array($this->getRecordClass()));
    }

    /**
     * @param \Netinteractive\Elegant\Repository\RepositoryInterface  $repository
     * @return $this
     */
    public function setMapper(RepositoryInterface $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return \Netinteractive\Elegant\Repository\RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Returns all record.
     *
     * @return array $roles
     */
    public function findAll()
    {
        return $this->getRepository()->get();
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

        $this->getRepository()->save($record);

        return $record;
    }
}