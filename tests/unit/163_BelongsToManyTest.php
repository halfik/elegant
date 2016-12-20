<?php

class BelongsToManyTest extends ElegantTest
{
    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::__construct
     * @group relation
     * @group constructor
     */
    public function testConstructor_FkNotArray()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');
        $fk = $this->getPrivateProperty($relation, 'foreignKey')->getValue($relation);

        $this->assertTrue(is_array($fk));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::__construct
     * @group relation
     * @group constructor
     */
    public function testConstructor_OtherKeyNotArray()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');
        $ok = $this->getPrivateProperty($relation, 'otherKey')->getValue($relation);

        $this->assertTrue(is_array($ok));
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getTable
     * @group get
     * @group table
     */
    public function testGetTable()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $this->assertEquals('patient_data', $relation->getTable());
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getForeignKey
     * @group get
     * @group fk
     */
    public function testGetForeignKey()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $fk = $relation->getForeignKey();
        $this->assertTrue(is_array($fk));
        $this->assertEquals('med__id', $fk[0]);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getQualifiedForeignKey
     * @group get
     * @group fk
     */
    public function testGetQualifiedForeignKey()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $fk = $relation->getQualifiedForeignKey();
        $this->assertTrue(is_array($fk));
        $this->assertEquals('patient_data.med__id', $fk[0]);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getOtherKey
     * @group get
     * @group key
     */
    public function testGetOtherKey()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $key = $relation->getOtherKey();

