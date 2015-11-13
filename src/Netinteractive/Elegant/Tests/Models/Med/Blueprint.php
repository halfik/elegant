<?php namespace Netinteractive\Elegant\Tests\Models\Med;


use Netinteractive\Elegant\Search\Searchable;

class Blueprint extends \Netinteractive\Elegant\Model\Blueprint
{
   protected function init()
    {
        $this->setStorageName('med');
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
                'title' => _('Nazwa'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'required|max:100'
                ),
                'filters' => array(
                    'fill' => array(
                        'stripTags'
                    )
                )
            ),
            'city' => array(
                'title' => _('Miejscowość'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'required|max:100'
                ),
                'filters' => array(
                    'fill' => array(
                        'stripTags'
                    )
                )
            ),
            'street' => array(
                'title' => _('Ulica'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'required|max:100'
                ),
                'filters' => array(
                    'fill' => array(
                        'stripTags'
                    )
                )
            ),
            'zip_code' => array(
                'title' => _('Kod pocztowy'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'required'
                ),
                'filters' => array(
                    'fill' => array(
                        'stripTags'
                    )
                )
            ),
            'nip' => array(
                'title' => _('NIP'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'required'
                ),
                'filters' => array(
                    'fill' => array(
                        'stripTags'
                    )
                )
            ),
            'regon' => array(
                'title' => _('Regon'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'required'
                ),
                'filters' => array(
                    'fill' => array(
                        'stripTags'
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
                        'stripTags'
                    )
                )
            ),
            'spokesman' => array(
                'title' => _('spokesman'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'required|max:100'
                ),
                'filters' => array(
                    'fill' => array(
                        'stripTags'
                    )
                )
            ),
            'phone' => array(
                'title' => _('phone'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'required|max:20'
                ),
                'filters' => array(
                    'fill' => array(
                        'phone',
                        'stripTags'
                    )
                )
            ),
            'cell_phone' => array(
                'title' => _('cell_phone'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'max:20'
                ),
                'filters' => array(
                    'fill' => array(
                        'phone'.
                        'stripTags'
                    )
                )
            ),
            'email' => array(
                'title' => _('Email'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'required|email|max:150'
                ),
                'filters' => array(
                    'fill' => array(
                        'stripTags'
                    )
                )
            ),
            'bank_name' => array(
                'title' => _('Bank'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'max:150'
                ),
                'filters' => array(
                    'fill' => array(
                        'stripTags'
                    )
                )
            ),
            'bank_account' => array(
                'title' => _('bank_account'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'max:50'
                ),
                'filters' => array(
                    'fill' => array(
                        'bank_account',
                        'stripTags'
                    )
                )
            ),

        );
        return parent::init();
    }
} 