<?php
namespace Netinteractive\Elegant\Events;
use Illuminate\Support\Facades\Config;
use Netinteractive\Elegant\Elegant;


/**
 * Class ElegantEventHandler
 */
class EventHandler {

    /**
     * filtry modyfikujace dane modelu w momencie proby ich wyswietlenia
     * @param stdClass $obj
     */
    public function displayFilters($obj){
        $definedFilters = \Config::get('elegant::filters.display');
        $filters = $obj->Record->getFieldFilters($obj->field);

        if (isSet($filters['display'])){
            foreach ($filters['display'] AS $filter){
                if ( !is_scalar($filter)){
                    $obj->value = $filter($obj->value);
                }
                elseif (isSet($definedFilters[$filter])){
                    $obj->value = $definedFilters[$filter]($obj->value );
                }
            }
        }
    }

    /**
     * filtry modyfikujace dane z modelu przed zapisem do bazy danych
     * (nie modyfikuja danych w samym modelu)
     * @param stdClass $obj
     */
    public function saveFilters($obj){
        $definedFilters = \Config::get('elegant::filters.save');

        foreach ($obj->Record->getAttributes() AS $key=>$val){
            $filters = $obj->Record->getFieldFilters($key);

            if (isSet($filters['save'])){
                foreach ($filters['save'] AS $filter){
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
     * filtry, modyfikujace pola modelu w momencie zmiany ich wartosci przez setAttr (m.in. wypelnienie danymi z bazy)
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