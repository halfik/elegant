<?php

class RelationManagerTest extends ElegantTest
{

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::__construct
     * @group set
     * @group relation
     * @group translator
     */
    public function testConstructor_Call_RegisterTranslator()
    {
        $mock = $this->getMockBuilder('\Netinteractive\Elegant\Model\Relation\Manager')
            ->setMethods( array('registerTranslator'))
            ->getMock()
        ;

        $mock->expects($this->atLeastOnce())
            ->method('registerTranslator')
            ->withAnyParameters()
        ;


        $mock->__construct();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::__construct
     * @group set
     * @group relation
     * @group translator
     */
    public function testConstructor_Call_SetCurrentTranslator()
    {
        $mock = $this->getMockBuilder('\Netinteractive\Elegant\Model\Relation\Manager')
            ->setMethods( array('setCurrentTranslator'))
            ->getMock()
        ;

        $mock->expects($this->atLeastOnce())
            ->method('setCurrentTranslator')
            ->withAnyParameters()
        ;


        $mock->__construct();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::getCurrentTranslator
     * @group get
     * @group relation
     * @group translator
     */
    public function testGetCurrentTranslator()
    {
        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();

        $this->assertEquals('db', $manager->getCurrentTranslator());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::setCurrentTranslator
     * @group set
     * @group relation
     * @group translator
     */
    public function testSetCurrentTranslator()
    {
        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();
        $manager->setCurrentTranslator('db2');

        $this->assertEquals('db2', $manager->getCurrentTranslator());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::hasTranslator
     * @group has
     * @group translator
     */
    public function testHasTranslator_True()
    {
        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();
        $this->assertTrue($manager->hasTranslator('db'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::hasTranslator
     * @group has
     * @group translator
     */
    public function testHasTranslator_False()
    {
        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();
        $this->assertFalse($manager->hasTranslator('abcd'));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::registerTranslator
     * @group set
     * @group relation
     * @group translator
     */
    public function testRegisterTranslator()
    {
        $mock = $this->getMockBuilder('\Netinteractive\Elegant\Model\Relation\Translator\DbTranslator')
            ->getMock();

        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();
        $manager->registerTranslator('new_translator', $mock);

        $this->assertTrue($manager->hasTranslator('new_translator'));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::hasTranslator
     * @group get
     * @group translator
     */
    public function testGetTranslator_Call_HasTranslator()
    {
        $mock = $this->getMockBuilder('\Netinteractive\Elegant\Model\Relation\Manager')
            ->setMethods( array('hasTranslator'))
            ->getMock()
        ;

        $mock->method('hasTranslator')
            ->willReturn(true);

        $mock->expects($this->once())
            ->method('hasTranslator')
            ->withAnyParameters()
        ;

        $mock->getTranslator();
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::getTranslator
     * @expectedException \Netinteractive\Elegant\Exception\TranslatorNotRegisteredException
     * @group get
     * @group translator
     */
    public function testGetTranslator_Call_GetCurrentTranslator()
    {
        $mock = $this->getMockBuilder('\Netinteractive\Elegant\Model\Relation\Manager')
            ->setMethods( array('getCurrentTranslator'))
            ->getMock()
        ;


        $mock->expects($this->once())
            ->method('getCurrentTranslator')
            ->withAnyParameters()
        ;

        $mock->getTranslator();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::getTranslator
     * @group get
     * @group translator
     */
    public function testGetTranslator_Response()
    {
        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();
        $response = $manager->getTranslator('db');

        $this->assertTrue($response instanceof \Netinteractive\Elegant\Model\Relation\Translator\DbTranslator);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::belongsTo
     * @group relation
     * @group belongTo
     */
    public function testBelongsTo()
    {

        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();
        $manager->belongsTo('my_relation_belongTo', 'PatientData',  array('patient__id'), array('id'));

        $reflectedProperty = $this->getPrivateProperty($manager, 'relations');
        $relations = $reflectedProperty->getValue($manager);

        $this->assertTrue(array_key_exists('my_relation_belongTo', $relations));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::hasOne
     * @group relation
     * @group hasOne
     */
    public function testHasOne()
    {
        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();
        $manager->hasOne('my_relation_hasOne', 'PatientData',  array('patient__id'), array('id'));

        $reflectedProperty = $this->getPrivateProperty($manager, 'relations');
        $relations = $reflectedProperty->getValue($manager);

        $this->assertTrue(array_key_exists('my_relation_hasOne', $relations));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::hasMany
     * @group relation
     * @group hasMany
     */
    public function testHasMany()
    {
        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();
        $manager->hasMany('my_relation_hasMany', 'PatientData',  array('patient__id'), array('id'));

        $reflectedProperty = $this->getPrivateProperty($manager, 'relations');
        $relations = $reflectedProperty->getValue($manager);

        $this->assertTrue(array_key_exists('my_relation_hasMany', $relations));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::belongsToMany
     * @group relation
     * @group hasMany
     */
    public function testBelongsToMany()
    {
        $relatedModel = App::make('PatientData');

        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();
        $manager->belongsToMany('my_relation_belongsToMany', 'PatientData',  array('patient__id'), array('id'));

        $reflectedProperty = $this->getPrivateProperty($manager, 'relations');
        $relations = $reflectedProperty->getValue($manager);

        $this->assertTrue(array_key_exists('my_relation_belongsToMany', $relations));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::hasRelation
     * @group has
     * @group relation
     */
    public function testHasRelation_True()
    {
        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();
        $manager->belongsToMany('my_relation_belongsToMany', 'PatientData',  array('patient__id'), array('id'));


        $this->assertTrue( $manager->hasRelation('my_relation_belongsToMany') );
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::hasRelation
     * @group has
     * @group relation
     */
    public function testHasRelation_False()
    {
        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();

        $this->assertFalse( $manager->hasRelation('my_relation_belongsToMany') );
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::getRelation
     * @expectedException \Netinteractive\Elegant\Exception\RelationDoesntExistsException
     * @group get
     * @group relation
     */
    public function testGetRelation_Call_HasRelation()
    {
        $mock = $this->getMockBuilder('\Netinteractive\Elegant\Model\Relation\Manager')
            ->setMethods( array('hasRelation'))
            ->getMock()
        ;

        $mock->method('hasTranslator')
            ->willReturn(false);



        $mock->expects($this->once())
            ->method('hasRelation')
            ->withAnyParameters()
        ;

        $mock->getRelation('test_relation');
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::getRelation
     * @group get
     * @group relation
     */
    public function testGetRelation_Response()
    {
        $relatedModel = App::make('PatientData');

        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();
        $manager->belongsTo('my_relation_belongTo', $relatedModel,  array('patient__id'), array('id'), 'relation1');

        $response = $manager->getRelation('my_relation_belongTo');

        $this->assertEquals(5, count($response));
        $this->assertEquals('belongsTo', $response[0]);
        $this->assertTrue($response[1] instanceof \Netinteractive\Elegant\Model\Record);
        $this->assertTrue(is_array($response[2]));
        $this->assertTrue(is_array($response[3]));
        $this->assertEquals('relation1', $response[4]);
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::createRelation
     * @group get
     * @group relation
     */
    public function testCreateRelation_Call_GetTranslator()
    {
        $record  = App::make('Patient');


        $mock = $this->getMockBuilder('\Netinteractive\Elegant\Model\Relation\Manager')
            ->setMethods( array('getTranslator'))
            ->getMock()
        ;

        $mock->belongsTo('my_relation_belongTo', 'PatientData',  array('patient__id'), array('id'), 'relation1');


        $mock->method('getTranslator')
             ->willReturn(new \Netinteractive\Elegant\Model\Relation\Translator\DbTranslator());

        $mock->expects($this->atLeastOnce())
            ->method('getTranslator')
            ->withAnyParameters()
        ;

        $mock->createRelation($record,'my_relation_belongTo');
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::createRelation
     * @group get
     * @group relation
     */
    public function testCreateRelation_Call_GetRelation()
    {
        $record  = App::make('Patient');


        $mock = $this->getMockBuilder('\Netinteractive\Elegant\Model\Relation\Manager')
            ->setMethods( array('getRelation'))
            ->getMock()
        ;


        $mock->method('getRelation')
            ->willReturn(array('belongsTo', 'PatientData', array('patient__id'), array('id'), 'my_relation_belongTo'));

        $mock->expects($this->atLeastOnce())
            ->method('getRelation')
            ->withAnyParameters()
        ;


        $mock->createRelation($record,'my_relation_belongTo');
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::createRelation
     * @group get
     * @group relation
     */
    public function testCreateRelation_Response()
    {
        $record  = App::make('Patient');


        $manager = new \Netinteractive\Elegant\Model\Relation\Manager();
        $manager->belongsTo('my_relation_belongTo', 'PatientData',  array('patient__id'), array('id'), 'relation1');

        $response = $manager->createRelation($record,'my_relation_belongTo');
        $this->assertTrue($response instanceof \Netinteractive\Elegant\Relation\BelongsTo);
    }

}