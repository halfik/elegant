<?php namespace Netinteractive\Elegant\Model\Filter\Event;

use Netinteractive\Elegant\Model\Filter\Logic;
use Netinteractive\Elegant\Model\Filter\Type\Display;
use Netinteractive\Elegant\Model\Filter\Type\Fill;
use Netinteractive\Elegant\Model\Filter\Type\Save;

use Netinteractive\Elegant\Model\Record;

/**
 * Class Handler
 * @package Netinteractive\Elegant\Models\Filter
 */
class Handler
{
    /**
     * applies display filters
     * @param \stdClass $obj
     */
    public function displayFilters(\stdClass $obj)
    {
        if ($obj->record->hasBlueprint()){
            $filters =  Logic::parseFilters($obj->record->getBluePrint()->getFieldFilters($obj->field, 'display'));

            foreach ($filters AS $key=>$val){
                $val = trim($val);
                if (empty($val)){
                    unset($filters[$key]);
                }
            }

            if (isSet($filters) && !empty($filters)){
                Display::apply($obj, $filters);
            }
        }
    }

    /**
     * applies save filters
     * @param \stdClass $obj
     */
    public function saveFilters(\stdClass $obj)
    {
        if ($obj->record->hasBlueprint()){
            foreach ($obj->record->getAttributes() AS $key=>$val){
                $filters =  Logic::parseFilters( $obj->record->getBluePrint()->getFieldFilters($key, 'save' ));

                foreach ($filters AS $k=>$v){
                    $v = trim($v);
                    if (empty($v)){
                        unset($filters[$k]);
                    }
                }

                if (isSet($filters) && !empty($filters)){
                    Save::apply($obj, $key, $filters);
                }
            }
        }
    }

    /**
     * applies fill filters
     * @param \stdClass $obj
     */
    public function fillFilters(\stdClass $obj)
    {
        if ($obj->record->hasBlueprint()){
            foreach ($obj->record->getAttributes() AS $key=>$val){
                $filters =  Logic::parseFilters( $obj->record->getBluePrint()->getFieldFilters($key, 'fill') );

                foreach ($filters AS $k=>$v){
                    $v = trim($v);
                    if (empty($v)){
                        unset($filters[$k]);
                    }
                }

                if (isSet($filters) && !empty($filters)){
                    Fill::apply($obj->record, $key, $filters);
                }
            }
        }
    }

} 