<?php

class BlueprintTest extends ElegantTest
{
    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::__construct
     * @group constructor
     */
   /* public function testConstructor__Init()
    {
        $record = App::make('Patient');

        $mock = $this->getMockBuilder(get_class($record->getBlueprint()))
            ->disableOriginalConstructor()
            ->setMethods( array('init'))
            ->getMock()
        ;


        $mock->expects($this->exactly(1))
            ->method('init')
            ->withAnyParameters()
        ;

        $blueprint = $this->getReflectedObj($record->getBlueprint(), array('__construct'));

        $constructor = $blueprint->getConstructor();
        $constructor->invoke($mock);
    }*/

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getStorageName
     * @group storage
     * @group get
     */
    public function testGetStorageName()
    {
        $blueprint = \App::make('Patient')->getBlueprint();

        $this->assertEquals('patient', $blueprint->getStorageName());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::setStorageName
     * @group storage
     * @group get
     */
    public function testSetStorageName()
    {
        $blueprint = \App::make('Patient')->getBlueprint();
        $blueprint->setStorageName('patient2');

        $this->assertEquals('patient2', $blueprint->getStorageName());
        $blueprint->setStorageName('patient');
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getInstance
     * @group instance
     * @group get
     */
    public function testGetInstance()
    {
        $blueprint = \App::make('Patient')->getBlueprint();
        $blueprint2 = \App::make('Patient')->getBlueprint();

        $blueprint->setStorageName('patient2');
        $this->assertEquals($blueprint->getStorageName(), $blueprint2->getStorageName());
        $blueprint->setStorageName('patient');
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getFields
     * @group fields
     * @group array
     */
    public function testGetFields_Array()
    {
        $blueprint = \App::make('Patient')->getBlueprint();
        $this->assertTrue(is_array($blueprint->getFields()));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getSortableFields
     * @group fields
     * @group get
     * @group sortable
     */
    public function testGetSortableFields()
    {
        $blueprint = \App::make('Patient')->getBlueprint();
        $fields = $blueprint->getSortableFields();

        $this->assertEquals(2, count($fields));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getSearchableFields
     * @group fields
     * @group get
     * @group searchable
     */
    public function testGetSearchableFields()
    {
        $blueprint = \App::make('Patient')->getBlueprint();
        $fields = $blueprint->getSearchableFields();

        $this->assertEquals(1, count($fields));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getFieldsTitles
     * @group fields
     * @group get
     * @group titles
     */
    public function testGetFieldsTitles_EmptyKeys()
    {
        $blueprint = \App::make('Patient')->getBlueprint();
        $titles = $blueprint->getFieldsTitles();

        $this->assertEquals(5, count($titles));
        $this->assertTrue(array_key_exists('pesel', $titles));
        $this->assertEquals('PESEL', $titles['pesel']);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getFieldsTitles
     * @group fields
     * @group get
     * @group titles
     */
    public function testGetFieldsTitles_StringFieldKey()
    {
        $blueprint = \App::make('Patient')->getBlueprint();
        $titles = $blueprint->getFieldsTitles('pesel');

        $this->assertEquals(1, count($titles));
        $this->assertTrue(array_key_exists('pesel', $titles));
        $this->assertEquals('PESEL', $titles['pesel']);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getFieldTitle
     * @group fields
     * @group get
     * @group titles
     */
    public function testGetFieldTitle()
    {
        $blueprint = \App::make('Patient')->getBlueprint();
        $title = $blueprint->getFieldTitle('pesel');

        $this->assertEquals('PESEL', $title);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getFieldTitle
     * @group fields
     * @group get
     * @group titles
     */
    public function testGetFieldTitle_Null()
    {
        $blueprint = \App::make('Patient')->getBlueprint();
        $title = $blueprint->getFieldTitle('pesel3');

        $this->assertTrue(is_null($title));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getFieldsRules
     * @group fields
     * @group get
     * @group validation
     */
    public function testGetFieldsRules()
    {
        $blueprint = \App::make('Patient')->getBlueprint();

        $rules = $blueprint->getFieldsRules();

        $this->assertEquals(3, count($rules));
        $this->assertTrue(array_key_exists('id', $rules));
        $this->assertTrue(array_key_exists('user__id', $rules));
        $this->assertTrue(array_key_exists('pesel', $rules));
        $this->assertFalse(strpos($rules['id'], 'required'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getFieldsRules
     * @group fields
     * @group get
     * @group validation
     */
    public function testGetFieldsRules_StringRulesGroups()
    {
        $blueprint = \App::make('Patient')->getBlueprint();

        $rules = $blueprint->getFieldsRules('update');

        $this->assertEquals(3, count($rules));
        $this->assertTrue(array_key_exists('id', $rules));
        $this->assertNotFalse(strpos($rules['id'], 'required'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getFieldsRules
     * @group fields
     * @group get
     * @group validation
     */
    public function testGetFieldsRules_FieldKeys()
    {
        $blueprint = \App::make('Patient')->getBlueprint();

        $rules = $blueprint->getFieldsRules('update', array('id'));

        $this->assertEquals(1, count($rules));
        $this->assertTrue(array_key_exists('id', $rules));
        $this->assertNotFalse(strpos($rules['id'], 'required'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getFieldRules
     * @group fields
     * @group get
     * @group validation
     */
    public function testGetFieldRules()
    {
        $blueprint = \App::make('Patient')->getBlueprint();

        $rules = $blueprint->getFieldRules('id');

        $this->assertEquals(2, count($rules));
        $this->assertTrue(array_key_exists('any', $rules));
        $this->assertTrue(array_key_exists('update', $rules));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getFieldRules
     * @group fields
     * @group get
     * @group validation
     */
    public function testGetFieldRules_EmptyArray()
    {
        $blueprint = \App::make('Patient')->getBlueprint();

        $rules = $blueprint->getFieldRules('id2');

        $this->assertEquals(0, count($rules));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getFieldsTypes
     * @group fields
     * @group get
     * @group types
     */
    public function testGetFieldsTypes()
    {
        $blueprint = \App::make('Patient')->getBlueprint();
        $types = $blueprint->getFieldsTypes(array('id'));

        $this->assertEquals(1, count($types));
        $this->assertTrue(array_key_exists('id', $types));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getFieldsTypes
     * @group fields
     * @group get
     * @group types
     */
    public function testGetFieldsTypes_All()
    {
        $blueprint = \App::make('Patient')->getBlueprint();
        $types = $blueprint->getFieldsTypes();

        $this->assertEquals(5, count($types));
        $this->assertTrue(array_key_exists('id', $types));
        $this->assertTrue(array_key_exists('user__id', $types));
        $this->assertTrue(array_key_exists('pesel', $types));
        $this->assertTrue(array_key_exists('created_at', $types));
        $this->assertTrue(array_key_exists('updated_at', $types));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Blueprint::getFieldType
     * @group fields
     * @group get
     * @group types
     */
    public function testGetFieldType()
    {
        $blueprint = \App::make('Patient')->getBlueprint();
        $type = $blueprint->getFieldType('id');

        $this->assertEquals($blueprint::TYPE_INT, $type);
    }

}