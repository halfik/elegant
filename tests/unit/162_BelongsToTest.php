<?php

class BelongsToTest extends ElegantTest
{
    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::__construct
     * @group relation
     * @group constructor
     */
    public function testConstructor_FkNotArray()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');
        $fk = $this->getPrivateProperty($belongsTo, 'foreignKey')->getValue($belongsTo);

        $this->assertTrue(is_array($fk));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::__construct
     * @group relation
     * @group constructor
     */
    public function testConstructor_OtherKeyNotArray()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');
        $ok = $this->getPrivateProperty($belongsTo, 'otherKey')->getValue($belongsTo);

        $this->assertTrue(is_array($ok));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::__construct
     * @expectedException \Netinteractive\Elegant\Exception\PkFkSizeException
     * @group relation
     * @group constructor
     */
    public function testConstructor_KeyDiff()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, array('user__id'), array('id', 'id2'), 'user');
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::getForeignKey
     * @group get
     * @group fk
     */
    public function testGetForeignKey()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');
        $fk = $belongsTo->getForeignKey();

        $this->assertTrue(is_array($fk));
        $this->assertEquals(1, count($fk));
        $this->assertEquals('user__id', $fk[0]);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::getQualifiedForeignKey
     * @group get
     * @group fk
     */
    public function testGetQualifiedForeignKey()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');
        $fk = $belongsTo->getQualifiedForeignKey();

        $this->assertTrue(is_array($fk));
        $this->assertEquals(1, count($fk));
        $this->assertEquals('patient.user__id', $fk[0]);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::addConstraints
     * @group add
     * @group constraint
     */
    public function testAddConstraints_True()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');
        $belongsTo->addConstraints();


        $this->assertEquals('select * where ("user"."id" = ? and "user"."id" = ?)', $belongsTo->getQuery()->toSql());
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::addConstraints
     * @group add
     * @group constraint
     */
    public function testAddConstraints_False()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');
        $this->getPrivateProperty($belongsTo, 'constraints')->setValue($belongsTo, false);
        $belongsTo->addConstraints();
        $this->getPrivateProperty($belongsTo, 'constraints')->setValue($belongsTo, true);

        $this->assertEquals('select * where ("user"."id" = ?)', $belongsTo->getQuery()->toSql());
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::getOtherKey
     * @group get
     * @group key
     */
    public function testGetOtherKey()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');
        $ok = $belongsTo->getOtherKey();

        $this->assertTrue(is_array($ok));
        $this->assertEquals(1, count($ok));
        $this->assertEquals('id', $ok[0]);
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::getResults
     * @group get
     */
    public function testGetResults_Query_Call_SetRecord()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();


        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $mockBuilder = $this->getMockBuilder(get_class($dbModelBuilder))
            ->setMethods( array('setRecord', 'getRecord'))
            ->setConstructorArgs( array($connection, $grammar, $processor) )
            ->getMock()
        ;

        $mockBuilder->from('user');


        $mockBuilder->method('getRecord')
            ->withAnyParameters()
            ->willReturn($userRecord);


        $mockBuilder->expects($this->once())
            ->method('setRecord')
            ->withAnyParameters()
        ;


        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($mockBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');
        $belongsTo->getResults();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::getResults
     * @group get
     */
    public function testGetResults_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');
        $result = $belongsTo->getResults();

        $this->assertInstanceOf('\Netinteractive\Elegant\Tests\Models\User\Record', $result);
        $this->assertTrue(isSet($result->id));
        $this->assertEquals(1, $result->id);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::get
     * @group get
     */
    public function testGet_SetRecord()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $mockBuilder = $this->getMockBuilder(get_class($dbModelBuilder))
            ->setMethods( array('setRecord', 'getRecord'))
            ->setConstructorArgs( array($connection, $grammar, $processor) )
            ->getMock()
        ;

        $mockBuilder->from('user');

        $mockBuilder->method('getRecord')
            ->withAnyParameters()
            ->willReturn($userRecord);


        $mockBuilder->expects($this->once())
            ->method('setRecord')
            ->withAnyParameters()
        ;

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($mockBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');
        $belongsTo->get();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::get
     * @group get2
     */
    public function testGet_Result()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');
        $results = $belongsTo->get();

        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection', $results);
        $this->assertEquals(1, count($results));
        $this->assertInstanceOf('\Netinteractive\Elegant\Tests\Models\User\Record', $results[0]);
        $this->assertTrue(isSet($results[0]->id));
        $this->assertEquals(1, $results[0]->id);
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::getEagerRecordKeys
     * @group get
     * @group record
     * @group eager
     */
    public function testGetEagerRecordKeys()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');
        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $result = $this->callPrivateMethod($belongsTo,'getEagerRecordKeys', array($collection));

        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
        $this->assertArrayHasKey('user__id', $result);
        $this->assertTrue(is_array($result['user__id']));
        $this->assertEquals(2, $result['user__id'][0]);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::addEagerConstraints
     * @group add
     * @group eager
     * @group constraint
     */
    public function testAddEagerConstraints_Call_GetEagerRecordKeys()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');

        $mockBelongsTo = $this->getMockBuilder(get_class($belongsTo))
            ->setMethods( array('getEagerRecordKeys'))
            ->setConstructorArgs( array($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user') )
            ->getMock()
        ;

        $mockBelongsTo->method('getEagerRecordKeys')
            ->withAnyParameters()
            ->willReturn(array(
                    'user__id' => array(2)
                )
            )
        ;

        $mockBelongsTo->expects($this->once())
            ->method('getEagerRecordKeys')
            ->withAnyParameters()
        ;

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $mockBelongsTo->addEagerConstraints($collection);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::addEagerConstraints
     * @group add
     * @group eager
     * @group constraint
     */
    public function testAddEagerConstraints_Call_Query_From()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);


        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();


        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $mockBuilder = $this->getMockBuilder(get_class($dbModelBuilder))
            ->setMethods( array('getFrom', 'from'))
            ->setConstructorArgs( array($connection, $grammar, $processor) )
            ->getMock()
        ;

        $mockBuilder->method('getFrom')
            ->withAnyParameters()
            ->willReturn('user');


        $mockBuilder->expects($this->once())
            ->method('from')
            ->withAnyParameters()
        ;


        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($mockBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $belongsTo->addEagerConstraints($collection);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::addEagerConstraints
     * @group add
     * @group eager
     * @group constraint
     */
    public function testAddEagerConstraints_Query_WhereIn()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);


        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();


        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $mockBuilder = $this->getMockBuilder(get_class($dbModelBuilder))
            ->setMethods( array('whereIn'))
            ->setConstructorArgs( array($connection, $grammar, $processor) )
            ->getMock()
        ;

        $mockBuilder->method('whereIn')
            ->withAnyParameters()
        ;

        $mockBuilder->expects($this->once())
            ->method('whereIn')
            ->withAnyParameters()
        ;

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($mockBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $belongsTo->addEagerConstraints($collection);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::initRelation
     * @group relation
     * @group init
     */
    public function testInitRelation_SetRelated()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');

        $mockPatientRecord  = $this->getMockBuilder(get_class($patientRecord))
            ->setMethods( array('setRelated'))
            ->setConstructorArgs( array($patientRecord->toArray()) )
            ->getMock()
        ;


        $mockPatientRecord->expects($this->once())
            ->method('setRelated')
            ->withAnyParameters()
        ;

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($mockPatientRecord);

        $belongsTo->initRelation($collection, 'user');
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::initRelation
     * @group relation
     * @group init
     */
    public function testInitRelation_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $response = $belongsTo->initRelation($collection, 'user');

        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection',$response);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::match
     * @group relation
     * @group set
     * @group related
     */
    public function testMatch_Record_SetRelated()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');

        $mockPatientRecord  = $this->getMockBuilder(get_class($patientRecord))
            ->setMethods( array('setRelated'))
            ->setConstructorArgs( array($patientRecord->toArray()) )
            ->getMock()
        ;


        $mockPatientRecord->expects($this->once())
            ->method('setRelated')
            ->withAnyParameters()
        ;

        $collection1 = new \Netinteractive\Elegant\Model\Collection();
        $collection1->add($mockPatientRecord);

        $collection2 = new \Netinteractive\Elegant\Model\Collection();
        $collection2->add($userRecord);

        $belongsTo->match($collection1, $collection2, 'user');
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::match
     * @group relation
     * @group set2
     * @group related
     */
    public function testMatch_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbMapper->setRecordClass('User');
        $userRecord = $dbMapper->find($patientRecord->user__id);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $belongsTo = new \Netinteractive\Elegant\Relation\BelongsTo($dbModelBuilder, $userRecord, $patientRecord, 'user__id', 'id', 'user');

        $collection1 = new \Netinteractive\Elegant\Model\Collection();
        $collection1->add($patientRecord);

        $collection2 = new \Netinteractive\Elegant\Model\Collection();
        $collection2->add($userRecord);

        $response = $belongsTo->match($collection1, $collection2, 'user');

        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection', $response);
        $this->assertTrue(isSet($response[0]->user));
        $this->assertNotNull($response[0]->user);
        $this->assertInstanceOf(get_class($userRecord), $response[0]->user);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::associate
     * @group relation
     */
    public function testAssociate_GetOtherKey()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::associate
     * @group relation
     */
    public function testAssociate_Parent_SetAttribute()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::associate
     * @group relation
     */
    public function testAssociate_Parent_SetRelated()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::associate
     * @group relation
     */
    public function testAssociate_Result()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::dissociate
     * @group relation
     */
    public function testDissociate_Parent_SetAttribute()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::dissociate
     * @group relation
     */
    public function testDissociate_Parent_SetRelated()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::dissociate
     * @group relation
     */
    public function testDissociate_Result()
    {
        $this->markTestIncomplete();
    }



}