<?php
namespace Netinteractive\Elegant\Events;
use Illuminate\Support\Facades\Config;
use Netinteractive\Elegant\Elegant;


/**
 * Class ElegantEventHandler
 */
class EventHandler {

    /**
     * filtry modyfikujace dane z modelu przed zapisem do bazy danych
     * (nie modyfikuja danych w samym modelu)
     * @param Elegant $model
     */
    public function saveFilters($obj){

        $definedFilters = \Config::get('elegant::filters.save');

        foreach ($obj->Record->getAttributes() AS $key=>$val){
            $filters = $obj->Record->getFieldFilters($key);

            if (isSet($filters['fill'])){
                foreach ($filters['fill'] AS $filter){
                    if ( !is_scalar($filter)){
                        $obj->data[$key] = $filter($val);
                    }
                    elseif (isSet($definedFilters[$filter])){
                        $obj->data[$key] = $definedFilters[$filter]($val);
                    }
                }
            }
        }
    }

    /**
     * filtry, modyfikujace pola modelu w momencie zmiany ich wartosci
     * @param Elegant $model
     */
    public function fillFilters(Elegant $model){
        $definedFilters = \Config::get('elegant::filters.fill');

        foreach ($model->getAttributes() AS $key=>$val){
            $filters = $model->getFieldFilters($key);

            if (isSet($filters['fill'])){
                foreach ($filters['fill'] AS $filter){

                    if ( !is_scalar($filter)){
                        $model->$key = $filter($val);
                    }
                    elseif (isSet($definedFilters[$filter])){
                        $model->$key = $definedFilters[$filter]($val);
                    }
                }
            }
        }
    }

}