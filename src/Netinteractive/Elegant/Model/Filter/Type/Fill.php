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
        $serializer = new \SuperClosure\Serializer;
        $definedFilters = config('netinteractive/elegant/filters.fill');

        foreach ($filters AS $filter){
            $filterInfo = explode(':', $filter, 2);
            $filter = $filterInfo[0];

            if ( !is_scalar($filter)){
                $record->$key = $filter($record->$key);
            }
            elseif (isSet($definedFilters[$filter])){
                $filter = $serializer->unserialize($definedFilters[$filterInfo[0]]);

                if (isSet($filterInfo[1])){
                    $params = explode(',', $filterInfo[1]);
                    $params = array_map('trim',$params);
                    $record->$key = $filter($record->$key, $params);
                }
                else{
                   $record->$key = $filter($record->$key);
                }
            }
        }
    }
}