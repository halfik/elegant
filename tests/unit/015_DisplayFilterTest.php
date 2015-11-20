<?php

use \Netinteractive\Elegant\Model\Filter\Type\Display AS Display;

class DisplayFilterTest extends ElegantTest
{

    /**
     * @covers \Netinteractive\Elegant\Model\Filter\Type\Display::apply
     * @group display
     * @group filter
     */
    public function testApply_Callable()
    {
        $obj = new \stdClass();

        $obj->value = 'JOHN';
        $obj->field = 'login';
        $obj->record = null;

        Display::apply($obj, array(
            function ($value, $field, $params=array()){
                return strtolower($value);
            }
        ));

         $this->assertEquals('john', $obj->value);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Filter\Type\Display::apply
     * @group display
     * @group filter
     */
    public function testApply_Defined()
    {
        $obj = new \stdClass();

        $obj->value = 'john';
        $obj->field = 'login';
        $obj->record = new \stdClass();

        Display::apply($obj, array('upper'));

        $this->assertEquals('JOHN', $obj->value);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Filter\Type\Display::apply
     * @group display
     * @group filter
     */
    public function testApply_DefinedWithParams()
    {
        $obj = new \stdClass();

        $obj->value = 'john';
        $obj->field = 'login';
        $obj->record = null;

        Display::apply($obj, array('truncate: 2,0'));

        $this->assertEquals('jo...', $obj->value);
    }


}