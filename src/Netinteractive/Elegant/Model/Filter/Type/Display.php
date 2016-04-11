<?php namespace Netinteractive\Elegant\Model\Filter\Type;


/**
 * Class Display
 * This class contains the logic of field display mechanism filter
 * Filters modifiers record data when field is access by display method
 *
 * @package Netinteractive\Elegant\Models\Filter
 */
class Display
{
    /**
     *  Apply display filter on field
     * @param \stdClass $obj
     * @param array $filters
     */
    public static function apply($obj, $filters)
    {
        $definedFilters = config('packages.netinteractive.elegant.filters.display');

        foreach ($filters AS $filter){
            if ( is_callable($filter)){
                $obj->value = $filter($obj->value, $obj->field, array(), $obj->record);
            }
            else{
                $filterInfo = explode(':', $filter, 2);
                $filter = $filterInfo[0];

                if (isSet($definedFilters[$filter])){
                    $filter = unserialize($definedFilters[$filterInfo[0]]);

                    if (isSet($filterInfo[1])){
                        $params = explode(',', $filterInfo[1]);
                        $params = array_map('trim',$params);

                        $obj->value = $filter($obj->value , $obj->field, $params, $obj->record);
                    }
                    else{
                        $obj->value = $filter($obj->value, $obj->field, array(), $obj->record);
                    }
                }
            }

        }
    }

    public function getFunc($func)
    {
        return $func;
    }
}