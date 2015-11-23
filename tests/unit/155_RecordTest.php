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
}