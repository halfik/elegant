<?php
use \Netinteractive\Elegant\Model\Record AS Record;

class HasManyTest  extends ElegantTest
{

    /**
     * @param \Netinteractive\Elegant\Model\Record $related
     * @param \Netinteractive\Elegant\Model\Record $parent
     * @param string $foreignKey
     * @param string $localKey
     * @return \Netinteractive\Elegant\Relation\HasMany
     */
    public function getRelation(Record $related, Record $parent, $foreignKey, $localKey, $builder=null)
    {
        if (!$builder){
            $builder = \App::make('ni.elegant.model.query.builder');
        }

        $relation =  new \Netinteractive\Elegant\Relation\HasMany($builder, $related, $parent, $foreignKey, $localKey);

        return $relation;
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::get
     * @group get
     */
    public function testGet_Call_Query_SetRecord()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);


        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $mockBuilder = $this->getMockBuilder(get_class($dbModelBuilder))
            ->setMethods( array('setRecord', 'getRecord'))
            ->setConstructorArgs( array($connection, $grammar, $processor) )
            ->getMock()
        ;

        $mockBuilder->from('med');

        $mockBuilder
            ->method('getRecord')
            ->willReturn($medRecord)
        ;

        $mockBuilder->expects($this->once())
            ->method('setRecord')
            ->withAnyParameters()
        ;

        $relation = $this->getRelation($patientDataRecord, $medRecord, 'med__id', 'id', $mockBuilder);
        $relation->get();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::get
     * @group get
     */
    public function testGet_Call_Query_From()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);


        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $mockBuilder = $this->getMockBuilder(get_class($dbModelBuilder))
            ->setMethods( array('from', 'getFrom'))
            ->setConstructorArgs( array($connection, $grammar, $processor) )
            ->getMock()
        ;

        $mockBuilder
            ->method('getFrom')
            ->willReturn('patient_data')
        ;

        $this->getPrivateProperty($mockBuilder, 'from')->setValue($mockBuilder, 'patient_data');

        $mockBuilder->expects($this->atLeastOnce())
            ->method('from')
            ->withAnyParameters()
        ;

        $relation = $this->getRelation($patientDataRecord, $medRecord, 'med__id', 'id', $mockBuilder);
        $relation->get();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::get
     * @group get
     */
    public function testGet_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);


        $relation = $this->getRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $response = $relation->get();

        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection',$response);
        $this->assertEquals(2, count($response));
        foreach ($response AS $record){
            $this->assertInstanceOf(get_class($patientDataRecord), $record);
            $this->assertEquals($medRecord->id, $patientDataRecord->med__id);
        }
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::getResults
     * @group get
     */
    public function testGetResults_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->getRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $response = $relation->getResults();

        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection',$response);
        $this->assertEquals(2, count($response));
        foreach ($response AS $record){
            $this->assertInstanceOf(get_class($patientDataRecord), $record);
            $this->assertEquals($medRecord->id, $patientDataRecord->med__id);
        }
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::initRelation
     * @group init
     * @group relation
     */
    public function testInitRelation_Call_Record_SetRelated()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $mockedMedRecord = $this->getMockBuilder(get_class($medRecord))
            ->setMethods( array('setRelated'))
            ->setConstructorArgs( array($medRecord->toArray()) )
            ->getMock()
        ;

        $mockedMedRecord->expects($this->once())
            ->method('setRelated')
            ->withAnyParameters()
        ;

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($mockedMedRecord);

        $relation = $this->getRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $relation->initRelation($collection, 'patients');
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::initRelation
     * @group init
     * @group relation
     */
    public function testInitRelation_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($medRecord);

        $relation = $this->getRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $response = $relation->initRelation($collection, 'patients');

        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection',$response);
        $this->assertInstanceOf(get_class($medRecord),$response[0]);
        $this->assertTrue(isSet($response[0]->patients));
        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection',$response[0]->patients);
        $this->assertTrue(isSet($response[0]->patients[0]));
        $this->assertInstanceOf(get_class($patientDataRecord),$response[0]->patients[0]);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::match
     * @group init
     * @group relation
     */
    public function testMatch_Call_MatchMany()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($medRecord);

        $relation = $this->getRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $mock = $this->getMockBuilder(get_class($relation))
            ->setMethods(array('matchMany'))
            ->setConstructorArgs(array($relation->getQuery(), $patientDataRecord, $medRecord, 'med__id', 'id'))
            ->getMock()
        ;

        $mock->method('matchMany')
            ->withAnyParameters()
        ;

        $collection1 = new \Netinteractive\Elegant\Model\Collection();
        $collection1->add($patientDataRecord);

        $collection2 = new \Netinteractive\Elegant\Model\Collection();
        $collection2->add($medRecord);

        $mock->match($collection2, $collection1, 'patients');
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany
     * @group general
     */
    public function testGeneral()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $record = $dbMapper->with('patientData')->find(1);


        $this->assertTrue(isset($record->patientData));
        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection', $record->patientData);
        $this->assertEquals(2, count($record->patientData));
    }


}