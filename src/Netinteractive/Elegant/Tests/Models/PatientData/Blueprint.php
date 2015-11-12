<?php namespace Netinteractive\Elegant\Tests\Models\PatientData;

use Netinteractive\Elegant\Search\Searchable;

class Blueprint extends \Netinteractive\Elegant\Model\Blueprint
{
   protected function init()
    {
        $this->setStorageName('patient_data');
        $this->primaryKey = array('id', 'patient__id');
        $this->incrementingPk = 'id';
        $this->timestamps = true;

        $this->getRelationManager()->belongsTo('Tu', 'tu__id', 'id');
        $this->getRelationManager()->belongsTo('Med', 'med__id', 'id');
        $this->getRelationManager()-> belongsTo('Patient', 'patient__id', 'id');

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
            'patient__id' => array(
                'title' => _('patient__id'),
                'type' => 'int',
                'rules' => array(
                    'any' => 'required|integer|exists:patient,id',
                )
            ),
            'med__id' => array(
                'title' => _('med__id'),
                'type' => 'int',
                'searchable' => Searchable::$equal,
                'rules' => array(
                    'any' => 'integer|exists:med,id',
                )
            ),
            'tu__id' => array(
                'title' => _('tu__id'),
                'type' => 'int',
                'searchable' => Searchable::$equal,
                'rules' => array(
                    'any' => 'integer|exists:tu,id',
                )
            ),
            'first_name' => array(
                'title' => _('first_name'),
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
            'last_name' => array(
                'title' => _('last_name'),
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
            'birth_date' => array(
                'title' => _('birth_date'),
                'type' => 'date',
                'rules' => array(
                    'any' => 'required|date',
                )
            ),
            'zip_code' => array(
                'title' => _('zip_code'),
                'type' => 'string',
                'sortable' => true,
                'searchable' => Searchable::$contains,
                'rules' => array(
                    'any' => 'required'
                )
            ),
            'city' => array(
                'title' => _('city'),
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
            'street' => array(
                'title' => _('street'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'required|max:100'
                ),
                'filters' => array(
                    'fill' => array(
                        ''
                    )
                )
            ),
            'email' => array(
                'title' => _('E-mail'),
                'type' => 'string',
                'sortable' => true,
                'rules' => array(
                    'any' => 'required|email|max:150'
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
                    )
                )
            ),
            'notes' => array(
                'title' => _('notes'),
                'type' => 'text',
                'rules' => array(),
                'filters' => array(
                    'fill' => array(
                    )
                )
            ),
        );

        return parent::init();
    }
} 