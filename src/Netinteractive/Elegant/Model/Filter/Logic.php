<?php

namespace Netinteractive\Elegant\Model\Filter;


/**
 * Class Logic
 * @package Netinteractive\Elegant\Models\Filter
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
        if ( !is_array($filters)){
            $filters = explode('|', $filters);
        }

        return array_map('trim', $filters);
    }
}