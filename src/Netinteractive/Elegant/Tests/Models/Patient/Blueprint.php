<?php namespace Netinteractive\Elegant\Tests\Models\Patient;



class Blueprint extends \Netinteractive\Elegant\Model\Blueprint
{
   protected function init()
    {
        $this->setStorageName('patient');
        $this->primaryKey = array('id');
        $this->incrementingPk = 'id';
        $this->timestamps = true;

        $this->getRelationManager()->belongsTo('User', 'user__id', 'id');
        $this->getRelationManager()->hasMany('PatientData', 'patient__id', 'id');

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