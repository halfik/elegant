<?php

class RecordDbMapperTest  extends ElegantTest
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

        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Patient');
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
     * @group new
     * @group exists
     * @group make
     */
    public function testMarkAsNew()
    {
        $record = \App::make('Patient',  array(array(
            'id' => 99,
            'user__id' => 5,
            'pesel' => '13292213737',
        )));

        $record->setExists(true);
        $this->assertTrue($record->exists());

        $record->setExists(false);
        $this->assertFalse($record->exists());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::markAsNew
     * @group new
     * @group exists
     * @group related
     * @group make
     */
    public function testMarkAsNew_TouchRelated()
    {
        $this->markTestIncomplete();
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Record::markAsNew
     * @group new
     * @group exists
     * @group make
     */
    public function testMarkAsNew_SetExists()
    {
        $record = App::make('Patient');
        $mock = $this->getMockBuilder(get_class($record))
            ->setMethods( array('setExists'))
            ->getMock()
        ;

        $mock->expects($this->exactly(1))
            ->method('setExists')
            ->withAnyParameters()
        ;

        $mock->markAsNew();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::hasRelated
     * @group related
     * @group has
     */
    public function testHasRelated_True()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('User');
        $record = $dbMapper->with('patient')->find(1);

        $this->assertTrue($record->hasRelated('patient'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::hasRelated
     * @group related
     * @group has
     */
    public function testHasRelated_False()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('User');
        $record = $dbMapper->with('patient')->find(1);

        $this->assertFalse($record->hasRelated('patientData'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::getRelated
     * @group related
     * @group get
     */
    public function testGetRelated()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('User');
        $record = $dbMapper->with('patient')->find(1);

        $result = $record->getRelated('patient');

        $this->assertInstanceOf('Netinteractive\Elegant\Tests\Models\Patient\Record', $result);
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Record::setRelated
     * @group related
     * @group set
     */
    public function testSetRelated()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('User');
        $record = $dbMapper->with('patient')->find(1);

        $dbMapper->setRecordClass('Patient');
        $patient = $dbMapper->find(1);

        $record->setRelated('patient', $patient);
        $result = $record->getRelated('patient');

        $this->assertInstanceOf('Netinteractive\Elegant\Tests\Models\Patient\Record', $result);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setRawRelated
     * @group related
     * @group set
     */
    public function testSetRawRelated()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('User');
        $record = $dbMapper->with('patient')->find(1);

        $dbMapper->setRecordClass('Patient');
        $patient = $dbMapper->find(1);

        $record->setRawRelated( array('patient'=>$patient));
        $result = $record->getRelated('patient');

        $this->assertInstanceOf('Netinteractive\Elegant\Tests\Models\Patient\Record', $result);
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::toArray
     * @group array
     * @group to
     */
    public function testToArray_Related()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('User');
        $record = $dbMapper->with('patient')->find(1);

        $dbMapper->setRecordClass('Patient');
        $patient = $dbMapper->find(1);

        $record->setRawRelated( array('patient'=>$patient));
        $result = $record->toArray();

        $this->assertArrayHasKey('patient', $result);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::__isset
     * @group attribute
     * @group isset
     */
    public function testIsset_Related()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('User');
        $record = $dbMapper->with('patient')->find(1);

        $this->assertTrue(isSet($record->patient));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::__unset
     * @group attribute
     * @group unset
     */
    public function testUnset_Related()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('User');
        $record = $dbMapper->with('patient')->find(1);


        unset($record->patient);
        
        $this->assertFalse(isSet($record->patient));
    }

    /**
     * @group scope
     */
    public function testScopes()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Netinteractive\Elegant\Tests\Models\MedPersonnel\Record');

        $res1 = $dbMapper->where("user__id", "=", 5)->med(1)->first();
        $res2 = $dbMapper->med(2)->whereRaw("1=1")->get();

        $this->assertTrue($res1 instanceof \Netinteractive\Elegant\Tests\Models\MedPersonnel\Record);
        $this->assertEquals(1 ,count($res2));
    }

    /**
     * @group count
     */
    public function testCount()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Netinteractive\Elegant\Tests\Models\MedPersonnel\Record');
        $res1 = $dbMapper->where("user__id", "=", 5)->med(1)->count();

        $this->assertEquals(1, $res1);
    }



}