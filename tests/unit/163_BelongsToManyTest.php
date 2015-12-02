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
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
     * @group set2
     */
    public function testSetJoin_Result()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::addEagerConstraints
     * @group add
     * @group eager
     * @group constraint
     */
    public function testAddEagerConstraints_Call_Query_From()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
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
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $medRecord = $dbMapper->find(1);

        $dbMapper->setRecordClass('Patient');
        $patientRecord = $dbMapper->find(2);

        $dbModelBuilder = \App::make('ni.elegant.model.query.builder');

        $relation = new \Netinteractive\Elegant\Relation\BelongsToMany($dbModelBuilder, $patientRecord, $medRecord, 'patient_data', 'med__id', 'patient__id');

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($patientRecord);

        $response = $relation->initRelation($collection, 'patients');
        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection',$response);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getAliasedPivotColumns
     * @group columns
     * @grou pivot
     * @group get
     */
    public function testGetAliasedPivotColumns()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getAliasedPivotColumns
     * @group columns
     * @grou pivot
     * @group get
     */
    public function testGetAliasedPivotColumns_CallGetTable()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getAliasedPivotColumns
     * @group columns
     * @grou pivot
     * @group get
     */
    public function testGetAliasedPivotColumns_ArrayUnique()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getSelectColumns
     * @group columns
     * @group get
     */
    public function testGetSelectColumns_Response()
    {
        $this->markTestIncomplete();
    }




    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getSelectColumns
     * @group columns
     * @group get
     */
    public function testGetSelectColumns_CallGetAliasedPivotColumns()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::get
     * @group get
     * @group hydrate
     */
    public function testGet_CallHydratePivotRelation()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::get
     * @group get
     */
    public function testGet_CallEagerLoadRelations()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::get
     * @group get
     */
    public function testGet_Response()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getResults
     * @group get
     */
    public function testGetResults_CallGet()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::buildDictionary
     * @group match
     */
    public function testBuildDictionary_Response()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::match
     * @group match
     */
    public function testMatch_CallBuildDictionary()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::match
     * @group match
     */
    public function testMatch_Response()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::createNewPivot
     * @group pivot
     */
    public function testCreateNewPivot_CallSetPivotKeys()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::createNewPivot
     * @group pivot
     */
    public function testCreateNewPivot_CallNewPivot()
    {
        $this->markTestIncomplete();
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::createNewPivot
     * @group pivot
     */
    public function testCreateNewPivot_Response()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::newExistingPivot
     * @group pivot
     */
    public function testNewExistingPivot_CallCreateNewPivot()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::setWhere
     * @group where
     * @group set
     */
    public function testSetWhere_CallGetForeignKey()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::setWhere
     * @group where
     * @group set
     */
    public function testSetWhere()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::cleanPivotAttributes
     * @group pivot
     */
    public function testCleanPivotAttributes()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo
     * @group relation
     * @group general
     */
    public function testGeneral()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $record = $dbMapper->with('patients')->find(1);

        $this->assertTrue(isset($record->patients));
        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Collection',$record->patients);
        $this->assertEquals(2, count($record->patients));
    }


}