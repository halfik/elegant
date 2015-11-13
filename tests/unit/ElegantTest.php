<?php
use Illuminate\Support\Facades\Artisan;

class ElegantTest  extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        App::bind('PatientData', '\Netinteractive\Elegant\Tests\Models\PatientData\Record');
        App::bind('Patient', '\Netinteractive\Elegant\Tests\Models\Patient\Record');
        App::bind('User', '\Netinteractive\Elegant\Tests\Models\User\Record');
        App::bind('Med', '\Netinteractive\Elegant\Tests\Models\Med\Record');
        App::bind('Tu', '\Netinteractive\Elegant\Tests\Models\Tu\Record');

        Artisan::call('db:ni-seed:test-data',
            array(
                '--config' => 'packages.netinteractive.elegant.test',
                '--env' => 'testing'
            )
        );
    }

    protected function tearDown()
    {
    }
}