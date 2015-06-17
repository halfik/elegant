# Model

Elegant model consists of two parts: Blueprint and Record. Model is separate from the data source. Mappers allows access to different data sources.
So single model can work with many data sources. It allows in example to read data from xml(data source 1)  and then just save them to database (data source 2).

* Blueprint - defines (describes) what record is (data types, validators, informations how to present data and so on)
* Record - keeps single data row and business logic

### Example of user Record
    <?php namespace App\Models\User;

    use Netinteractive\Elegant\Model\Record AS BaseRecord;

    class Record extends BaseRecord
    {
        public function init()
        {
            $this->setBlueprint( Blueprint::getInstance() );
            return $this;
        }
    }



### Example of user Blueprint
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
                )
            );

            return parent::init();
        }
    }