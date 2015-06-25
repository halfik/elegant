# Netinteractive\Elegant\Model\Record

Record represent a single data row. Example 1 show how basic class will looks like. Use getBluePrint() to get to any informations about fields.

## Blueprint and attributes

Record doesn't know anything about attributes. We use Blueprint object to keep this informations.

There are 2 types of record attributes. Ones that should be saved to data source and ones that belong to record, but shouldn't be saved.
First one is an attribute, second one is an external attribute. List of both are defined in blueprint. Defined external attributes only when you need validate them when saving the record.
Every attribute that is not defined in record blueprint by default is treated as external.


## Filters (data manipulators)

We created a mechanism that allows programmer to manipulate record data on 3 different levels:

* you can modify data when the record is filled
* you can modify data when the record is save to data source
* you can modify data when it's displayed

It's not a default mechanism. You have to add 'Netinteractive\Elegant\FiltersServiceProvider' to your application providers list.

All filters are defined in config file. You can easy add you own filters any time you want. Config file is published in:

        config/netinteractive/elegant/filters.php

or you can define them inline in blueprints (Example 5)

Because L5 cache configs we are not allowed to define anonymous functions in configs. We solved this problem by using jeremeamia/superclosure package, that allows to serialize and unserialize functions.

### Fill filter

It is fired in fill method. It won't work if you assign data to record any other way (we decided that sometimes programmer would want to bypass this filter mechanism).
So each time you fill record by fill method (notice that when you read data from data source it is always filled by this method) you will allow data to by modify before it's
assigned to record object. In Example 6 you can find how to define filters.

### Save filter

This filter doesn't modify record itself. It allows to modify data that are pass to data source. Each time we want to save record, a copy of record data is pass to data source.
Save filters modify copy. Good example here would be floating-point numbers notation. Lets say we have a price field on model that is decimal. We need to keep it decimal to be able to do
math on price (sum 2 prices etc. etc.) but we need to save this data source with "," instead "." decimals separator. Then we can use save filter.

### Display filter

Display filters modify data when you display it. There is a special function for this: display (example 7). We could fire this filter any time you try to get field value but it would be inflexible.
In example 5 and 6 you can see how we can use date filter to modify how date is presented by default. Sometimes you will have to present same date differently from default. If this is a case
then you can apply different filters (Example 8).




###Blueprint

* setBlueprint( \Netinteractive\Elegant\Model\Blueprint $blueprint) : $this

        Sets record blueprint.

* getBlueprint() : \Netinteractive\Elegant\Model\Blueprint|Null

        Returns record blueprint. Blueprint contains (or it should) all informations about record fields.

* hasBlueprint() : bool

        Method checks if record has a blueprint

###Validation

* enableValidation() : $this

        Enables data validation.

* disableValidation() : $this

        Disables data validation.

* validate( array $data, $rulesGroups = 'all' ) : $this

        Validates data using record validators (Example 3). It will throw \Netinteractive\Elegant\Exception\ValidationException if validation fails.
        Validation is fired automatically when mapper tries to save record. And it always validate all data, not only those that have been changed.


###Attributes

* fill( $attributes ) : $this

        Fill record with data (Example 2).

* setAttribute( string $key, string $value )  : $this

        Sets attribute value. If attribute is not defined in Blueprint it won't set it. There are 2 types of attributes (
        when you access to record attribute it dosn't matter what kind it is):

            * external - attributes that belongs to record but aren't stored in data storage
            * attributes - attributes that are stored in data storage

* getAttribute( string $key ) : mixed|null

        Gets attribute value or related rows (Example 4).


* getAttributes() : array

        Returns attributes names and values.

* getAttributesKeys() : array

        Returns list of attributes names

