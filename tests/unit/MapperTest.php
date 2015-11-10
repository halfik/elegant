<?php

class MapperTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        \Artisan::call('db:ni-seed:test-data',
            array(
                '--config' => 'packages.netinteractive.elegant.test',
                '--env' => 'testing'
            )
        );
    }

    protected function tearDown()
    {
    }

    // tests
    public function testMe()
    {
    }
}
