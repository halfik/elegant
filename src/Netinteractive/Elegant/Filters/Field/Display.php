<?php
namespace Netinteractive\Elegant\Filters\Field;

/**
 * Class Display
 * Klasa zawiera logike display mechanizmu filtrow pol
 * Filtry modyfikujace dane modelu w momencie proby ich wyswietlenia za pomoca Elegant::display
 * @package Netinteractive\Elegant\Filters\Field
 */
class Display
{
    /**
     * mchanizm filtrow display
     * @param stdClass $obj
     * @param array $filters
     */
    public static function apply($obj, $filters)
    {
        $definedFilters = \Config::get('elegant::filters.display');

        foreach ($filters AS $filter){
            $filterInfo = explode(':', $filter);
            $filter = $filterInfo[0];

            if ( !is_scalar($filter)){
                $obj->value = $filter($obj->value);
            }
            elseif (isSet($definedFilters[$filter])){
                if (isSet($filterInfo[1])){
                    $params = explode(',', $filterInfo[1]);
                    $params = array_map('trim',$params);

                    $obj->value = $definedFilters[$filterInfo[0]]($obj->value , $params);
                }
                else{
                    $obj->value = $definedFilters[$filterInfo[0]]($obj->value);
                }
            }
        }
    }
}