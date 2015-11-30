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
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::__construct
     * @group relation
     * @group constructor
     */
    public function testConstructor_OtherKeyNotArray()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getTable
     * @group get
     */
    public function testGetTable()
    {
        $this->markTestIncomplete();
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getForeignKey
     * @group get
     * @group fk
     */
    public function testGetForeignKey_CallGetTable()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getForeignKey
     * @group get
     * @group fk
     */
    public function testGetForeignKey()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getOtherKey
     * @group get
     * @group key
     */
    public function testGetOtherKey_CallGetTable()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::getOtherKey
     * @group get
     * @group key
     */
    public function testGetOtherKey()
    {
        $this->markTestIncomplete();
    }



    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::setJoin
     * @group relation
     * @group join
     * @group set
     */
    public function testSetJoin_CallGetQuery()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::setJoin
     * @group relation
     * @group join
     * @group set
     */
    public function testSetJoin_CallGetOtherKey()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::setJoin
     * @group relation
     * @group join
     * @group set
     */
    public function testSetJoin_Response()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::addEagerConstraints
     * @group add
     */
    public function testAddEagerConstraints()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::initRelation
     * @group relation
     */
    public function testInitRelation_Record_setRelated()
    {
        $this->markTestIncomplete();
    }




    /**
     * @covers \Netinteractive\Elegant\Relation\BelongsToMany::initRelation
     * @group relation
     */
    public function testInitRelation_Response()
    {
        $this->markTestIncomplete();
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




}