<?php

class ModelFilterSaveTest extends ElegantTest
{
    /**
     * @covers \Netinteractive\Elegant\Model\Filter\Type\Save::apply
     * @group filter
     * @group save
     */
    public function testParseApply_IsCallable()
    {
        $obj = new \stdClass();

        $obj->data = array(
            'name' => 'adam'
        );
        $obj->record = null;

        $func = function ($value) {
            return strtoupper($value);
        };

        \Netinteractive\Elegant\Model\Filter\Type\Save::apply($obj, 'name' ,array($func));

        $this->assertEquals('ADAM', $obj->data['name']);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Filter\Type\Save::apply
     * @group filter
     * @group save
     */
    public function testParseApply_Defined_NoParams()
    {
        $obj = new \stdClass();

        $obj->data = array(
            'name' => 'adam'
        );
        $obj->record = null;

        \Netinteractive\Elegant\Model\Filter\Type\Save::apply($obj, 'name' ,array('firstToUpper'));

        $this->assertEquals('Adam', $obj->data['name']);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Filter\Type\Save::apply
     * @group filter
     * @group save
     */
    public function testParseApply_Defined_WithParams()
    {
        $obj = new \stdClass();

        $obj->data = array(
            'phone' => '000'
        );
        $obj->record = null;

        \Netinteractive\Elegant\Model\Filter\Type\Save::apply($obj, 'phone' ,array('str_replace: 0,1'));

        $this->assertEquals('111', $obj->data['phone']);
    }
}