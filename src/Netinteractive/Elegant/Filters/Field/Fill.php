<?php
namespace Netinteractive\Elegant\Filters\Field;

/**
 * Class Fill
 * Klasa zawiera logike fill mechanizmu filtrow pol
 * @package Netinteractive\Elegant\Filters\Field
 */
class Fill
{
    /**
     * mchanizm filtrow fill
     * @param \Netinteractive\Elegant\Elegant $model
     * @param array $filters
     */
    public static function apply(\Netinteractive\Elegant\Elegant $model, $filters)
    {
        $definedFilters = \Config::get('elegant::filters.fill');

        foreach ($filters AS $filter){
            $filterInfo = explode(':', $filter);
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