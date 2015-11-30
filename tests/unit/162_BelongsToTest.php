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
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::__construct
     * @group relation
     * @group constructor
     */
    public function testConstructor_OtherKeyNotArray()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::__construct
     * @expectedException \Netinteractive\Elegant\Exception\PkFkSizeException
     * @group relation
     * @group constructor
     */
    public function testConstructor_KeyDiff()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::getForeignKey
     * @group get
     * @group fk
     */
    public function testGetForeignKey()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::getQualifiedForeignKey
     * @group get
     * @group fk
     */
    public function testGetQualifiedForeignKey()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::getOtherKey
     * @group get
     * @group fk
     */
    public function testGetOtherKey()
    {
        $this->markTestIncomplete();
    }




    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::getResults
     * @group get
     */
    public function testGetResults()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::get
     * @group get
     */
    public function testGet_SetRecord()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::get
     * @group get
     */
    public function testGet_Result()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::addConstraints
     * @group add
     */
    public function testAddConstraints()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::getEagerRecordKeys
     * @group get
     */
    public function testGetEagerRecordKeys()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::getEagerRecordKeys
     * @group get
     */
    public function testGetEagerRecordKeys_ArrayUnique()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::addEagerConstraints
     * @group add
     */
    public function testAddEagerConstraints_CallGetEagerRecordKeys()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::initRelation
     * @group relation
     */
    public function testInitRelation_SetRelated()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::initRelation
     * @group relation
     */
    public function testInitRelation_Result()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::match
     * @group relation
     */
    public function testMatch_SetRelated()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsTo::match
     * @group relation
     */
    public function testMatch_Result()
    {
        $this->markTestIncomplete();
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