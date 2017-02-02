<?php

namespace Netinteractive\Elegant\Domain;

use Netinteractive\Elegant\Repository\RepositoryInterface;

/**
 * Class ServiceProvider
 * Abstract class that should be use to implement business layer for specific model
 * @package Netinteractive\Elegant\Model
 */
abstract class ServiceProvider
{
    /**
     * @var \Netinteractive\Elegant\Repository\RepositoryInterface
     */
    protected $repository;


    /**
     * Domain model class
     *
     * @var string
     */
    protected $modelClass = null;


    /**
     * ServiceProvider constructor.
     * @param string $modelClass
     */
    public function __construct($modelClass)
    {
        $this->$modelClass = $modelClass;
        $this->init();
    }

    /**
     * init repositories and other needed objects
     */
    protected function init()
    {
        $repo = \App::make('ni.elegant.repository',
            array($this->getModelClass())
        );

        $this->setRepository($repo);
    }

    /**
     * @param \Netinteractive\Elegant\Repository\RepositoryInterface  $repository
     * @return $this
     */
    public function setRepository(RepositoryInterface $repository)
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
     * Create a new instance of model.
     *
     * @return mixed
     */
    public function createModel()
    {
        return \App::make($this->getModelClass());
    }

    /**
     * Returns model class name
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * Creates and saves record.
     *
     * @param  array $credentials
     * @param boolean $save
     * @return mixed
     */
    public function create(array $credentials, $save=true)
    {
        $model = $this->createModel();
        $model->fill($credentials);

        if($save){
            $this->getRepository()->save($model);
        }
        return $model;
    }
}