* getExternals() : array

        Returns list of external attributes (attributes that don't belong to this record).

* getOriginals(): array

        Returns list of attributes  in their original state (before any changes were made on record).

* getDirty() : array

        Returns list of dirty attributes (attribute is dirty if its value has been modified).

* makeDirty( array $attributes=array() ) : $this

        Method enables to make attributes considered dirty.

* isDirty( mixed $attributes = null ) : bool

        Determine if the record or given attribute(s) have been modified.


###Relations

* getRelation(string $type, string $relation) : mixed

        Creates and returns relation object

* getRelated(string $name=null) : mixed

        Returns related records

*  hasRelated(string $name=null) : bool

        Checks if record has any related records

* setRelated(string $name, mixed $records) : $this

        Assigns related records to parent

###Data presentation

* display(string $field, array $filters = array(), bool $defaultFilters = true) : mixed

        Apply display filters on attribute value and returns it.
        Usage:
               * display('my_field') - will apply filters defined in blueprint
               * display('my_field', array('additional_filter')) - apply filters defined in blueprint and passed in $filters params in that order.
               * display('my_field', array('additional_filter'), false) - will apply only passsed filters




## Examples

### Example 1

    <?php namespace App\Models\Patient;

    use Netinteractive\Elegant\Model\Record AS BaseRecord;

    class Record extends BaseRecord
    {
        public function init()
        {
            #Blueprint is in same namespace as record
            $this->setBlueprint( Blueprint::getInstance() );
            return $this;
        }
    }


### Example 2
    $dbMapper = new DbMapper('Patient');
    $record = $dbMapper->createRecord();

    $record->fill( array(
      'user__id' => 9,
      'pesel'    => '35101275448',
    ));


### Example 3
     $dbMapper = new DbMapper('Patient');
     $record = $dbMapper->createRecord();

     try {
        $record->validate(array(
                                'user__id' => 9,
                                'pesel'    => '35101275448',
              ));
     }
     catch (\Netinteractive\Elegant\Exception\ValidationException $e){
        debug($e->getMessageBag());
     }


### Example 4
    $dbMapper = new DbMapper('Patient');
    #patientData is one to many relation
    $patients = $dbMapper->with('patientData')->get();

    foreach ($patients as $patient){
        echo $patient->pesel.'<br>';
        if (count($patient->patientData)){
            foreach ($patient->patientData as $patientData){
                echo $patientData->phone.'<br>';
            }
        }
    }


### Example 5
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
                            'stripTags',
                            function ($value){
                                return ucfirst($value);
                            }
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
                    ),
                    'filters' => array(
                        'display' => 'date: Y.m.d'
                    )
                ),
                'phone' => array(
                    'title' => _('Phone'),
                    'type' => 'string',
                    'sortable' => true,
                    'searchable' => Searchable::$ends,
                    'rules' => array(
                        'any' => 'required|max:20'
                    ),
                    'filters' => array(
                        'fill' => array(
                            'stripTags'
                        ),
                        'save' => array(
                            'phone'
                        ),
                    )
                ),
            );

            return parent::init();
        }
    }


### Example 6

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
                    ),
                    'filters' => array(
                        'display' => 'date: Y.m.d'
                    )
                ),
                'phone' => array(
                    'title' => _('Phone'),
                    'type' => 'string',
                    'sortable' => true,
                    'searchable' => Searchable::$ends,
                    'rules' => array(
                        'any' => 'required|max:20'
                    ),
                    'filters' => array(
                        'fill' => array(
                            'stripTags'
                        ),
                        'save' => array(
                            'phone'
                        ),
                    )
                ),
            );

            return parent::init();
        }
    }


### Example 8
        $dbMapper = new DbMapper('PatientData');
        $records = $dbMapper->getQuery()->limit(1)->get();

        $records[0]->display('birth_date'); #will apply all display filters on birth_day



### Example 8
        $dbMapper = new DbMapper('PatientData');
        $records = $dbMapper->getQuery()->limit(1)->get();

        echo \DisplayFilter::run( $records[0]->birth_date, array('date: Y'))."<br>"; #will display year


