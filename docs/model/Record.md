# Netinteractive\Elegant\Model\Record

Record represent a single data row. Example 1 show how basic class will looks like.

* fill( $attributes ) : $this

        Fill record with data (Example 2).

* validate( array $data, $rulesGroups = 'all' ) : $this

        Validates data using record validators (Example 3). It will throw \Netinteractive\Elegant\Exception\ValidationException if validation fails.
        Validation is fired automatically when mapper tries to save record. And it always validate all data, not only those that have been changed.

* setBlueprint( \Netinteractive\Elegant\Model\Blueprint $blueprint) : $this

        Sets record blueprint.

* getBlueprint() : \Netinteractive\Elegant\Model\Blueprint|Null

        Returns record blueprint. Blueprint contains (or it should) all informations about record fields.

* enableValidation() : $this

        Enables data validation.

* disableValidation() : $this

        Disables data validation.

* setAttribute( string $key, string $value )  : $this

        Sets attribute value. If attribute is not defined in Blueprint it won't set it. There are 2 types of attributes (
        when you access to record attribute it dosn't matter what kind it is):

            * external - attributes that belongs to record but aren't stored in data storage
            * attributes - attributes that are stored in data storage

* getAttribute( string $key ) : mixed|null

        Gets attribute value or related rows (Example 4).




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

