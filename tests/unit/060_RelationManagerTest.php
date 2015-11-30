<?php

class RelationManagerTest extends ElegantTest
{

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::setCurrentTranslator
     * @group set
     * @group relation
     * @group translator
     */
    public function testSetCurrentTranslator()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::getCurrentTranslator
     * @group get
     * @group relation
     * @group translator
     */
    public function testGetCurrentTranslator()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::registerTranslator
     * @group set
     * @group relation
     * @group translator
     */
    public function testRegisterTranslator()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::__construct
     * @group set
     * @group relation
     * @group translator
     */
    public function testConstructor_Call_RegisterTranslator()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::__construct
     * @group set
     * @group relation
     * @group translator
     */
    public function testConstructor_Call_SetCurrentTranslator()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::__construct
     * @group set
     * @group relation
     * @group translator
     */
    public function testConstructor()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::hasTranslator
     * @group has
     * @group translator
     */
    public function testHasTranslator_Response()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::getTranslator
     * @group get
     * @group translator
     */
    public function testGetTranslator_Call_GetCurrentTranslator()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::getTranslator
     * @group get
     * @group translator
     */
    public function testGetTranslator_Call_GasTranslator()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::getTranslator
     * @expectedException \Netinteractive\Elegant\Exception\TranslatorNotRegisteredException
     * @group get
     * @group translator
     */
    public function testGetTranslator_NoTranslator()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::getTranslator
     * @group get
     * @group translator
     */
    public function testGetTranslator_Response()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::hasRelation
     * @group has
     * @group relation
     */
    public function testHasRelation_Response()
    {
        $this->markTestIncomplete();
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::getRelation
     * @group get
     * @group relation
     */
    public function testGetRelation_Call_HasRelation()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::getRelation
     * @expectedException \Netinteractive\Elegant\Exception\RelationDoesntExistsException
     * @group get
     * @group relation
     */
    public function testGetRelation_NoRelation()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::getRelation
     * @group get
     * @group relation
     */
    public function testGetRelation_Response()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::createRelation
     * @group get
     * @group relation
     */
    public function testCreateRelation_Call_GetTranslator()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::createRelation
     * @group get
     * @group relation
     */
    public function testCreateRelation_Call_GetRelation()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::createRelation
     * @group get
     * @group relation
     */
    public function testCreateRelation_Response()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::belongsTo
     * @group relation
     * @group belongTo
     */
    public function testBelongsTo()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::hasOne
     * @group relation
     * @group hasOne
     */
    public function testHasOne()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::hasMany
     * @group relation
     * @group hasMany
     */
    public function testHasMany()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Relation\Manager::belongsToMany
     * @group relation
     * @group hasMany
     */
    public function testBelongsToMany()
    {
        $this->markTestIncomplete();
    }

}