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
            'created_at' => array(
                'title'=> _('created_at'),
                'type'=>'dateTime',
                'sortable' => true,
                'external'=>true
            ),
            'id' => array(
                'title' => 'Id',
                'type' => static::TYPE_INT,
                'sortable' => true,
                'rules' => array(
                    'any' => 'integer',
                    'update' => 'required'
                )
            ),
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
            'ip'=>array(
                'title'=>_('ip'),
                'type'=>'string',
                'external'=>true
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

            'tu__id' => array(
                'title' => _('Id TU'),
                'type' => 'int',
                'rules' => array(
                    'any' => 'integer',
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
                    'any' => 'integer',
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