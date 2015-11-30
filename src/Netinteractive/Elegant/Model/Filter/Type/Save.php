<?php namespace Netinteractive\Elegant\Model\Filter\Type;


/**
 * Class Save
 * Save filters modify record data before they are save to data storage (it dosn't change record data only create and modify copy of this data)
 * @package Netinteractive\Elegant\Models\Filter
 */
class Save
{
    /**
     * Save filters logic
     * @param \stdClass $obj
     * @param string $key
     * @param array $filters
     */
    public static function apply($obj, $key, $filters)
    {
        $serializer = new \SuperClosure\Serializer;
        $definedFilters = config('netinteractive/elegant/filters.save');

        foreach ($filters AS $filter){
            $filterInfo = explode(':', $filter, 2);
            $filter = $filterInfo[0];

            if ( is_callable($filter)){
                $obj->data[$key] = $filter($obj->data[$key]);
            }
            else{
                $filterInfo = explode(':', $filter, 2);
                $filter = $filterInfo[0];

                if (isSet($definedFilters[$filter]) && isset($obj->data[$key])){
                    $filter = $serializer->unserialize($definedFilters[$filterInfo[0]]);

                    if (isSet($filterInfo[1])){
                        $params = explode(',', $filterInfo[1]);
                        $params = array_map('trim',$params);
                        $obj->data[$key] = $filter($obj->data[$key], $params);
                    }
                    else{
                        $obj->data[$key] = $filter($obj->data[$key]);
                    }
                }
            }
        }
    }
}