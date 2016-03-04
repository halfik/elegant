<?php

class EagerTest extends ElegantTest
{
    /**
     * @covers \Netinteractive\Elegant\Model\Record::__call
     * @covers \Netinteractive\Elegant\Relation\Relation::get
     * @group eager
     * @group loading
     * @grup hasOne
     */
    public function testEagerHasOneOrMany_Get(){
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('User');
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
     * @group hasOne
     */
    public function testEagerHasOneOrMany_First(){
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('User');
        $user = $dbMapper->find(1);

        $result = $user->patient()->first();
        $this->assertInstanceOf('\Netinteractive\Elegant\Tests\Models\Patient\Record', $result);
        $this->assertEquals(1, $result->user__id);
    }
}

