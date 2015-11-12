<?php namespace Netinteractive\Elegant\Tests\Models\User;


use Netinteractive\Elegant\Search\Searchable;

class Blueprint extends \Netinteractive\Elegant\Model\Blueprint
{
   protected function init()
    {
        $this->setStorageName('user');
        $this->primaryKey = array('id');
        $this->incrementingPk = 'id';

        $this->getRelationManager()->hasOne('patient','Patient', 'user__id','id');

        $this->fields = array(
            'login'=>array(
                'title'=>_('Login'),
                'type'=>'string',
                'sortable' => true,
                'searchable' => Searchable::$contains,
                'rules'=>array(
                    'update'=>'required',
                    'insert'=>'required|unique:users'
                )
            ),
            'email'=>array(
                'title'=>_('E-mail'),
                'type'=>'string',
                'sortable' => true,
                'searchable' => Searchable::$contains,
                'rules'=>array(
                    'update'=>'required|email',
                    'insert'=>'required|email|unique:users'
                )
            ),
            'password'=>array(
                'title'=>_('Password'),
                'type'=>'string',
                'rules'=>array(
                    'insert'=>'required'
                )
            ),
            'first_name' => array(
                'title'=> _('First name'),
                'type'=>'string',
                'sortable' => true,
                'searchable' => Searchable::$contains,
                'rules'=>array(
                    'update'=>'',
                    'insert'=>''
                ),
                'filters' => array(
                    'fill' => array(
                        'stripTags'
                    )
                )
            ),
            'last_name' => array(
                'title'=> _('Last name'),
                'type'=>'string',
                'sortable' => true,
                'searchable' => Searchable::$contains,
                'rules'=>array(
                    'update'=>'',
                    'insert'=>''
                ),
                'filters' => array(
                    'fill' => array(
                        'stripTags'
                    )
                )
            ),
            'activated' => array(
                'title' => _('Is Active'),
                'type' => 'bool',
                'sortable' => true,
                'rules' => array(
                    'any' => 'in:0,1'
                ),
                'filters' => array(
                    'display' => array('bool'),
                )
            ),
            'tu__id' => array(
                'title' => _('Id TU'),
                'type' => 'int',
                'rules' => array(
                    'any' => 'integer|exists:tu,id',
                ),
                'filters' => array(
                    'fill' => array(
                    ),
                )
            ),
            'med__id' => array(
                'title' => _('Id Med'),
                'type' => 'int',
                'rules' => array(
                    'any' => 'integer|exists:med,id',
                ),
                'filters' => array(
                    'fill' => array(
                    )
                )
            )
        );

        return parent::init();
    }
} 