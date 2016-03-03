<?php
use \Netinteractive\Elegant\Model\Record AS Record;

class HasOneOrManyTest extends ElegantTest
{

    /**
     * @param \Netinteractive\Elegant\Model\Record $related
     * @param \Netinteractive\Elegant\Model\Record $parent
     * @param string $foreignKey
     * @param string $localKey
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function mockRelation(Record $related, Record $parent, $foreignKey, $localKey, $builder=null, $methods=array())
    {
        if (!$builder){
            $builder = \App::make('ni.elegant.model.query.builder');
        }

        $mock = $this->getMockBuilder('\Netinteractive\Elegant\Relation\HasOneOrMany')
            ->setMethods( array_merge(array( 'initRelation', 'match', 'getResults'), $methods))
            ->setConstructorArgs(array($builder, $related, $parent, $foreignKey, $localKey))
            ->getMock()
        ;

        $mock->method('initRelation')
            ->withAnyParameters()
        ;

        $mock->method('match')
            ->withAnyParameters()
        ;

        $mock->method('getResults')
            ->withAnyParameters()
        ;

        return $mock;
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::__construct
     * @group relation
     * @group constructor
     */
    public function testConstructor_FkNotArray()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);


        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $fk = $this->getPrivateProperty($relation, 'foreignKey')->getValue($relation);

        $this->assertTrue(is_array($fk));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::__construct
     * @group relation
     * @group constructor
     */
    public function testConstructor_LocalKeyNotArray()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);


        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $lk = $this->getPrivateProperty($relation, 'localKey')->getValue($relation);

        $this->assertTrue(is_array($lk));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::__construct
     * @expectedException \Netinteractive\Elegant\Exception\PkFkSizeException
     * @group relation
     * @group constructor
     */
    public function testConstructor_KeyDiff()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);


        $this->mockRelation($patientDataRecord, $medRecord, array('med__id'), array('id', 'patient__id'));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getLocalKey
     * @group get
     * @group key
     */
    public function testGetLocalKey()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);


        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $key = $relation->getLocalKey();


        $this->assertTrue(is_array($key));
        $this->assertEquals('id', $key[0]);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getForeignKey
     * @group get
     * @group key
     */
    public function testGetForeignKey()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);


        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $key = $relation->getForeignKey();

        $this->assertTrue(is_array($key));
        $this->assertEquals('med__id', $key[0]);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getPlainForeignKey
     * @group key
     * @group get
     */
    public function testGetPlainForeignKey()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);


        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $key = $relation->getPlainForeignKey('patient_data.med__id');


        $this->assertEquals('med__id', $key);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getParentKey
     * @group get
     * @group key
     */
    public function testGetParentKey()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $key = $relation->getParentKey();

        $this->assertTrue(is_array($key));
        $this->assertArrayHasKey('id', $key);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getQualifiedParentKeyName
     * @group get
     * @group key
     */
    public function testGetQualifiedParentKeyName()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $key = $relation->getQualifiedParentKeyName();

        $this->assertTrue(is_array($key));
        $this->assertEquals('med.id', $key[0]);
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::addConstraints
     * @group add
     * @group constraint
     */
    public function testAddConstraints_Query_Call_Where()
    {
        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $mockBuilder = $this->getMockBuilder(get_class($dbModelBuilder))
            ->setMethods( array('where'))
            ->setConstructorArgs( array($connection, $grammar, $processor) )
            ->getMock()
        ;

        $mockBuilder->from('med');


        $mockBuilder->expects($this->once())
            ->method('where')
            ->withAnyParameters()
        ;

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $relation->setQuery($mockBuilder);

        $relation->addConstraints();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::addEagerConstraints
     * @group add
     * @group eager
     * @group constraint
     */
    public function testAddEagerConstraints_Query_Call_From()
    {
        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $mockBuilder = $this->getMockBuilder(get_class($dbModelBuilder))
            ->setMethods( array('from'))
            ->setConstructorArgs( array($connection, $grammar, $processor) )
            ->getMock()
        ;

        $mockBuilder->method('from')
            ->willReturn('med')
        ;

        $mockBuilder->expects($this->once())
            ->method('from')
            ->withAnyParameters()
        ;

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $relation->setQuery($mockBuilder);

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($medRecord);

        $relation->addEagerConstraints($collection);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::addEagerConstraints
     * @group add
     * @group eager
     * @group constraint
     */
    public function testAddEagerConstraints_Query_Call_WhereIn()
    {
        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $mockBuilder = $this->getMockBuilder(get_class($dbModelBuilder))
            ->setMethods( array('whereIn'))
            ->setConstructorArgs( array($connection, $grammar, $processor) )
            ->getMock()
        ;


        $mockBuilder->expects($this->once())
            ->method('whereIn')
            ->withAnyParameters()
        ;

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');
        $relation->setQuery($mockBuilder);

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientDataRecord);

        $relation->addEagerConstraints($collection);
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::buildDictionary
     * @group build
     * @group dictionary
     */
    public function testBuildDictionary()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientDataRecord);

        $result = $this->callPrivateMethod($relation, 'buildDictionary', array($collection));

        $this->assertTrue(is_array($result));
        $this->assertTrue(isSet($result[$patientDataRecord->med__id]));
        $this->assertTrue(is_array($result[$patientDataRecord->med__id]));
        $this->assertInstanceOf(get_class($patientDataRecord), $result[$patientDataRecord->med__id][0]);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getRelationValue
     * @group get
     * @group relation
     */
    public function testGetRelationValue_One()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientDataRecord);

        $dict = $this->callPrivateMethod($relation, 'buildDictionary', array($collection));
        $response = $this->callPrivateMethod($relation,'getRelationValue', array($dict,1, 'one'));

        $this->assertInstanceOf(get_class($patientDataRecord), $response);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getRelationValue
     * @group get
     * @group relation
     */
    public function testGetRelationValue_Many()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientDataRecord);

        $dict = $this->callPrivateMethod($relation, 'buildDictionary', array($collection));
        $response = $this->callPrivateMethod($relation,'getRelationValue', array($dict,1, 'many'));

        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection', $response);
        $this->assertInstanceOf(get_class($patientDataRecord), $response[0]);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::matchOneOrMany
     * @group match
     * @group related
     */
    public function testMatchOneOrMany_Call_BuildDictionary()
    {

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id', null, array('buildDictionary'));
        $relation
            ->method('buildDictionary')
            ->willReturn(array())
        ;

        $relation->expects($this->once())
            ->method('buildDictionary')
            ->withAnyParameters()
        ;

        $collection1 = new \Netinteractive\Elegant\Model\Collection();
        $collection1->add($patientDataRecord);

        $collection2 = new \Netinteractive\Elegant\Model\Collection();
        $collection2->add($medRecord);

        $this->callPrivateMethod($relation, 'matchOneOrMany', array($collection2, $collection1, 'patients', 'many'));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::matchOneOrMany
     * @group match
     * @group related
     */
    public function testMatchOneOrMany_Call_Record_SetRelated()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');

        $mockedMedRecord = $this->getMockBuilder(get_class($medRecord))
            ->setMethods( array('setRelated'))
            ->setConstructorArgs( array($medRecord->toArray()) )
            ->getMock()
        ;

        $mockedMedRecord->expects($this->once())
            ->method('setRelated')
            ->withAnyParameters()
        ;

        $collection1 = new \Netinteractive\Elegant\Model\Collection();
        $collection1->add($patientDataRecord);

        $collection2 = new \Netinteractive\Elegant\Model\Collection();
        $collection2->add($mockedMedRecord);

        $this->callPrivateMethod($relation, 'matchOneOrMany', array($collection2, $collection1, 'patients', 'many'));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::matchOneOrMany
     * @group match
     * @group related
     */
    public function testMatchOneOrMany_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id');

        $collection1 = new \Netinteractive\Elegant\Model\Collection();
        $collection1->add($patientDataRecord);

        $collection2 = new \Netinteractive\Elegant\Model\Collection();
        $collection2->add($medRecord);

        $result = $this->callPrivateMethod($relation, 'matchOneOrMany', array($collection2, $collection1, 'patient', 'one'));

        $this->assertInstanceOf(get_class($collection1), $result);
        $this->assertTrue(isSet($result[0]));
        $this->assertInstanceOf(get_class($medRecord), $result[0]);
        $this->assertTrue(isSet($result[0]->patient));
        $this->assertInstanceOf(get_class($patientDataRecord), $result[0]->patient);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::matchOne
     * @group match
     * @group related
     */
    public function testMatchOne_Call_MatchOneOrMany()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id', null, array('matchOneOrMany'));

        $collection1 = new \Netinteractive\Elegant\Model\Collection();
        $collection1->add($patientDataRecord);

        $collection2 = new \Netinteractive\Elegant\Model\Collection();
        $collection2->add($medRecord);

        $this->callPrivateMethod($relation, 'matchOne', array($collection2, $collection1, 'patient'));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::matchMany
     * @group match
     * @group related
     */
    public function testMatchMany_CallMatchOneOrMany()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

        $relation = $this->mockRelation($patientDataRecord, $medRecord, 'med__id', 'id', null, array('matchOneOrMany'));

        $collection1 = new \Netinteractive\Elegant\Model\Collection();
        $collection1->add($patientDataRecord);

        $collection2 = new \Netinteractive\Elegant\Model\Collection();
        $collection2->add($medRecord);

         $this->callPrivateMethod($relation, 'matchMany', array($collection2, $collection1, 'patient'));
    }

    /**
     *  @covers \Netinteractive\Elegant\Relation\HasOneOrMany::create
     */
    public function testCreate(){
        $this->markTestIncomplete();
    }

    /**
     *  @covers \Netinteractive\Elegant\Relation\HasOneOrMany::createMany
     */
    public function testCreateMany(){
        $this->markTestIncomplete();
    }

}