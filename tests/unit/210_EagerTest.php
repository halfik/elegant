<?php

class EagerTest extends ElegantTest
{
    /**
     * @covers \Netinteractive\Elegant\Model\Record::__call
     * @covers \Netinteractive\Elegant\Relation\Relation::get
     * @group eager
     * @group loading
     * @grup HasOne
     */
    public function testEagerHasOne_Get(){
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('User');
        $user = $dbMapper->find(1);

        $result = $user->patient()->get();

        $this->assertCount(1, $result);
        $this->assertInstanceOf('\Netinteractive\Elegant\Tests\Models\Patient\Record', $result[0]);
        $this->assertEquals(1, $result[0]->user__id);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::__call
     * @covers \Netinteractive\Elegant\Relation\Relation::first
     * @group eager
     * @group loading
     * @group HasOne
     */
    public function testEagerHasOne_First(){
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('User');
        $user = $dbMapper->find(1);

        $result = $user->patient()->first();
        $this->assertInstanceOf('\Netinteractive\Elegant\Tests\Models\Patient\Record', $result);
        $this->assertEquals(1, $result->user__id);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::__call
     * @covers \Netinteractive\Elegant\Relation\HasMany::get
     * @group eager
     * @group loading
     * @grup HasMany
     */
    public function testEagerHasMany_Get(){
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Patient');
        $record = $dbMapper->find(2);

        $result = $record->patientData()->get();

        $this->assertCount(2, $result);
        $this->assertInstanceOf('\Netinteractive\Elegant\Tests\Models\PatientData\Record', $result[0]);
        $this->assertEquals($record->id, $result[0]->patient__id);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::__call
     * @covers \Netinteractive\Elegant\Relation\Relation::first
     * @group eager
     * @group loading
     * @group HasMany
     * @group test
     */
    public function testEagerHasMany_First(){
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Patient');
        $record = $dbMapper->find(1);

        $result = $record->patientData()->first();

        $this->assertInstanceOf('\Netinteractive\Elegant\Tests\Models\PatientData\Record', $result);
        $this->assertEquals($record->id, $result->patient__id);
    }
}

