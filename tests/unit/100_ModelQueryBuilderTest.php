<?php


class ModelQueryBuilderTest extends ElegantTest
{
    /**
     * @return \Netinteractive\Elegant\Model\Query\Builder
     */
    protected function newBuilder()
    {
        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        return new \Netinteractive\Elegant\Model\Query\Builder($connection, $grammar, $processor);
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::setRecord
     * @group set
     * @group record
     */
    public function testSetRecord()
    {
        $record  = App::make('Patient');

        $builder = $this->newBuilder();
        $builder->setRecord($record);

        $reflectedProperty = $this->getPrivateProperty($builder, 'record');
        $recordValue = $reflectedProperty->getValue($builder);

        $this->assertTrue($recordValue instanceof \Netinteractive\Elegant\Model\Record);
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::getRecord
     * @group get
     * @group record
     */
    public function testGetRecord()
    {
        $record  = App::make('Patient');

        $builder = $this->newBuilder();
        $builder->setRecord($record);

        $this->assertTrue($builder->getRecord() instanceof \Netinteractive\Elegant\Model\Record);
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::isNested
     * @group is
     * @group relation
     */
    public function testIsNested_True()
    {
        $builder = $this->newBuilder();
        $this->assertTrue($builder->isNested('user.tu', 'user'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::isNested
     * @group is
     * @group relation
     */
    public function testIsNested_False()
    {
        $builder = $this->newBuilder();
        $this->assertFalse($builder->isNested('user.tu', 'patient'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::isNested
     * @group is
     * @group relation
     */
    public function testIsNested_False_NoDots()
    {
        $builder = $this->newBuilder();
        $this->assertFalse($builder->isNested('user', 'user'));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::macro
     * @group set
     * @group macro
     */
    public function testMacro()
    {
        $builder = $this->newBuilder();
        $builder->macro('my_macro', function(){
            return true;
        });

        $reflectedProperty = $this->getPrivateProperty($builder, 'macros');
        $macros = $reflectedProperty->getValue($builder);

        $this->assertTrue(array_key_exists('my_macro', $macros));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::getMacro
     * @group get
     * @group macro
     */
    public function testGetMacro()
    {
        $builder = $this->newBuilder();
        $builder->macro('my_macro', function(){
            return true;
        });

        $macro = $builder->getMacro('my_macro');
        $this->assertTrue(is_callable($macro));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::__call
     * @group call
     * @group macro
     */
    public function test_Call_Macro()
    {
        $builder = $this->newBuilder();
        $builder->macro('my_macro', function(){
            return 'ok';
        });

        $result = $builder->my_macro();
        $this->assertEquals('ok', $result);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::parseNested
     * @group parse
     * @group nested
     * @group relation
     */
    public function testParseNested()
    {
        $builder = $this->newBuilder();
        $response = $this->callPrivateMethod($builder, 'parseNested', array('patient.patientData', array()));

        $this->assertEquals(2, count($response));
        $this->assertArrayHasKey('patient', $response);
        $this->assertArrayHasKey('patient.patientData', $response);

        $closure = $response['patient'];


        $this->assertTrue(is_callable($closure));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::parseRelations
     * @group parse
     * @group nested
     * @group relation
     */
    public function testParseRelations_Call_ParseNested()
    {
        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('parseNested'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mock->expects($this->once())
            ->method('parseNested')
            ->withAnyParameters()
        ;

        $mock->parseRelations(array('relation1'));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::parseRelations
     * @group parse
     * @group nested
     * @group relation
     */
    public function testParseRelations_NumericName()
    {
        $builder = $this->newBuilder();

        $result = $builder->parseRelations(array('relation1'));

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('relation1', $result);
        $this->assertTrue(is_callable($result['relation1']));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::setRelationsToLoad
     * @group set
     * @group relation
     */
    public function testSetRelationsToLoad()
    {
        $builder = $this->newBuilder();
        $builder->setRelationsToLoad(array(1,2,3));

        $reflectedProperty = $this->getPrivateProperty($builder, 'relationsToLoad');
        $relationsToLoad = $reflectedProperty->getValue($builder);

        $this->assertEquals(3, count($relationsToLoad));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::getRelationsToLoad
     * @group get
     * @group relation
     */
    public function testGetRelationsToLoad()
    {
        $builder = $this->newBuilder();
        $builder->setRelationsToLoad(array(1,2,3));

        $this->assertEquals(3, count($builder->getRelationsToLoad()));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::nestedRelations
     * @group load
     * @group relation
     */
    public function testNestedRelations_Call_GetRelationsToLoad()
    {
        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('getRelationsToLoad'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mock->method('getRelationsToLoad')
            ->willReturn(array());

        $mock->expects($this->once())
            ->method('getRelationsToLoad')
            ->withAnyParameters()
        ;

        $mock->nestedRelations('relation1');
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::nestedRelations
     * @group load
     * @group relation
     */
    public function testNestedRelations_Call_IsNested()
    {
        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('isNested'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mock->method('isNested')
            ->willReturn(true);

        $mock->expects($this->once())
            ->method('isNested')
            ->withAnyParameters()
        ;

        $mock->setRelationsToLoad( array('relation.name'=>function(){}));

        $mock->nestedRelations('relation');
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::nestedRelations
     * @group load
     * @group relation
     */
    public function testNestedRelations_Response()
    {
        $builder = $this->newBuilder();
        $builder->setRelationsToLoad( array('relation.nested'=>function(){}));

        $response = $builder->nestedRelations('relation');

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('nested',$response);
        $this->assertTrue(is_callable($response['nested']));
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::whereSoftDeleted
     * @group where
     * @group softDelete
     */
    public function testWhereSoftDeleted()
    {
        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('whereNull'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mock->expects($this->once())
            ->method('whereNull')
            ->withAnyParameters()
        ;

        $record  = App::make('Patient');

        $mock->setRecord($record);
        $mock->whereSoftDeleted();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::getRelation
     * @group get
     * @group relation
     */
    public function testGetRelation_Call_GetRecord()
    {
        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('getRecord'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mock->method('getRecord')
            ->willReturn(App::make('Patient'));


        $mock->expects($this->once())
            ->method('getRecord')
            ->withAnyParameters()
        ;

        $mock->getRelation('patientData');
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::getRelation
     * @group get
     * @group relation
     */
    public function testGetRelation_Call_Record_GetRelation()
    {
        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('whereNull'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $record  = App::make('Patient');
        $mockRecord = $this->getMockBuilder(get_class($record))
            ->setMethods( array('getRelation'))
            ->getMock()
        ;

        $mockRecord->expects($this->once())
            ->method('getRelation')
            ->withAnyParameters()
        ;

        $mock->setRecord($mockRecord);
        $mock->getRelation('patientData');
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::getRelation
     * @group get
     * @group relation
     */
    public function testGetRelation_Result()
    {
        $builder = $this->newBuilder();

        $builder->setRecord(App::make('Patient'));

        $result = $builder->getRelation('patientData');

        $this->assertTrue($result instanceof \Netinteractive\Elegant\Relation\Relation);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::loadRelated
     * @group load
     * @group relation
     */
    public function testLoadRelated_Call_GetRelation()
    {
        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('getRelation'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mock->method('getRelation')
            ->willReturn(App::make('Patient')->getRelation('patientData', 'db'));


        $mock->expects($this->once())
            ->method('getRelation')
            ->withAnyParameters()
        ;

        $mock->setRecord(App::make('Patient'));

        $this->callPrivateMethod($mock, 'loadRelated', array(
            new \Netinteractive\Elegant\Model\Collection(),
            'patientData',
            function(){}
        ));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::eagerLoadRelations
     * @group load
     * @group relation
     */
    public function testEagerLoadRelations_Call_GetRelationsToLoad()
    {
        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('getRelationsToLoad'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mock->method('getRelationsToLoad')
            ->willReturn(array())
        ;

        $mock->expects($this->once())
            ->method('getRelationsToLoad')
            ->withAnyParameters()
        ;

        $mock->eagerLoadRelations(new \Netinteractive\Elegant\Model\Collection());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::eagerLoadRelations
     * @group load
     * @group relation
     */
    public function testEagerLoadRelations_Call_LoadRelated()
    {
        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('getRelationsToLoad', 'loadRelated'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mock->method('getRelationsToLoad')
            ->willReturn(array(
                'patient'=>function(){
                }
            ))
        ;

        $mock->method('loadRelated')
            ->willReturn(array())
        ;

        $mock->expects($this->once())
            ->method('loadRelated')
            ->withAnyParameters()
        ;

        $mock->eagerLoadRelations(new \Netinteractive\Elegant\Model\Collection());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::eagerLoadRelations
     * @group load
     * @group relation
     */
    public function testEagerLoadRelations_Response()
    {
        $builder = $this->newBuilder();
        $response = $builder->eagerLoadRelations(new \Netinteractive\Elegant\Model\Collection());

        $this->assertTrue($response instanceof \Netinteractive\Elegant\Model\Collection);
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::createRecords
     * @group create
     * @group record
     */
    public function testCreateRecords_Response()
    {
        $builder = $this->newBuilder();
        $builder->setRecord(\App::make('Patient'));


        $response = $builder->createRecords();

        $this->assertTrue($response instanceof \Netinteractive\Elegant\Model\Collection);
        $this->assertEquals(2, count($response));
        $this->assertTrue($response[0] instanceof \Netinteractive\Elegant\Tests\Models\Patient\Record);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::get
     * @group get
     */
    public function testGet_Call_WhereSoftDeleted()
    {
        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('whereSoftDeleted'))
            ->setConstructorArgs(array($connection, $grammar, $processor))
            ->getMock()
        ;

        $mock->expects($this->once())
            ->method('whereSoftDeleted')
            ->withAnyParameters()
        ;

        $mock->setRecord(\App::make('Patient'));

        $mock->get();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::get
     * @group get
     */
    public function testGet_Call_CreateRecords()
    {
        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('createRecords'))
            ->setConstructorArgs(array($connection, $grammar, $processor))
            ->getMock()
        ;

        $mock->expects($this->once())
            ->method('createRecords')
            ->withAnyParameters()
        ;

        $mock->method('createRecords')
            ->willReturn(array())
        ;

        $mock->setRecord(\App::make('Patient'));

        $mock->get();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::get
     * @group get
     */
    public function testGet_Call_EagerLoadRelations()
    {
        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('eagerLoadRelations'))
            ->setConstructorArgs(array($connection, $grammar, $processor))
            ->getMock()
        ;

        $mock->expects($this->once())
            ->method('eagerLoadRelations')
            ->withAnyParameters()
        ;

        $mock->method('eagerLoadRelations')
            ->willReturn(array())
        ;

        $mock->setRecord(\App::make('Patient'));

        $mock->get();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::get
     * @group get
     */
    public function testGet_Response()
    {
        $builder = $this->newBuilder();
        $builder->setRecord(\App::make('Patient'));

        $response = $builder->get();

        $this->assertTrue($response instanceof \Netinteractive\Elegant\Model\Collection);
        $this->assertEquals(2, count($response));
        $this->assertTrue($response[0] instanceof \Netinteractive\Elegant\Tests\Models\Patient\Record);
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::with
     * @group with
     */
    public function testWith_Call_SetRelationsToLoad()
    {
        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('setRelationsToLoad', 'addConstraints'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mock->method('addConstraints')
            ->willReturn(array())
        ;

        $mock->expects($this->once())
            ->method('setRelationsToLoad')
            ->withAnyParameters()
        ;

        $mock->setRecord(\App::make('Patient'));

        $mock->with('patientData');
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::with
     * @group with
     */
    public function testWith_Call_GetRelationsToLoad()
    {
        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('getRelationsToLoad'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mock->method('getRelationsToLoad')
            ->willReturn(array())
        ;

        $mock->expects($this->once())
            ->method('getRelationsToLoad')
            ->withAnyParameters()
        ;

        $mock->setRecord(\App::make('Patient'));

        $mock->with('patientData');
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::with
     * @group with
     */
    public function testWith_Call_ParseRelations()
    {
        $builder = $this->newBuilder();
        $mock = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('parseRelations'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mock->method('parseRelations')
            ->willReturn(array())
        ;

        $mock->expects($this->once())
            ->method('parseRelations')
            ->withAnyParameters()
        ;

        $mock->setRecord(\App::make('Patient'));

        $mock->with('patientData');
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::with
     * @group with
     */
    public function testWith_Response()
    {
        $builder = $this->newBuilder();


        $builder->setRecord(\App::make('Patient'));

        $response = $builder->with('patientData');

        $this->assertTrue($response instanceof \Netinteractive\Elegant\Model\Query\Builder);
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Query\Builder::apply
     * @group filter
     * @group save
     */
    public function testGetSoftDelete()
    {
        DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $record = $dbMapper->find(1);

        $dbMapper->delete( $record );

        $results = $dbMapper->get();

        $this->assertEquals(1, count($results));

        DB::rollback();
    }

}