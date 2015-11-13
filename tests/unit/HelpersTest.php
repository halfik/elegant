<?php
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 13.11.15
 * Time: 11:00
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