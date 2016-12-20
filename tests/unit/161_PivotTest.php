<?php

class PivotTest extends ElegantTest
{


    /**
     * @covers \Netinteractive\Elegant\Relation\Pivot::__construct
     * @group pivot
     * @group fill
     * @group constructor
     */
    public function testConstructor_CallFill()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Patient');
        $record = $dbMapper->find(1);

        $pivot = new Netinteractive\Elegant\Relation\Pivot($record, $record->toArray(), 'patient', true);

        $mock = $this->getMockBuilder(get_class($pivot))
            ->setMethods( array('fill') )
            ->setConstructorArgs(array($record, $record->toArray(), 'patient', true))
            ->getMock()
        ;

        $mock->expects($this->atLeastOnce())
            ->method('fill')
            ->willReturn($this)
            ->withAnyParameters()
        ;

        $mock->__construct($record, $record->toArray(), 'patient', true);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\Pivot::__construct
     * @group pivot
     * @group exists
     * @group constructor
     */
    public function testConstructor_SetExists()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Patient');
        $record = $dbMapper->find(1);

        $pivot = new Netinteractive\Elegant\Relation\Pivot($record, $record->toArray(), 'patient', true);

        $mock = $this->getMockBuilder(get_class($pivot))
            ->setMethods( array('setExists'))
            ->setConstructorArgs(array($record, $record->toArray(), 'patient', true))
            ->getMock()
        ;

        $mock->expects($this->atLeastOnce())
            ->method('setExists')
            ->willReturn($this)
            ->withAnyParameters()
        ;

        $mock->__construct($record, $record->toArray(), 'patient', true);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\Pivot::__construct
     * @group pivot
     * @group constructor
     */
    public function testConstructor()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Patient');
        $record = $dbMapper->find(1);

        $pivot = new Netinteractive\Elegant\Relation\Pivot($record, $record->toArray(), 'patient', true);

        $parent = $this->getPrivateProperty(get_class($pivot), 'parent')->getValue($pivot);

        $this->assertInstanceOf('\Netinteractive\Elegant\Model\Record', $parent);
        $this->assertEquals($record->id, $parent->id);
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\Pivot::setPivotKeys
     * @group get
     * @group key
     */
    public function testSetPivotKeys()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Patient');
        $record = $dbMapper->find(1);

        $pivot = new Netinteractive\Elegant\Relation\Pivot($record, $record->toArray(), 'patient', true);
        $pivot->setPivotKeys('key1', 'key2');

        $foreignKey = $this->getPrivateProperty(get_class($pivot), 'foreignKey')->getValue($pivot);
        $otherKey = $this->getPrivateProperty(get_class($pivot), 'otherKey')->getValue($pivot);

        $this->assertEquals('key1', $foreignKey);
        $this->assertEquals('key2', $otherKey);
    }


    /**
     * @covers \Netinteractive\Elegant\Relation\Pivot::getForeignKey
     * @group get
     * @group fk
     */
    public function testGetForeignKey()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Patient');
        $record = $dbMapper->find(1);

        $pivot = new Netinteractive\Elegant\Relation\Pivot($record, $record->toArray(), 'patient', true);
        $pivot->setPivotKeys('key1', 'key2');

        $this->assertEquals('key1', $pivot->getForeignKey());
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\Pivot::getOtherKey
     * @group get
     * @group key
     */
    public function testGetOtherKey()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Patient');
        $record = $dbMapper->find(1);

        $pivot = new Netinteractive\Elegant\Relation\Pivot($record, $record->toArray(), 'patient', true);
        $pivot->setPivotKeys('key1', 'key2');

        $this->assertEquals('key2', $pivot->getOtherKey());
    }

    /**
     * @covers \Netinteractive\Elegant\Relation\Pivot::getDeleteQuery
     * @group get
     * @group delete
     * @group query
     */
    public function testGetDeleteQuery()
    {
        $this->markTestIncomplete();
    }



}