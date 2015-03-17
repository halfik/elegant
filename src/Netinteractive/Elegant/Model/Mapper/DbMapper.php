<?php
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 06.03.15
 * Time: 10:58
 */

namespace Netinteractive\Elegant\Model\Mapper;
use Netinteractive\Elegant\Exception\PrimaryKeyException;
use Netinteractive\Elegant\Model\MapperInterface;
use Netinteractive\Elegant\Model\Model;

/**
 * Class DbMapper
 * @package Netinteractive\Elegant\Model\Mapper
 */
abstract class DbMapper implements MapperInterface
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;


    protected $modelName;


    /**
     * Create a new db mapper
     *
     * @return void
     */
    public function __construct($connection=null)
    {
        $this->connection = \App('db')->connection($connection);
    }


    /**
     * Returns model Blueprint
     * @return \Netinteractive\Elegant\Model\Blueprint
     */
    protected function getBlueprint()
    {
        return $this->createModel()->getBlueprint();
    }


    /**
     * Method checkcs if blueprint primary keys are same with input array keys
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
     * Delete record
     *
     * @param integer $id
     * @return $this
     */
    public function delete($ids)
    {
        $this->checkPrimaryKey($ids);

        return $this->getQuery()->from($this->getBlueprint()->getTable())->delete($ids);
    }

    /**
     * Save model
     *
     * @param Model $model
     * @return $this
     */
    public function save(Model $model)
    {


        return $this;
    }

    /**
     * Find one model
     *
     * @param $ids
     * @param array $columns
     * @return Model
     */
    public function find($ids, array $columns=array('*'))
    {
        $this->checkPrimaryKey($ids);

        $data = $this->getQuery()->from($this->getBlueprint()->getTable())->find($ids, $columns);

        $model = $this->createModel((array) $data);
        $model->exists = true;

        return $model;
    }

    /**
     * Find collection of models
     *
     * @param array $params
     * @return mixed
     */
    public function findMany(array $params)
    {

    }

    /**
     * Create new model
     *
     * @param array $data
     * @return Model
     */
    public function createModel(array $data = array())
    {
        $model = \App::make($this->getModelName());
        $model->fill($data);
        $model->exists = false;

        return $model;
    }

    /**
     * get name of moles class
     *
     * @return string
     */
    public function getModelName()
    {
        return $this->modelName;
    }

    /**
     * @return mixed`
     */
    public function getQuery()
    {
        return \App::make('Builder', array($this->connection,  $this->connection->getQueryGrammar(), $this->connection->getPostProcessor()));
    }

    public function getFindQuery()
    {

    }
} 