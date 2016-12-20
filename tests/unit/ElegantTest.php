<?php
use Illuminate\Support\Facades\Artisan;


/**
 * Class ElegantTest
 */
class ElegantTest  extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {

        \App::bind('PatientData', '\Netinteractive\Elegant\Tests\Models\PatientData\Record');
        \App::bind('Patient', '\Netinteractive\Elegant\Tests\Models\Patient\Record');
        \App::bind('User', '\Netinteractive\Elegant\Tests\Models\User\Record');
        \App::bind('Med', '\Netinteractive\Elegant\Tests\Models\Med\Record');
        \App::bind('Tu', '\Netinteractive\Elegant\Tests\Models\Tu\Record');
        \App::bind('MedPersonnel', '\Netinteractive\Elegant\Tests\Models\MedPersonnel\Record');
        \App::bind('MedScienceDegree', '\Netinteractive\Elegant\Tests\Models\MedScienceDegree\Record');

    }


    protected function tearDown()
    {

    }

    /**
     * @param string $class
     * @param string $method
     * @param array $args
     * @param string $code
     * @return mixed
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
     * @param $class
     * @return ReflectionClass
     */
    public function getReflectedObj($class, $methods=array(), $properties=array())
    {
        if (is_object($class)){
            $class = get_class($class);
        }

        $obj = new \ReflectionClass($class);

        foreach ($methods AS $method){
            $reflectionMethod = $obj->getMethod($method);
            $reflectionMethod->setAccessible(true);
        }

        foreach ($properties AS $property){
            $reflectionProperty = $obj->getProperty($property);
            $reflectionProperty->setAccessible(true);
        }

        return $obj;
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