<?php

class CollectionTest extends ElegantTest
{
    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::getResults
     * @group add
     */
    public function testAdd()
    {
        $collection = new \Netinteractive\Elegant\Model\Collection();
        $record = App::make('Patient');

        $collection->add($record);

        $this->assertEquals(1, count($collection->all()));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::makeDirty
     * @group make
     * @group dirty
     */
    public function testMakeDirty_Call_MakeDirty()
    {
        $data = array(
            'tu__id' => 123,
            'first_name' => 'John',
            'last_name' => 'London',
        );

        $record = \App::make('User',  array($data));


        $mockRecord = $this->getMockBuilder(get_class($record))
            ->setMethods( array('makeDirty'))
            ->getMock()
        ;

        $mockRecord->expects($this->atLeastOnce())
            ->method('makeDirty')
            ->withAnyParameters()
        ;

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($mockRecord);
        $collection->makeDirty(array('first_name', 'last_name'));
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::makeDirty
     * @group make
     * @group dirty
     */
    public function testMakeDirty_Result()
    {
        $data = array(
            'tu__id' => 123,
            'first_name' => 'John',
            'last_name' => 'London',
        );

        $record = \App::make('User',  array($data));
        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($record);
        $collection->makeDirty(array('first_name', 'last_name'));

        $records = $collection->all();

        $this->assertTrue($records[0]->isDirty('first_name'));
        $this->assertTrue($records[0]->isDirty('last_name'));
        $this->assertFalse($records[0]->isDirty('tu__id'));
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::makeNoneExists
     * @group add
     */
    public function testMakeNoneExists_Call_MarkAsNew()
    {
        $data = array(
            'tu__id' => 123,
            'first_name' => 'John',
            'last_name' => 'London',
        );

        $record = \App::make('User',  array($data));


        $mockRecord = $this->getMockBuilder(get_class($record))
            ->setMethods( array('markAsNew'))
            ->getMock()
        ;

        $mockRecord->expects($this->atLeastOnce())
            ->method('markAsNew')
            ->withAnyParameters()
        ;

        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($mockRecord);
        $collection->makeNoneExists();
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\HasMany::makeNoneExists
     * @group add
     */
    public function testMakeNoneExists()
    {
        $data = array(
            'tu__id' => 123,
            'first_name' => 'John',
            'last_name' => 'London',
        );

        $record = \App::make('User');
        $record->fill($data);
        $record->setExists(true);


        $collection = new \Netinteractive\Elegant\Model\Collection();
        $collection->add($record);


        $collection->makeNoneExists();

        $this->assertTrue($record->isNew());
    }



}