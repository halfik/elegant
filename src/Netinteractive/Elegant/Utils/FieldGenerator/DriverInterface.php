<?php

namespace Netinteractive\Elegant\Utils\FieldGenerator;

/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 2017-01-05
 * Time: 14:21
 */
interface DriverInterface
{

    /**
     * @param $table
     * @return array
     */
    public function getFieldsList($table);

    /***
     * @return mixed
     */
    public function getName();
}