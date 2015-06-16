# Netinteractive\Elegant\Model\Mapper\DbMapper

Class allows to read\write\delete data from database. In most cases you will use standard DbMapper to work with all record classes.
But if there is a need (you need to overwrite some methods) you can make custom DbMapper for specific record.


### Methods
* public function createRecord(array $data = array()) - created record and fill with $data (dosn't save it do database)
* public function getRecordClass() - returns class name of record that mapper is currently working with
* public function setRecordClass($name) - sets class name of record
* public function getBlueprint() - returns blueprint object
* public function delete($ids) - deleted rows from database
* public function save(Record $record) - saves record to database



### Examples:

Getting a record (with related patientData data) :

     $dbMapper = new DbMapper('Patient');
     $record = $dbMapper
            ->with('patientData')
            ->find('1')
            ;

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

Getting a collection:

     $dbMapper = new DbMapper('Patient');
     $collection = $dbMapper
            ->with('patientData')
            ->where('first_name', 'LIKE', 'Jan%')
            ->get()
            ;

Creating a record (it dosn't save record do data storage):

    $dbMapper = new DbMapper('Patient');
    $patient = $dbMapper->createRecord(
        array(
            'first_name' => 'Janet',
            'last_name'  => 'Gold',
            'pesel'      => '123456'
        )
    );

Saving record:

    $dbMapper = new DbMapper('Patient');

    $record = $dbMapper->createRecord(
        array(
            'user__id' => 9,
            'pesel'    => '35101275448',
         )
    );
    $dbMapper->save($record);


Deleting a record:

    $dbMapper = new DbMapper('Patient');
    $patient = $dbMapper->delete(1);

Updating a record:

    $dbMapper = new DbMapper('Patient');
    $record = $dbMapper
            ->with('patientData')
            ->find('1')
            ;

    $record->first_name = 'New name';
    $dbMapper->save($record);

Searching for specific records:

        $searchParams = array(
                'Patient' => array('pesel'=>'26090971975')
        );

        $dbMapper = new DbMapper('Patient');
        $collection = $dbMapper->findMany($searchParams);
