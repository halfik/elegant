<?php namespace Netinteractive\Elegant\Tests\Models\Patient;

use Netinteractive\Elegant\Search\Searchable;

class Blueprint extends \Netinteractive\Elegant\Model\Blueprint
{
   protected function init()
    {
        $this->setStorageName('patient');
        $this->primaryKey = array('id');
        $this->incrementingPk = 'id';
        $this->timestamps = true;
        $this->softDelete = true;


        $this->getRelationManager()->hasMany('patientData','PatientData', array('patient__id'), array('id') );
        $this->getRelationManager()->belongsTo('user','User', array('user__id'), array('id') );
        $this->getRelationManager()->belongsTo('med','Tu', array('tu__id'), array('id') );
        $this->getRelationManager()->belongsTo('tu','Med', array('med__id'), array('id') );

        $this->fields = array(
            'id' => array(
                'title' => 'Id',
                'type' => 'int',
                'sortable' => true,
                'rules' => array(
                    'any' => 'integer',
                    'update' => 'required'
                )
            ),
            'user__id' => array(
                'title' => _('user__id'),
                'type' => 'int',
                'rules' => array(
                    'any' => 'integer|exists:user,id',
                )
            ),
            'pesel' => array(
                'title' => _('PESEL'),
                'type' => 'int',
                'sortable' => true,
                'searchable' => Searchable::$contains,
                'rules' => array(
                    'any' => 'required|unique:patient,pesel',
                ),
                'filters' => array(
                )
            ),
        );

        return parent::init();
    }
} 