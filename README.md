Netinteractive\Elegant
======================

Elegant is a domain model package. He is similar to laravels Eloquent and we used a lot of Eloquent code to build this package.


## Services
* ElegantServiceProvider - registers in App most important classes:
     * ni.elegant.model.mapper.db - class that allows to work with databases.
     * ni.elegant.db.query.builder - database query builder.
     * ni.elegant.model.query.builder - model query builder. Responsible for relations.
     * ni.elegant.model.collection - data collection class
     * ni.elegant.model.relation.manager- class that allows to register relations translators for different data sources. ElegantServiceProvider register standard db translator.
     * ni.elegant.model.relation.translator.db - class that knows how to build database relations based on informations from blueprint
     * ni.elegant.search.db.translator - class that knows how to build and add to query proper where statements based on blueprint informations.


## Events
* Netinteractive\Elegant\Model\Mapper\DbMapper

        ni.elegant.mapper.search.$recordClass    - event is fired in search method. You can use it to modify search query object.
        ni.elegant.mapper.saving.$recordClass    - event is fired before record is save to database.
        ni.elegant.mapper.saved.$recordClass     - event is fired after record is save to database.
        ni.elegant.mapper.updating.$recordClass  - event is fired  before record update on database.
        ni.elegant.mapper.updated.$recordClass   - event is fired after record update on database.
        ni.elegant.mapper.creating.$recordClass  - event is fired  before record is inserted to database.
        ni.elegant.mapper.created.$recordClass   - event is fired after record is inserted to database.
        ni.elegant.mapper.deleting.$recordClass  - event is fired before record is deleted from database.
        ni.elegant.mapper.deleted.$recordClass   - event is fired after record is deleted from database.


*  Netinteractive\Elegant\Db\Query\Builder

        ni.elegant.db.builder.modify           - event allows to modify query before execution.

## Important
There is the requirement for naming foreign keys. If you wont meet this requirement - relations won't work.
In example if you have patient_data table where you have foreign key from patient table it has to be named: patient__id (related: table_name + __ + field_name)
Also we recommend pivot table names to looke like this: table_one__table_two


## Package versioning

x.y.z -
    x - a whole new version of package, where architecture is completely changed
    y - we change it always when package is no more compatible with lasted version (no matter why)
    z - we chage it when we fix bug or add new features and package is still compatible

## Documentation

In docs folder you can find more documentation about package.


All examples are based on this 3 classes:

### Binding
    \App::bind('Patient', 'App\Models\Patient\Record');
    \App::bind('PatientData', 'App\Models\PatientData\Record');
    \App::bind('User', 'App\Models\User\Record');


### User
     <?php namespace App\Models\User;

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
    <?php namespace App\Models\Patient;

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

    <?php namespace App\Models\PatientData;

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