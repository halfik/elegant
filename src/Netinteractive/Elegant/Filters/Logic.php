<?php
namespace Netinteractive\Elegant\Filters;

/**
 * Class Logic
 * Klasa zawiera logike mechanizmu filtrow
 * @package Netinteractive\Elegant\Filters
 */
class Logic
{
    /**
     * metoda parsuje filtry
     * @param Elegant $model
     * @param string $key
     * @param string $type
     * @return mixed
     */
    public static function  parseFilters($filters)
    {
        if (!empty($filters) && !is_array($filters)){
            $filters = explode('|', str_replace(' ', '', $filters));
        }

        return $filters;
    }
}