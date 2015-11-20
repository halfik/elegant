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

    /**
     * @param string|object $class
     * @param string $method
     * @param array $args
     * @param string $code
     * @param array $flags
     */
    protected function redefineMethod($class, $method, array $args, $code)
    {
        if (is_object($class)){
            $class = get_class($class);
        }

        return runkit_method_redefine(
            $class,
            $method,
            implode(',', $args),
            $code
        );
    }





    /**
     * @param string $class
     * @param string $property
     * @return ReflectionProperty
     */
    protected function getPrivateProperty($class, $property)
    {
        if (is_object($class)){
            $class = get_class($class);
        }

        $reflectionClass = new \ReflectionClass($class);

        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty;
    }



    /**
     * Allows to test private and protected methods
     * @param string|object $class
     * @param string $method
     * @return ReflectionMethod
     */
    protected function getPrivateMethod($class, $method)
    {
        if (is_object($class)){
            $class = get_class($class);
        }

        $reflectionClass = new \ReflectionClass($class);
        $method = $reflectionClass->getMethod($method);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Call pirvate/protected method of object
     * @param object $class
     * @param string $method
     * @param array $args
     * @return mixed
     */
    protected function  callPrivateMethod($object, $method, array $args=array())
    {

        $method = $this->getPrivateMethod($object, $method);
        return $method->invokeArgs($object, $args);
    }
}