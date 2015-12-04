<?php

class ModelFilterLogicTest extends ElegantTest
{
    /**
     * @covers \Netinteractive\Elegant\Model\Filter\Logic::parseFilters
     * @group filter
     */
    public function testParseFilters()
    {
        $response =  Netinteractive\Elegant\Model\Filter\Logic::parseFilters('filter1|filter2 |filter3');

        $this->assertTrue(is_array($response));
        $this->assertEquals(3, count($response));
        $this->assertEquals('filter2', $response[1]);
    }
}