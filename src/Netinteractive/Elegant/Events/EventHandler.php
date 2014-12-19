<?php
namespace Netinteractive\Elegant\Events;
use Illuminate\Support\Facades\Config;
use Netinteractive\Elegant\Elegant;


/**
 * Class ElegantEventHandler
 */
class EventHandler {

    /**
     * metoda parsuje filtry
     * @param Elegant $model
     * @param string $key
     * @param string $type
     * @return mixed
     */
    protected function _parseFilters($filters){
        if (!empty($filters) && !is_array($filters)){
            $filters = explode('|', str_replace(' ', '', $filters));
        }

        return $filters;
    }


    /**
     * filtry modyfikujace dane modelu w momencie proby ich wyswietlenia
     * @param stdClass $obj
     */
    public function displayFilters($obj){
        $definedFilters = \Config::get('elegant::filters.display');
        $filters =  $this->_parseFilters( $obj->Record->getFieldFilters($obj->field)['display'] );

        if (isSet($filters)){
            foreach ($filters AS $filter){
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
            $filters =  $this->_parseFilters( $obj->Record->getFieldFilters($key)['save'] );

            if (isSet($filters)){
                foreach ($filters AS $filter){
                    if ( !is_scalar($filter)){
                        $obj->data[$key] = $filter($obj->data[$key]);
                    }
                    elseif (isSet($definedFilters[$filter])){
                        $obj->data[$key] = $definedFilters[$filter]($obj->data[$key]);
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
            $filters =  $this->_parseFilters( $model->getFieldFilters($key)['fill'] );

            if (isSet($filters)){
                foreach ($filters AS $filter){
                    if ( !is_scalar($filter)){
                        $model->$key = $filter($model->$key);
                    }
                    elseif (isSet($definedFilters[$filter])){
                        $model->$key = $definedFilters[$filter]($model->$key);
                    }
                }
            }
        }
    }

}