<?php

namespace Netinteractive\Elegant\Tests\Models\MedPersonnel;


/**
 * Class Blueprint
 * @package Netinteractive\Elegant\Tests\Models\MedPersonnel
 */
class Blueprint extends \Netinteractive\Elegant\Model\Blueprint
{
    protected function init()
    {
        $this->setStorageName('med_personnel');
        $this->primaryKey = array('id');
        $this->incrementingPk = 'id';

        $this->getRelationManager()->belongsToMany('med_degree', 'MedScienceDegree', 'med_personnel__med_sience_degree', 'med_personnel__id', 'med_sience_degree__id');
        $this->getRelationManager()->belongsTo('med', 'Med', 'med__id', 'id');


        $this->fields = array(
            'id' => array(
                'title' => 'Id',
                'type' => static::TYPE_INT,
                'sortable' => true,
                'rules' => array(
                    'any' => 'integer',
                    'update' => 'required'
                )
            ),
            'user__id' => array(
                'title' => _('user__id'),
                'type' => static::TYPE_INT,
                'rules' => array(
                    'any' => 'integer|exists:user,id',
                )
            ),
            'med__id' => array(
                'title' => _('med__id'),
                'type' => static::TYPE_INT,
                'rules' => array(
                    'any' => 'integer|exists:med,id',
                )
            ),
            'first_name' => array(
                'title' => _('first_name'),
                'type' => static::TYPE_STRING,

            ),
            'last_name' => array(
                'title' => _('last_name'),
                'type' => static::TYPE_STRING,

            ),
        );

        return parent::init();
    }


    /**
     * Returns scope object
     * @return null
     */
    public function getScopeObject()
    {
        return new Scope($this->getStorageName());
    }
}
