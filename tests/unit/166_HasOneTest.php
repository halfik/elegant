<?php
use \Netinteractive\Elegant\Model\Record AS Record;


class HasOneTest  extends ElegantTest
{

    /**
     * @param \Netinteractive\Elegant\Model\Record $related
     * @param \Netinteractive\Elegant\Model\Record $parent
     * @param string $foreignKey
     * @param string $localKey
     * @return \Netinteractive\Elegant\Relation\HasOne
     */
    public function getRelation(Record $related, Record $parent, $foreignKey, $localKey, $builder=null)
    {
        if (!$builder){
            $builder = \App::make('ni.elegant.model.query.builder');
        }

        $relation =  new \Netinteractive\Elegant\Relation\HasOne($builder, $related, $parent, $foreignKey, $localKey);

        return $relation;
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOne::getResults
     * @group get
     */
    public function testGetResults_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('User');
        $userRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->getRelation(new Netinteractive\Elegant\Tests\Models\Patient\Record(), $userRecord,  'user__id', 'id');
        $response = $relation->getResults();


        $this->assertInstanceOf(get_class($patientDataRecord), $response);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::initRelation
     * @group init
     * @group relation
     */
    public function testInitRelation_Call_Record_SetRelated()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(2);

        $mockedRecord = $this->getMockBuilder(get_class($patientRecord))
            ->setMethods( array('setRelated'))
            ->setConstructorArgs( array($patientRecord->toArray()) )
            ->getMock()
        ;

        $mockedRecord->expects($this->once())
            ->method('setRelated')
            ->withAnyParameters()
        ;

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($mockedRecord);

        $relation = $this->getRelation($patientDataRecord, $patientRecord, 'patient__id', 'id');
        $relation->initRelation($collection, 'patients');
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::initRelation
     * @group init
     * @group relation
     */
    public function testInitRelation_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(2);

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientDataRecord);

        $relation = $this->getRelation($patientDataRecord, $patientRecord, 'patient__id', 'id');
        $response = $relation->initRelation($collection, 'patient');

        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection',$response);
        $this->assertArrayHasKey('patient', $response[0]->toArray());
        $this->assertNull($response[0]->patient);;
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasOne
     * @group general
     */
    public function testGeneral()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('User');
        $record = $dbMapper->with('patient')->find(1);

        $this->assertTrue(isset($record->patient));
        $this->assertInstanceOf('\Netinteractive\Elegant\Tests\Models\Patient\Record', $record->patient);
    }
}