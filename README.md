Netinteractive\Elegant
======================

Elegant is a domain model package. He is similar to laravels Eloquent and we used a lot of Eloquent code to build this package.


## Services
* ElegantServiceProvider - registers in App most important classes:
     * ElegantDbMapper - class that allows to work with databases.
     * ElegantQueryBuilder - database query builder.
     * ElegantModelQueryBuilder - model query builder. Responsible for relations.
     * ElegantCollection - data collection class
     * ElegantRelationManager - class that allows to register relations translators for different data sources. ElegantServiceProvider register standard db translator.
     * ElegantRelationDbTranslator - class that knows how to build database relations based on informations from blueprint
     * ElegantSearchDbTranslator - class that knows how to build and add to query proper where statements based on blueprint informations.


## Documentation

In docs folder you can find more documentation about package.

All examples are based on this 3 classes:

### User
     <?php namespace Core2\Models\User;

     use Netinteractive\Elegant\Model\Blueprint AS BaseBluePrint;
     use Netinteractive\Elegant\Search\Searchable;

     class Blueprint extends BaseBluePrint
     {
        protected function init()
         {
             $this->setStorageName('users');
             $this->primaryKey = array('id');
             $this->incrementingPk = 'id';

             $this->getRelationManager()->hasOne('patient','Patient', 'user__id','id');

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
                 )
             );

             return parent::init();
         }
     }


### Patient
    <?php namespace Core2\Models\Patient;

    use Netinteractive\Elegant\Model\Blueprint AS BaseBluePrint;
    use Netinteractive\Elegant\Search\Searchable;

    class Blueprint extends BaseBluePrint
    {
       protected function init()
        {
            $this->setStorageName('patient');
            $this->primaryKey = array('id');
            $this->incrementingPk = 'id';

            $this->getRelationManager()->hasMany('patientData','PatientData', array('patient__id'), array('id') );
            $this->getRelationManager()->belongsTo('user','User', array('user__id'), array('id') );

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
                    'title' => _('Id użytkownika'),
                    'type' => 'int',
                    'searchable' => '<',
                    'rules' => array(
                        'any' => 'integer',
                    )
                ),
                'pesel' => array(
                    'title' => _('Pesel'),
                    'type' => 'string',
                    'sortable' => true,
                    'searchable' => Searchable::$contains,
                    'rules' => array(

                    )
                )
            );

            return parent::init();
        }
    }

### PatientData

    <?php namespace Core2\Models\PatientData;

    use Netinteractive\Elegant\Model\Blueprint AS BaseBluePrint;
    use Netinteractive\Elegant\Search\Searchable;

    class Blueprint extends BaseBluePrint
    {
       protected function init()
        {
            $this->setStorageName('patient_data');
            $this->primaryKey = array('id');
            $this->incrementingPk = 'id';

            #Seting up relations
            $this->getRelationManager()->belongsTo('patient','Patient', array('patient__id'), array('id'));


            #Seting up fields
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
                    'title' => _('Patient id'),
                    'type' => 'int',
                    'rules' => array(
                        'any' => 'required|integer|exists:patient,id',
                    )
                ),
                'patient__pesel' => array(
                    'title' => _('Pesel'),
                    'type' => 'string',
                    'sortable' => true,
                    'searchable' => Searchable::$ends,
                    'rules' => array(

                    )
                ),
                'first_name' => array(
                    'title' => _('First name'),
                    'type' => 'string',
                    'sortable' => true,
                    'searchable' => Searchable::$ends,
                    'rules' => array(
                        'any' => 'required|max:100'
                    ),
                    'filters' => array(
                        'fill' => array(
                            'stripTags'
                        )
                    )
                ),
                'last_name' => array(
                    'title' => _('Last name'),
                    'type' => 'string',
                    'sortable' => true,
                    'searchable' => Searchable::$ends,
                    'rules' => array(
                        'any' => 'required|max:100'
                    ),
                    'filters' => array(
                        'fill' => array(
                            'stripTags'
                        )
                    )
                ),
                'birth_date' => array(
                    'title' => _('Birth date'),
                    'type' => 'date',
                    'searchable' => '=',
                    'rules' => array(
                        'any' => 'required|date',
                    )
                ),
                'phone' => array(
                    'title' => _('Phone'),
                    'type' => 'string',
                    'sortable' => true,
                    'searchable' => Searchable::$ends,
                    'rules' => array(
                        'any' => 'required|phone|max:20'
                    ),
                    'filters' => array(
                        'fill' => array(
                            'phone', 'stripTags'
                        )
                    )
                ),
            );

            return parent::init();
        }
    }

## Changelog