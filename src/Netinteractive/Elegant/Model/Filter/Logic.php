<?php namespace Netinteractive\Elegant\Model\Filter;


/**
 * Class Logic
 * @package Netinteractive\Elegant\Model\Filter
 */
class Logic
{
    /**
     *  Parse filters
     *  @param mixed $filters
     *  @return mixed
     */
    public static function  parseFilters($filters)
    {
        if (!empty($filters) && !is_array($filters)){
            $filters = explode('|', $filters);
        }

        return $filters;
    }
}