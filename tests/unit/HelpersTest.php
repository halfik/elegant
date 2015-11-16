<?php

/**
 * Class HelpersTest
 */
class HelpersTest extends  ElegantTest
{
    /**
     * classDotNotation function test
     */
    public function testClassDotNotation()
    {
        $obj = new \Netinteractive\Elegant\Tests\Models\Patient\Record();


        $this->assertEquals('netinteractive.elegant.tests.models.patient.record', classDotNotation($obj));
    }
}