<?php namespace Netinteractive\Elegant\Model\Filter\Type;


/**
 * Class Fill
 * This class contains the logic of fill filters
 * @package Netinteractive\Elegant\Model\Filter\
 */
class Fill
{
    /**
     * Fill logic
     * @param \Netinteractive\Elegant\Model\Record $model
     * @param string $key
     * @param array $filters
     */
    public static function apply(\Netinteractive\Elegant\Model\Record $record, $key, $filters)
    {
        $definedFilters = \Config::get('elegant::filters.fill');

        foreach ($filters AS $filter){
            $filterInfo = explode(':', $filter, 2);
            $filter = $filterInfo[0];

            if ( !is_scalar($filter)){
                $record->$key = $filter($record->$key);
            }
            elseif (isSet($definedFilters[$filter])){
                if (isSet($filterInfo[1])){
                    $params = explode(',', $filterInfo[1]);
                    $params = array_map('trim',$params);

                    $record->$key = $definedFilters[$filterInfo[0]]($record->$key, $params);
                }
                else{
                    $record->$key = $definedFilters[$filterInfo[0]]($record->$key);
                }
            }
        }
    }
}