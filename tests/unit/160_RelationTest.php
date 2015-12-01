<?php

class RelationTest  extends ElegantTest
{

    public function mockRelation()
    {
        $queryBuilder = \App::make('ni.elegant.model.query.builder');
        $record = \App::make('Patient');

        $mock = $this->getMockBuilder('\Netinteractive\Elegant\Relation\Relation')
            ->setMethods( array('addConstraints', 'addEagerConstraints', 'initRelation', 'match', 'getResults'))
            ->setConstructorArgs(array($queryBuilder, $record))
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
     * @covers \Netinteractive\Elegant\Relation\Relation::__construct
     * @group relation
     * @group constructor
     */
    public function testConstructor_CallAddConstraints()
    {
        $queryBuilder = \App::make('ni.elegant.model.query.builder');
        $record = \App::make('Patient');

        $mock = $this->mockRelation();

        $mock->expects($this->once())
            ->method('addConstraints')
            ->withAnyParameters()
        ;

        $mock->__construct($queryBuilder, $record);
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::__construct
     * @group relation
     * @group constructor
     */
    public function testConstructor()
    {
        $mock = $this->mockRelation();

        $privateQuery = $this->getPrivateProperty($mock, 'query')->getValue($mock);
        $privateParent= $this->getPrivateProperty($mock, 'parent')->getValue($mock);

        $this->assertTrue($privateQuery instanceof \Netinteractive\Elegant\Model\Query\Builder);
        $this->assertTrue($privateParent instanceof \Netinteractive\Elegant\Model\Record);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::setRelated
     * @group related
     * @group record
     * @group set
     */
    public function testSetRelated()
    {
        $mock = $this->mockRelation();
        $mock->setRelated(\App::make('PatientData'));

        $privateRelated= $this->getPrivateProperty($mock, 'related')->getValue($mock);

        $this->assertTrue($privateRelated instanceof \Netinteractive\Elegant\Model\Record);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::getRelated
     * @group related
     * @group record
     * @group get
     */
    public function testGetRelated()
    {
        $mock = $this->mockRelation();
        $mock->setRelated(\App::make('PatientData'));

        $this->assertTrue($mock->getRelated() instanceof \Netinteractive\Elegant\Model\Record);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::getQuery
     * @group get
     * @group query
     */
    public function testGetQuery()
    {
        $mock = $this->mockRelation();
        $this->assertTrue($mock->getQuery() instanceof \Netinteractive\Elegant\Model\Query\Builder);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::setQuery
     * @group set
     * @group query
     */
    public function testSetQuery()
    {
        $mock = $this->mockRelation();
        $query = $mock->getQuery();
        $query->from('patient');
        $mock->setQuery($query);

        $this->assertEquals('patient', $mock->getQuery()->getFrom());
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::getEager
     * @group eager
     * @group get
     */
    public function testGetEager()
    {
        $mock = $this->mockRelation();
        $mock->setRelated(\App::make('PatientData'));

        $this->assertTrue($mock->getEager() instanceof \Netinteractive\Elegant\Model\Collection);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::rawUpdate
     * @group raw
     * @group update
     */
    public function testRawUpdate_Query_Call_Update()
    {
        $mock = $this->mockRelation();

        $builder = $mock->getQuery();
        $mockQuery = $this->getMockBuilder(get_class($builder))
            ->setMethods( array('update'))
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mockQuery->expects($this->once())
            ->method('update')
            ->withAnyParameters()
        ;

        $mock->setQuery($mockQuery);
        $mock->rawUpdate();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::noConstraints
     * @group constraint
     */
    public function testNoConstraints()
    {
        $called = false;

        $someFunction = function() use(&$called){
            $called = true;
        };

        $mock = $this->mockRelation();

        $mock->noConstraints( $someFunction );

        $protectedConstraints = $this->getPrivateProperty($mock, 'constraints')->getValue($mock);

        $this->assertTrue($called);
        $this->assertTrue($protectedConstraints);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::getParent
     * @group record
     * @group get
     */
    public function testGetParent()
    {
        $mock = $this->mockRelation();
        $this->assertTrue($mock->getParent() instanceof \Netinteractive\Elegant\Model\Record);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::getForeignKey
     * @group fk
     * @group get
     */
    public function testGetForeignKey()
    {
        $mock = $this->mockRelation();
        $record = \App::make('User');

        $this->assertEquals('user'.$mock::$postFk, $mock->getForeignKey($record));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::newPivot
     * @group pivot
     * @group get
     */
    public function testNewPivot()
    {
        $mock = $this->mockRelation();
        $record = \App::make('User');
        $params = array();

        $pivot = $mock->newPivot($record, $params, 'user', false);

        $this->assertTrue($pivot instanceof \Netinteractive\Elegant\Relation\Pivot);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::__call
     * @group call
     */
    public function testCall_Result()
    {
        $mock = $this->mockRelation();
        $result = $mock->from('user');


        $this->assertTrue($result instanceof \Netinteractive\Elegant\Relation\Relation);
        $this->assertEquals('user', $result->getFrom());
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::getKeys
     * @group pk
     * @group get
     */
    public function testGetKeys_EmptyKeys_Response()
    {
        $mock = $this->mockRelation();
        $record = \App::make('Patient');

        $q =\App::make('ni.elegant.db.query.builder');
        $q->from('patient');

        $record->fill((Array) $q->find(1));
        $collection = \App::make('ni.elegant.model.collection');
        $collection->add($record);

        $response = $this->callPrivateMethod($mock, 'getKeys', array($collection));

        $this->assertTrue(is_array($response));
        $this->assertEquals(1, count($response));
        $this->assertArrayHasKey('id', $response);
        $this->assertTrue(is_array($response['id']));
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\Relation::getKeys
     * @group pk
     * @group get
     */
    public function testGetKeys_Response()
    {
        $mock = $this->mockRelation();
        $record = \App::make('Patient');

        $q =\App::make('ni.elegant.db.query.builder');
        $q->from('patient');

        $record->fill((Array) $q->find(1));
        $collection = \App::make('ni.elegant.model.collection');
        $collection->add($record);

        $keys = array('id');
        $response = $this->callPrivateMethod($mock, 'getKeys', array($collection, $keys));

        $this->assertTrue(is_array($response));
        $this->assertEquals(1, count($response));
        $this->assertArrayHasKey('id', $response);
        $this->assertTrue(is_array($response['id']));
    }
}