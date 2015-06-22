# Netinteractive\Elegant\Model\Mapper\DbMapper

Class allows to read\write\delete data from database. In most cases you will use standard DbMapper to work with all record classes.
But if there is a need (you need to overwrite some methods) you can make custom DbMapper for concrete record.

## Events
All events names depends on record class mapper is currently working with.

* ni.elegant.mapper.search.$recordClass    - you can use it to modify search query object. (see Example 9)
* ni.elegant.mapper.saving.$recordClass    - event is fired before record save.
* ni.elegant.mapper.saved.$recordClass     - event is fired after record save.
* ni.elegant.mapper.updating.$recordClass  - event is fired before record update.
* ni.elegant.mapper.updated.$recordClass   - event is fired after record update.
* ni.elegant.mapper.deleting.$recordClass  - event is fired before record is deleted from database.
* ni.elegant.mapper.deleted.$recordClass   - event is fired after record is deleted from database.


## Methods
* createRecord( array $data = array() ) : \Netinteractive\Elegant\Model\Record

        Create a new record and fill it with $data (dosn't save it do database).

* getRecordClass() : string

        Returns class name of record that mapper is currently working with.

* setRecordClass( string $name ) : $this

        Sets record class name.

* getBlueprint() : \Netinteractive\Elegant\Model\Blueprint

        Returns record blueprint object.

* delete( \Netinteractive\Elegant\Model\Record $record ) : int

        Delete row from database.

* save( \Netinteractive\Elegant\Model\Record $record ) : $this

        Saves record to database.

* find( int|array $ids, array $columns=array('*')) : \Netinteractive\Elegant\Model\Record

        Finds single record.

* findMany(array $params, $columns = array('*'), $operator = 'and') :\Netinteractive\Elegant\Model\Collection

        This method allows to search database for records that meet the criteria set out in $params (Example 8). There is a requirement how to name $params.

        $params - As you can find in example 8 $params has to be array of arrays. Main array key is name of record (class name or name you bind in App). Value has to be
        array that contains this record field names (keys) and values you are looking for. Method uses search method to get build proper query object. All information about
        how to search by field is taken from record blueprint.

        $columns - array of column names for select statment

        $operator - 'and' or 'or'. There is no possibility to build query like: "WHERE x=1 AND (y=2 OR c=4)"

        If you need to modify search query object (you want to add joins etc. etc.), there is a event you can use to do so: ni.elegant.mapper.search (Example 9)

* getQuery() : \Netinteractive\Elegant\Db\Query\Builder

        Returns query builder object. This method always sets query builder connection to the one we have in mapper to be sure they are the same.

* setConnection(\Illuminate\Database\ConnectionInterface  $connection) : $this

        Sets connection to database

* getConnection() : \Illuminate\Database\Connection|ConnectionInterface

         Returns database connection

* with( array|string $relations ) : \Netinteractive\Elegant\Db\Query\Builder

        Prepare query builder object that will allow to get records with related records. See Example 2.



## Examples:


### Example 1
Getting a record (with related patientData data) :

     $dbMapper = new DbMapper('Patient');
     $record = $dbMapper
            ->with('patientData')
            ->find('1')
            ;

### Example 2
Getting records with related rows:

    $dbMapper = new DbMapper('PatientData');
    $results = $dbMapper
        ->with('patient.user')
        ->get()
    ;

    $results = $dbMapper
                ->with(array('patient' => function($q){
                   $q->where('patient.pesel', '=', '35101275448');
                }))
                ->get()
    ;

### Example 3
Getting a collection:

     $dbMapper = new DbMapper('Patient');
     $collection = $dbMapper
            ->with('patientData')
            ->where('first_name', 'LIKE', 'Jan%')
            ->get()
            ;

### Example 4
Creating a record (it dosn't save record do data storage):

    $dbMapper = new DbMapper('Patient');
    $patient = $dbMapper->createRecord(
        array(
            'first_name' => 'Janet',
            'last_name'  => 'Gold',
            'pesel'      => '123456'
        )
    );

### Example 5
Saving record:

    $dbMapper = new DbMapper('Patient');

    $record = $dbMapper->createRecord(
        array(
            'user__id' => 9,
            'pesel'    => '35101275448',
         )
    );
    $dbMapper->save($record);


### Example 6
Deleting a record:

    $dbMapper = new DbMapper('Patient');
    $dbMapper->delete(1);

### Example 7
Updating a record:

    $dbMapper = new DbMapper('Patient');
    $record = $dbMapper
            ->with('patientData')
            ->find('1')
            ;

    $record->first_name = 'New name';
    $dbMapper->save($record);

### Example 8
Searching for specific records:

        $searchParams = array(
                'Patient' => array('pesel'=>'26090971975')
        );

        $dbMapper = new DbMapper('Patient');
        $collection = $dbMapper->findMany($searchParams);

### Example 9
Bind event to modify mapper search query (I bind my record as Patient):

In App\Providers\EventServiceProvider.php add this:


    /**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
        'ni.elegant.mapper.search.Patient' => [
            'App\Handlers\Events\Netinteractive\Elegant\Model\ModifySearch',
        ],
	];

Then you can use: php artisan event:generate to generate proper handler.

In App\Handlers\Events\Netinteractive\Elegant\Model you will find ModifySearch.php:

    <?php namespace App\Handlers\Events\Netinteractive\Elegant\Model;

    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Contracts\Queue\ShouldBeQueued;
    use Netinteractive\Elegant\Db\Query\Builder AS Builder;


    class ModifySearch
    {

        /**
         * Create the event handler.
         *
         * @return void
         */
        public function __construct()
        {
            //
        }

        /**
         * Handle the event.
         *
         * @param  Builder  $event
         * @return void
         */
        public function handle(Builder $q)
        {
            $q->join('patient_data', 'patient_data.patient__id', '=', 'patient.id');
        }

    }
