<?php

class ModelFilterFillTest extends ElegantTest
{
    /**
     * @covers \Netinteractive\Elegant\Model\Filter\Type\Fill::apply
     * @group filter
     * @group fill
     */
    public function testParseApply_IsCallable()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('User');
        $record = $dbMapper->find(1);

        $func = function ($value) {
            return strtoupper($value);
        };

        \Netinteractive\Elegant\Model\Filter\Type\Fill::apply($record, 'first_name' ,array($func));

        $this->assertEquals(strtoupper($record->first_name), $record->first_name);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Filter\Type\Fill::apply
     * @group filter
     * @group fill
     */
    public function testParseApply_Defined_NoParams()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('User');
        $record = $dbMapper->find(1);
        $record->first_name = '';

        \Netinteractive\Elegant\Model\Filter\Type\Fill::apply($record, 'first_name' ,array('emptyToNull'));

        $this->assertNull($record->first_name);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Filter\Type\Fill::apply
     * @group filter
     * @group fill
     */
    public function testParseApply_Defined_WithParams()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('User');
        $record = $dbMapper->find(1);
        $record->first_name = '<b><i>Adam</i></b>';

        \Netinteractive\Elegant\Model\Filter\Type\Fill::apply($record, 'first_name' ,array('stripTags: <b>'));

        $this->assertEquals('<b>Adam</b>', $record->first_name);
    }
}