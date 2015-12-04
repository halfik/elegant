<?php

class ModelFilterDisplayTest extends ElegantTest
{


    /**
     * @covers \Netinteractive\Elegant\Model\Filter\Type\Display::apply
     * @group filter
     * @group display
     */
    public function testParseApply_IsCallable()
    {
        $obj = new \stdClass();

        $obj->field = 'id';
        $obj->value = 1;
        $obj->record = null;


        $func =  function ($value, $field, $params = array(), $record=null) {
            if ($value && (int)$value == 1) {
                return _('Yes');
            }
            return _('No');
        };

        \Netinteractive\Elegant\Model\Filter\Type\Display::apply($obj, array($func));

        $this->assertEquals(_('Yes'), $obj->value);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Filter\Type\Display::apply
     * @group filter
     * @group display
     */
    public function testParseApply_Defined_NoParams()
    {
        $obj = new \stdClass();

        $obj->field = 'id';
        $obj->value = 1;
        $obj->record = null;

        \Netinteractive\Elegant\Model\Filter\Type\Display::apply($obj, array('bool'));

        $this->assertEquals(_('Yes'), $obj->value);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Filter\Type\Display::apply
     * @group filter
     * @group display
     */
    public function testParseApply_Defined_WithParams()
    {
        $obj = new \stdClass();

        $obj->field = 'my_Field';
        $obj->value = 1.2345;
        $obj->record = null;

        \Netinteractive\Elegant\Model\Filter\Type\Display::apply($obj, array('precision: 2'));

        $this->assertEquals(1.23, $obj->value);
    }
}