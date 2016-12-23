<?php

namespace Netinteractive\Elegant\Tests\Models\MedScienceDegree;

use Netinteractive\Elegant\Search\Searchable;

/**
 * Class Blueprint
 * @package Netinteractive\Elegant\Tests\Models\MedScienceDegree
 */
class Blueprint extends \Netinteractive\Elegant\Model\Blueprint
{
   protected function init()
    {
        $this->setStorageName('med_science_degree');
        $this->primaryKey = array('id');
        $this->incrementingPk = 'id';

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
            'name' => array(
                'title' => _('name'),
                'type' => static::TYPE_STRING,

            )
        );

        return parent::init();
    }
}
