<?php
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 06.03.15
 * Time: 10:58
 */

namespace Netinteractive\Elegant\Model\Mapper;
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
     * Delete record
     *
     * @param integer $id
     * @return $this
     */
    public function delete($id)
    {

    }

    /**
     * Save model
     *
     * @param Model $model
     * @return $this
     */
    public function save(Model $model)
    {

    }

    /**
     * Find one model
     *
     * @param $id
     * @param array $columns
     * @return Model
     */
    public function find($id, array $columns=array('*'))
    {
        $model = \App::make($this->getModelName());

        $this->getQuery()->from($model->getBlueprint()->getTable())->find($id, $columns);
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

    }

    /**
     * get name of moles class
     *
     * @return string
     */
    public function getModelName()
    {

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