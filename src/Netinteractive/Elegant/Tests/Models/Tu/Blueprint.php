<?php namespace Netinteractive\Elegant\Tests\Models\Tu;


use Netinteractive\Elegant\Search\Searchable;

class Blueprint extends \Netinteractive\Elegant\Model\Blueprint
{
   protected function init()
    {
        $this->setStorageName('tu');
        $this->primaryKey = array('id');
        $this->incrementingPk = 'id';


        $this->getRelationManager()->hasMany('patientData','PatientData','med__id', 'id');
        $this->getRelationManager()->hasMany('user','User', 'med_id', 'id');
        $this->getRelationManager()->belongsToMany('patients','Patient', array('patient_data', 'med__id', array('patient__id', 'patient__pesel') ) );

        $this->timestamps = true;

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
            'name' => array(
                'title' => _('name'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'required|max:100'
                ),
                'filters' => array(
                    'fill' => array(
                    )
                )
            ),
            'city' => array(
                'title' => _('city'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'max:100'
                ),
                'filters' => array(
                    'fill' => array(
                    )
                )
            ),
            'zip_code' => array(
                'title' => _('zip_code'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                )
            ),
            'street' => array(
                'title' => _('street'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'max:100'
                ),
                'filters' => array(
                    'fill' => array(
                    )
                )
            ),
            'nip' => array(
                'title' => _('NIP'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                ),
                'filters' => array(
                    'fill' => array(
                    )
                )
            ),
            'regon' => array(
                'title' => _('Regon'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => ''
                ),
                'filters' => array(
                    'fill' => array(
                    )
                )
            ),
            'krs' => array(
                'title' => _('KRS'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'required|max:20'
                ),
                'filters' => array(
                    'fill' => array(
                    )
                )
            ),
            'phone' => array(
                'title' => _('phone'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'max:20'
                ),
                'filters' => array(
                    'fill' => array(
                    )
                )
            ),
            'mobile' => array(
                'title' => _('mobile'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'max:20'
                ),
                'filters' => array(
                )
            ),
            'email' => array(
                'title' => _('Email'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'email|max:150'
                )
            ),
            'main_representative' => array(
                'title' => _('main_representative'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'max:150'
                ),
                'filters' => array(
                )
            )
        );

        return parent::init();
    }
} 