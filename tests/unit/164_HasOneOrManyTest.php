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
    public function mockRelation(Record $related, Record $parent, $foreignKey, $localKey, $builder=null)
    {
        if (!$builder){
            $builder = \App::make('ni.elegant.model.query.builder');
        }

        $mock = $this->getMockBuilder('\Netinteractive\Elegant\Relation\HasOneOrMany')
            ->setMethods( array('addConstraints', 'addEagerConstraints', 'initRelation', 'match', 'getResults'))
            ->setConstructorArgs(array($builder, $related, $parent, $foreignKey, $localKey))
            ->getMock()
        ;

        $mock->method('addEagerConstraints')
            ->withAnyParameters()
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
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getParentKey
     * @group get2
     * @group key
     */
    public function testGetParentKey()
    {
        $this->markTestSkipped('There is some problem with mocker and test wont work.');
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('PatientData');
        $patientDataRecord = $dbMapper->find(1);

    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getQualifiedParentKeyName
     * @group get
     * @group key
     */
    public function testGetQualifiedParentKeyName()
    {
        $this->markTestSkipped('There is some problem with mocker and test wont work.');
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::addConstraints
     * @group add
     */
    public function testAddConstraints()
    {
        $this->markTestSkipped('There is some problem with mocker and test wont work.');
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::addEagerConstraints
     * @group add
     * @group eager
     */
    public function testAddEagerConstraints()
    {
        $this->markTestSkipped('There is some problem with mocker and test wont work.');
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getPlainForeignKey
     * @group key
     * @group get
     */
    public function testGetPlainForeignKey()
    {
        $this->markTestSkipped('There is some problem with mocker and test wont work.');
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::buildDictionary
     * @group build
     * @group dictionary
     */
    public function testBuildDictionary()
    {
        $this->markTestSkipped('There is some problem with mocker and test wont work.');
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getRelationValue
     * @group get
     * @group relation
     */
    public function testGetRelationValue_One()
    {
        $this->markTestSkipped('There is some problem with mocker and test wont work.');
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getRelationValue
     * @group get
     * @group relation
     */
    public function testGetRelationValue_Many()
    {
        $this->markTestSkipped('There is some problem with mocker and test wont work.');
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::matchOneOrMany
     * @group add
     * @group eager
     */
    public function testMatchOneOrMany_CallBuildDictionary()
    {
        $this->markTestSkipped('There is some problem with mocker and test wont work.');
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::matchOneOrMany
     * @group add
     * @group eager
     */
    public function testMatchOneOrMany_Response()
    {
        $this->markTestSkipped('There is some problem with mocker and test wont work.');
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::matchOne
     * @group add
     * @group eager
     */
    public function testMatchOne_CallMatchOneOrMany()
    {
        $this->markTestSkipped('There is some problem with mocker and test wont work.');
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::matchMany
     * @group add
     * @group eager
     */
    public function testMatchMany_CallMatchOneOrMany()
    {
        $this->markTestSkipped('There is some problem with mocker and test wont work.');
    }


}