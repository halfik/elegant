<?php

class RecordTestDbMapper  extends ElegantTest
{
    /**
     * @covers \Netinteractive\Elegant\Model\Record::makeDirty
     * @group dirty
     * @group make
     */
    public function testMakeDirty_EmptyAttributes()
    {
        \DB::beginTransaction();

        $record = \App::make('Patient',  array(array(
            'id' => 99,
            'user__id' => 5,
            'pesel' => '13292213737',
        )));

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $dbMapper->save($record);


        $record->makeDirty(array('id'));
        $dirty = $record->getDirty();

        $this->assertEquals(1, count($dirty));
        $this->assertTrue($record->isDirty('id'));
        $this->assertFalse($record->isDirty('user__id'));
        $this->assertFalse($record->isDirty('pesel'));

        \DB::rollback();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::markAsNew
     * @group dirty
     * @group make
     */
    public function testMarkAsNew()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::getRelated
     * @group related
     * @group get
     */
    public function testGetRelated()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::hasRelated
     * @group related
     * @group has
     */
    public function testHasRelated()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setRelated
     * @group related
     * @group set
     */
    public function testSetRelated()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setRawRelated
     * @group related
     * @group set
     */
    public function testSetRawRelated()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::toArray
     * @group array
     * @group to
     */
    public function testToArray_Related()
    {
        $this->markTestIncomplete();
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Record::__isset
     * @group attribute
     * @group isset
     */
    public function testIsset_Related()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::__unset
     * @group attribute
     * @group unset
     */
    public function testUnset_Related()
    {
        $this->markTestIncomplete();
    }


}