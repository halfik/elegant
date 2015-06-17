# Netinteractive\Elegant\Search\Searchable

This class allow coder to define how to search the field.

Options:

* Searchable::$begins - text type search. in db case type it should build LIKE query statment: '$val%'
* Searchable::$contains - text type search. in db case type it should build LIKE query statment: '%$val%'
* Searchable::$ends - text type search. in db case type it should build LIKE query statment: '%$val'
* Other options: =, <, >, >=, <=, !=


### Example
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