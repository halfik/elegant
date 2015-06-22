<?php
namespace Netinteractive\Elegant\Model\Filter;

use Netinteractive\Elegant\Model\Filter\Type\Display;

/**
 * Class Loader
 * @package Netinteractive\Elegant\Filters
 */
class Loader
{
    /**
     * @param mixed $value
     * @param mixed $filters
     * @return mixed
     */
    public static function run($value, $filters)
    {
        if (!is_array($filters)){
            $filters = array($filters);
        }

        $displayFilter = new Display();

        $obj = new \stdClass();
        $obj->value = $value;

        $displayFilter->apply($obj, $filters);

        return $obj->value;
    }
} 