        $this->assertTrue(is_array($key));
        $this->assertEquals('patient__id', $key[0]);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getQualifiedOtherKey
     * @group get
     * @group key
     */
    public function testGetQualifiedOtherKey()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $key = $relation->getQualifiedOtherKey();
        $this->assertTrue(is_array($key));
        $this->assertEquals('patient_data.patient__id', $key[0]);
    }




    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::setJoin
     * @group relation
     * @group join
     * @group set
     */
    public function testSetJoin_Call_Query_Join()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();


        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $mockBuilder = $this->getMockBuilder(get_class($dbModelBuilder))
            ->setMethods( array('join'))
            ->setConstructorArgs( array($connection, $grammar, $processor) )
            ->getMock()
        ;

        $mockBuilder->from('patient_data');


        $mockBuilder->method('join')
            ->withAnyParameters()
        ;

        $mockBuilder->expects($this->atLeastOnce())
            ->method('join')
            ->withAnyParameters()
        ;

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($mockBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');
        $this->callPrivateMethod($relation, 'setJoin');
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::setJoin
     * @group relation
     * @group join
     * @group set
     */
    public function testSetJoin_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');
        $response = $this->callPrivateMethod($relation, 'setJoin');

        $this->assertInstanceOf(get_class($relation) ,$response);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::setJoin
     * @group relation
     * @group join
     * @group set
     */
    public function testSetJoin_Result()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $dbModelBuilder->from('patient');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');
        $this->callPrivateMethod($relation, 'setJoin');

        $this->assertEquals('select * from "patient" inner join "patient_data" on "patient"."id" = "patient_data"."patient__id" where ("patient_data"."med__id" = ?)', $relation->getQuery()->toSql());
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::setWhere
     * @group where
     * @group set
     */
    public function testSetWhere_Result()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');


        $this->callPrivateMethod($relation, 'setWhere');

        $sql = 'select * inner join "patient_data" on "patient"."id" = "patient_data"."patient__id" where ("patient_data"."med__id" = ? and "patient_data"."med__id" = ?)';
        $this->assertEquals($sql, $relation->getQuery()->toSql());
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::addEagerConstraints
     * @group add
     * @group eager
     * @group constraint
     */
    public function testAddEagerConstraints_Call_Query_From()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);


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


        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($mockBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $relation->addEagerConstraints($collection);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::addEagerConstraints
     * @group add
     * @group eager
     * @group constraints
     */
    public function testAddEagerConstraints_Query_WhereIn()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

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

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($mockBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $relation->addEagerConstraints($collection);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::addEagerConstraints
     * @group add
     * @group eager
     * @group constraints
     */
    public function testAddEagerConstraints_Result()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');


        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($medRecord);

        $relation->addEagerConstraints($collection);

        $sql = 'select * from "patient" inner join "patient_data" on "patient"."id" = "patient_data"."patient__id" where ("patient_data"."med__id" = ? and "patient_data"."med__id" in (?))';

        $this->assertEquals($sql, $relation->getQuery()->toSql());
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::initRelation
     * @group relation
     * @group init
     */
    public function testInitRelation_Record_SetRelated()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $mockRecord  = $this->getMockBuilder(get_class($patientRecord))
            ->setMethods( array('setRelated'))
            ->setConstructorArgs( array($patientRecord->toArray()) )
            ->getMock()
        ;

        $mockRecord->expects($this->once())
            ->method('setRelated')
            ->withAnyParameters()
        ;

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($mockRecord);

        $relation->initRelation($collection, 'patients');
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::initRelation
     * @group relation
     * @group init
     */
    public function testInitRelation_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $response = $relation->initRelation($collection, 'patients');
        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection', $response);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getSelectColumns
     * @group columns
     * @group get
     */
    public function testGetAliasedPivotColumns()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $response = $this->callPrivateMethod($relation, 'getAliasedPivotColumns');

        $this->assertTrue(is_array($response));
        $this->assertEquals(2, count($response));
        $this->assertEquals('patient_data.med__id as pivot_med__id', $response[0]);
        $this->assertEquals('patient_data.patient__id as pivot_patient__id', $response[1]);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getSelectColumns
     * @group columns
     * @group pivot
     * @group get
     */
    public function testGetSelectColumns_Call_GetAliasedPivotColumns()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation  = $this->getMockBuilder('\Netinteractive\Elegant\Relation\BelongsToMany')
            ->setMethods( array('getAliasedPivotColumns'))
            ->setConstructorArgs( array($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id') )
            ->getMock()
        ;

        $relation->expects($this->once())
            ->method('getAliasedPivotColumns')
            ->withAnyParameters()
            ->willReturn(array())
        ;


        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $this->callPrivateMethod($relation, 'getSelectColumns');
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getSelectColumns
     * @group columns
     * @group get
     */
    public function testGetSelectColumns_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');


        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $response = $this->callPrivateMethod($relation, 'getSelectColumns');

        $this->assertTrue(is_array($response));
        $this->assertEquals(3, count($response));
        $this->assertEquals('patient.*', $response[0]);
        $this->assertEquals('patient_data.med__id as pivot_med__id', $response[1]);
        $this->assertEquals('patient_data.patient__id as pivot_patient__id', $response[2]);
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::cleanPivotAttributes
     * @group pivot
     * @group attribute
     * @group clear
     */
    public function testCleanPivotAttributes()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');


        $patientRecord->pivot_med__id = 1;
        $patientRecord->pivot_patient__id = 1;

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');
        $response = $this->callPrivateMethod($relation, 'cleanPivotAttributes', array($patientRecord));


        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('med__id', $response);
        $this->assertArrayHasKey('patient__id', $response);
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::createNewPivot
     * @group pivot
     * @group create
     */
    public function testCreateNewPivot_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');


        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');
        $response = $relation->createNewPivot(array(
            'med__id' => 1,
            'patient__id' => 1
        ));

        
        $this->assertTrue(isSet($response->med__id));
        $this->assertTrue(isSet($response->patient__id));
        $this->assertEquals(1, $response->med__id);
        $this->assertEquals(1, $response->patient__id);
        $this->assertInstanceOf('Netinteractive\Elegant\Relation\Pivot', $response);
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::newExistingPivot
     * @group pivot
     * @group create
     */
    public function testNewExistingPivot_Call_CreateNewPivot()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');
        $response = $relation->newExistingPivot(array(
            'med__id' => 1,
            'patient__id' => 1
        ));

        $this->assertTrue($response->exists());
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::hydratePivotRelation
     * @group get
     * @group hydrate
     */
    public function testHydratePivotRelation_Call_CleanPivotAttributes()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');



        $relation = $this->getMockBuilder('\Netinteractive\Elegant\Relation\BelongsToMany')
            ->setMethods( array('cleanPivotAttributes'))
            ->setConstructorArgs( array($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id') )
            ->getMock()
        ;

        $relation->method('cleanPivotAttributes')
            ->withAnyParameters()
            ->willReturn(
                array(
                    'med__id'=>1,
                    'patient__id'=>1
                )
            );


        $relation->expects($this->once())
            ->method('cleanPivotAttributes')
            ->withAnyParameters()
        ;


        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $this->callPrivateMethod($relation, 'hydratePivotRelation', array($collection));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::hydratePivotRelation
     * @group get
     * @group hydrate
     */
    public function testHydratePivotRelation_Call_NewExistingPivot()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');


        $relation = $this->getMockBuilder('\Netinteractive\Elegant\Relation\BelongsToMany')
            ->setMethods( array('newExistingPivot'))
            ->setConstructorArgs( array($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id') )
            ->getMock()
        ;

        $relation->method('newExistingPivot')
            ->withAnyParameters()
            ->willReturn(
                new \Netinteractive\Elegant\Relation\Pivot($medRecord, array(), 'patient_data')
            );


        $relation->expects($this->once())
            ->method('newExistingPivot')
            ->withAnyParameters()
        ;

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $this->callPrivateMethod($relation, 'hydratePivotRelation', array($collection));
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::hydratePivotRelation
     * @group get
     * @group hydrate
     */
    public function testHydratePivotRelation_Record_Call_SetRelated()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $mockRecord = $this->getMockBuilder(get_class($patientRecord))
            ->setMethods(array('setRelated'))
            ->setConstructorArgs( array($patientRecord->toArray()) )
            ->getMock()
        ;

        $mockRecord->method('setRelated')
            ->withAnyParameters()
            ;


        $mockRecord->expects($this->once())
            ->method('setRelated')
            ->withAnyParameters()
        ;

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($mockRecord);

        $this->callPrivateMethod($relation, 'hydratePivotRelation', array($collection));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::get
     * @group get
     * @group hydrate
     */
    public function testGet_Call_Query_SetRecord()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $mockBuilder = $this->getMockBuilder(get_class($dbModelBuilder))
            ->setMethods( array('setRecord', 'getRecord'))
            ->setConstructorArgs( array($connection, $grammar, $processor) )
            ->getMock()
        ;

        $mockBuilder->from('patient');


        $mockBuilder->method('getRecord')
            ->withAnyParameters()
            ->willReturn($patientRecord);


        $mockBuilder->expects($this->once())
            ->method('setRecord')
            ->withAnyParameters()
        ;

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($mockBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');
        $relation->get();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::get
     * @group get
     * @group hydrate
     */
    public function testGet_Call_Query_AddSelect()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $mockBuilder = $this->getMockBuilder(get_class($dbModelBuilder))
            ->setMethods( array('addSelect'))
            ->setConstructorArgs( array($connection, $grammar, $processor) )
            ->getMock()
        ;

        $mockBuilder->from('patient');

        $mockBuilder->method('addSelect')
            ->withAnyParameters()
            ->willReturn($mockBuilder->getQuery())
        ;

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($mockBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');
        $relation->get();
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::get
     * @group get
     */
    public function testGet_Call_Query_EagerLoadRelations()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');
        $mockBuilder = $this->getMockBuilder(get_class($dbModelBuilder))
            ->setMethods( array('eagerLoadRelations'))
            ->setConstructorArgs( array($connection, $grammar, $processor) )
            ->getMock()
        ;

        $mockBuilder->from('patient');

        $mockBuilder->method('eagerLoadRelations')
            ->withAnyParameters()
            ->willReturn(new \Netinteractive\Elegant\Model\Collection())
        ;

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($mockBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');
        $relation->get();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::get
     * @group get
     */
    public function testGet_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);


        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');


        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');
        $response = $relation->get();


        $this->assertInstanceOf('Netinteractive\Elegant\Model\Collection', $response);
        $this->assertEquals(2, count($response));
        $this->assertTrue(isSet($response[0]->pivot));
        $this->assertInstanceOf('Netinteractive\Elegant\Relation\Pivot',$response[0]->pivot);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getResults
     * @group get
     */
    public function testGetResults_CallGet()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation  = $this->getMockBuilder('\Netinteractive\Elegant\Relation\BelongsToMany')
            ->setMethods( array('get'))
            ->setConstructorArgs( array($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id') )
            ->getMock()
        ;

        $relation->expects($this->once())
            ->method('get')
            ->withAnyParameters()
            ->willReturn(array())
        ;

        $relation->getResults();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::buildDictionary
     * @group build
     * @group dictionary
     */
    public function testBuildDictionary_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $pivot = new \Netinteractive\Elegant\Relation\Pivot($medRecord, array('med__id'=>1, 'patient__id'=>1), 'patient_data');
        $patientRecord->pivot = $pivot;


        $collection->add($patientRecord);

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');
        $response = $this->callPrivateMethod($relation, 'buildDictionary', array($collection));


        $this->assertTrue(is_array($response));
        $this->assertTrue(isSet($response[1]));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::match
     * @group match
     */
    public function testMatch_Record_Call_SetRelated()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $collection1 = new \Netinteractive\Elegant\Model\Collection();
        $collection2 = new \Netinteractive\Elegant\Model\Collection();

        $pivot = new \Netinteractive\Elegant\Relation\Pivot($medRecord, array('med__id'=>1, 'patient__id'=>1), 'patient_data');
        $patientRecord->pivot = $pivot;


        $muckMedRecord  = $this->getMockBuilder(get_class($medRecord))
            ->setMethods( array('setRelated'))
            ->setConstructorArgs( array($medRecord->toArray()) )
            ->getMock()
        ;

        $muckMedRecord->expects($this->once())
            ->method('setRelated')
            ->withAnyParameters()
            ->willReturn(array())
        ;

        $collection1->add($patientRecord);
        $collection2->add($muckMedRecord);

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $relation->match($collection2, $collection1, 'patient');
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::match
     * @group match
     */
    public function testMatch_Response()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $collection1 = new \Netinteractive\Elegant\Model\Collection();
        $collection2 = new \Netinteractive\Elegant\Model\Collection();

        $pivot = new \Netinteractive\Elegant\Relation\Pivot($medRecord, array('med__id'=>1, 'patient__id'=>1), 'patient_data');
        $patientRecord->pivot = $pivot;


        $collection1->add($patientRecord);
        $collection2->add($medRecord);

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $response = $relation->match($collection2, $collection1, 'patient');

        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection', $response);
        $this->assertTrue(isSet($response[0]));
        $this->assertTrue(isSet($response[0]->patient));
        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection', $response[0]->patient);
        $this->assertTrue(isSet($response[0]->patient[0]));
        $this->assertInstanceOf(get_class($patientRecord), $response[0]->patient[0]);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\createPivotData
     * @group pivot
     * @group create
     */
    public function testCreatePivotData()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany
     * @group relation
     * @group general
     */
    public function testGeneral()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $record = $dbMapper->with('patients')->find(1);

        $this->assertTrue(isset($record->patients));
        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection',$record->patients);
        $this->assertEquals(2, count($record->patients));
    }


}