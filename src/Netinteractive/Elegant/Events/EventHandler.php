<?php
namespace Netinteractive\Elegant\Events;
use Netinteractive\Elegant\Elegant;
use Netinteractive\Elegant\Filters\Field\Display AS DisplayLogic;
use Netinteractive\Elegant\Filters\Field\Fill AS FillLogic;
use Netinteractive\Elegant\Filters\Field\Save AS SaveLogic;
use Netinteractive\Elegant\Filters\Logic AS FiltersLogic;

/**
 * Class ElegantEventHandler
 */
class EventHandler {

    /**
     * filtry modyfikujace dane modelu w momencie proby ich wyswietlenia
     * @param stdClass $obj
     */
    public function displayFilters($obj)
    {
        $filters =  FiltersLogic::parseFilters(array_get($obj->Record->getFieldFilters($obj->field),'display'));

        if (isSet($filters)){
            DisplayLogic::apply($obj, $filters);
        }
    }

    /**
     * filtry modyfikujace dane z modelu przed zapisem do bazy danych
     * (nie modyfikuja danych w samym modelu)
     * @param stdClass $obj
     */
    public function saveFilters($obj)
    {
        foreach ($obj->Record->getAttributes() AS $key=>$val){
            $filters =  FiltersLogic::parseFilters( array_get($obj->Record->getFieldFilters($key),'save'));

            if (isSet($filters)){
                SaveLogic::apply($obj, $filters);
            }
        }
    }

    /**
     * filtry, modyfikujace pola modelu w momencie zmiany ich wartosci przez setAttr (m.in. wypelnienie danymi z bazy)
     * @param Elegant $model
     */
    public function fillFilters(Elegant $model){
        foreach ($model->getAttributes() AS $key=>$val){
            $filters =  FiltersLogic::parseFilters( array_get($model->getFieldFilters($key),'fill'));

            if (isSet($filters)){
                FillLogic::apply($model, $filters);
            }
        }
    }

}