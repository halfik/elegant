<?php namespace Netinteractive\Elegant\Model\Filter\Type;


/**
 * Class Display
 * This class contains the logic of field display mechanism filter
 * Filters modifiers record data when field is access by display methodt
 * @package Netinteractive\Elegant\Model\Filter
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
            if ( !is_scalar($filter)){
                $obj->value = $filter($obj->value);
            }
            else{
                $filterInfo = explode(':', $filter, 2);
                $filter = $filterInfo[0];
                if (isSet($definedFilters[$filter])){
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
}