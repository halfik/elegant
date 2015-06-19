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
     * @param \Netinteractive\Elegant\Elegant $model
     * @param string $key
     * @param array $filters
     */
    public static function apply(\Netinteractive\Elegant\Elegant $model, $key, $filters)
    {
        $definedFilters = \Config::get('elegant::filters.fill');

        foreach ($filters AS $filter){
            $filterInfo = explode(':', $filter, 2);
            $filter = $filterInfo[0];

            if ( !is_scalar($filter)){
                $model->$key = $filter($model->$key);
            }
            elseif (isSet($definedFilters[$filter])){
                if (isSet($filterInfo[1])){
                    $params = explode(',', $filterInfo[1]);
                    $params = array_map('trim',$params);

                    $model->$key = $definedFilters[$filterInfo[0]]($model->$key, $params);
                }
                else{
                    $model->$key = $definedFilters[$filterInfo[0]]($model->$key);
                }
            }
        }
    }
}