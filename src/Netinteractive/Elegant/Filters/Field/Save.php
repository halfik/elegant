<?php
namespace Netinteractive\Elegant\Filters\Field;

/**
 * Class Save
 * Klasa zawiera logike save mechanizmu filtrow pol
 *  SA to filtry modyfikujace dane z modelu przed zapisem do bazy danych
 * (nie modyfikuja danych w samym modelu)
 * @package Netinteractive\Elegant\Filters\Field
 */
class Save
{
    /**
     * mchanizm filtrow save
     * @param stdClass $obj
     * @param string $key
     * @param array $filters
     */
    public static function apply($obj, $key, $filters)
    {
        $definedFilters = \Config::get('elegant::filters.save');

        foreach ($filters AS $filter){
            $filterInfo = explode(':', $filter);
            $filter = $filterInfo[0];

            if ( !is_scalar($filter)){
                $obj->data[$key] = $filter($obj->data[$key]);

            }
            elseif (isSet($definedFilters[$filter]) && isset($obj->data[$key])){
                if (isSet($filterInfo[1])){
                    $params = explode(',', $filterInfo[1]);
                    $params = array_map('trim',$params);
                    $obj->data[$key] = $definedFilters[$filterInfo[0]]($obj->data[$key], $params);
                }
                else{
                    $obj->data[$key] = $definedFilters[$filterInfo[0]]($obj->data[$key]);
                }
            }
        }
    }
}