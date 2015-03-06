<?php
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 06.03.15
 * Time: 10:58
 */

namespace Netinteractive\Elegant\Model;

use Netinteractive\Elegant\Model\Model;

/**
 * Interface MapperInterface
 * @package Netinteractive\Elegant\Model
 */
interface MapperInterface {

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
    function save(Model $model);

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
     * Create new model
     *
     * @param array $data
     * @return Model
     */
    function createModel(array $data = array());

    /**
     * get name of moles class
     *
     * @return string
     */
    function getModelName();

} 