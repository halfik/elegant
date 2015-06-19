<?php namespace Netinteractive\Elegant\Model\Filter\Event;

use Netinteractive\Elegant\Model\Filter\Logic;
use Netinteractive\Elegant\Model\Filter\Type\Display;
use Netinteractive\Elegant\Model\Filter\Type\Fill;
use Netinteractive\Elegant\Model\Filter\Type\Save;

use Netinteractive\Elegant\Model\Record;

/**
 * Class Handler
 * @package Netinteractive\Elegant\Model\Filter
 */
class Handler
{
    /**
     * applys display filters
     * @param \stdClass $obj
     */
    public function displayFilters(\stdClass $obj)
    {
        $filters =  Logic::parseFilters($obj->record->getBluePrint()->getFieldFilters($obj->field, 'display'));

        if (isSet($filters)){
            Display::apply($obj, $filters);
        }
    }

    /**
     * applys save filters
     * @param \stdClass $obj
     */
    public function saveFilters(\stdClass $obj)
    {
        foreach ($obj->record->getAttributes() AS $key=>$val){
            $filters =  Logic::parseFilters( $obj->record->getBluePrint()->getFieldFilters($key, 'save' ));

            if (isSet($filters)){
                Save::apply($obj, $key, $filters);
            }
        }
    }

    /**
     * apllys fill filters
     * @param \stdClass $record
     */
    public function fillFilters(\stdClass $obj)
    {
        foreach ($obj->record->getAttributes() AS $key=>$val){
            $filters =  Logic::parseFilters( $obj->record->getBluePrint()->getFieldFilters($key, 'fill') );

            if (isSet($filters)){
                Fill::apply($obj->record, $key, $filters);
            }
        }
    }

} 