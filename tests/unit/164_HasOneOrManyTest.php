<?php

class HasOneOrManyTest extends ElegantTest
{
    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::__construct
     * @group relation
     * @group constructor
     */
    public function testConstructor_FkNotArray()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::__construct
     * @group relation
     * @group constructor
     */
    public function testConstructor_LocalKeyNotArray()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::__construct
     * @expectedException \Netinteractive\Elegant\Exception\PkFkSizeException
     * @group relation
     * @group constructor
     */
    public function testConstructor_KeyDiff()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getLocalKey
     * @group get
     * @group key
     */
    public function testGetLocalKey()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getForeignKey
     * @group get
     * @group key
     */
    public function testGetForeignKey()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getParentKey
     * @group get
     * @group key
     */
    public function testGetParentKey()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getQualifiedParentKeyName
     * @group get
     * @group key
     */
    public function testGetQualifiedParentKeyName()
    {
        $this->markTestIncomplete();
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::addConstraints
     * @group add
     */
    public function testAddConstraints()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::addEagerConstraints
     * @group add
     * @group eager
     */
    public function testAddEagerConstraints()
    {
        $this->markTestIncomplete();
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getPlainForeignKey
     * @group key
     * @group get
     */
    public function testGetPlainForeignKey()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::buildDictionary
     * @group build
     * @group dictionary
     */
    public function testBuildDictionary()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getRelationValue
     * @group get
     * @group relation
     */
    public function testGetRelationValue_One()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::getRelationValue
     * @group get
     * @group relation
     */
    public function testGetRelationValue_Many()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::matchOneOrMany
     * @group add
     * @group eager
     */
    public function testMatchOneOrMany_CallBuildDictionary()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::matchOneOrMany
     * @group add
     * @group eager
     */
    public function testMatchOneOrMany_Response()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::matchOne
     * @group add
     * @group eager
     */
    public function testMatchOne_CallMatchOneOrMany()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasOneOrMany::matchMany
     * @group add
     * @group eager
     */
    public function testMatchMany_CallMatchOneOrMany()
    {
        $this->markTestIncomplete();
    }


}