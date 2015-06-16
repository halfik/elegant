# Netinteractive\Elegant\Model\Blueprint

Class allows to define record fields. In blueprint you define their names, types, validation rules, how to search by this fields and many more.
You can define anything you need that concerns record fields.

Single blueprint is commmon for all records of same class.


Things you should define in init() method:

### Database related
* storage name - data storage name
* primaryKey - it can be single field or multi field PK
* incrementingPk - name part of PK that is autoincremented

### Not related to database
* relations - see example 1 - we use RelationManager object to do this. Record data relations are'nt related to database. Later proper
  relation translator can and will translate this relation to db relations or others (see RelationManager docs for more details).
* fields - here you define list of fields and anything that is related to specific field.


## About fields
* title - is a field title that you should present on views
* type  - type of field. possible values: int, text, date, dateTime, time, bool (and any other you want). Type can be used in example to generate form for record.
* sortable - defines if use can sort data by this field or not: true|false
* searchable - defines if and how we can search by this field (see Searchable.md)
* external - defines if field value should be saved to data storage when you save record
* rules - validation rules. same as you can find in Eloquent.


## Examples

### Example 1

    namespace Core2\Models\Patient;

    use Netinteractive\Elegant\Model\Blueprint AS BaseBluePrint;
    use Netinteractive\Elegant\Search\Searchable;

    class Blueprint extends BaseBluePrint
    {
       protected function init()
        {
            $this->setStorageName('patient');
            $this->primaryKey = array('id', 'pesel');
            $this->incrementingPk = 'id';

            $this->getRelationManager()->hasMany('patientData','PatientData', array('patient__id', 'patient__pesel'), array('id', 'pesel') );


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
                    'searchable' => '=',
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
                ),
                'phone' => array(
                   'title' => _('Phone'),
                   'type' => 'string',
                   'external' => true,
                   'rules' => array(
                        'any' => phone
                   )
               ),


            );

            return parent::init();
        }
    }
