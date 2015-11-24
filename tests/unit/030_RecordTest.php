<?php


class RecordTest extends ElegantTest
{

    /**
     * @covers \Netinteractive\Elegant\Model\Record::getBlueprint
     * @group blueprint
     */
    public function testGetBlueprint()
    {
        $record = App::make('User');

        $this->assertTrue($record->getBluePrint() instanceof \Netinteractive\Elegant\Model\Blueprint);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setBlueprint
     * @group blueprint
     */
    public function testSetBlueprint()
    {
        $record = App::make('User');
        $record2 = App::make('Patient');

        $record->setBlueprint($record2->getBlueprint());
        $this->assertTrue($record->getBluePrint() instanceof \Netinteractive\Elegant\Tests\Models\Patient\Blueprint);
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Record::hasBlueprint
     * @group blueprint
     */
    public function testHasBlueprint_True()
    {
        $record = App::make('User');

        $this->assertTrue($record->hasBlueprint());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::hasBlueprint
     * @group blueprint
     */
    public function testHasBlueprint_False()
    {
        $record = App::make('User');
        $record->setBluePrint(null);

        $this->assertFalse($record->hasBlueprint());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::originalIsNumericallyEquivalent
     * @group orignal
     * @group attribute
     */
    public function testOriginalIsNumericallyEquivalent()
    {
        $record = \App::make('User',  array(array(
            'tu__id' => 123,
            'first_name' => 'John',
            'last_name' => 'London',
        )));

        $record->fill(array('tu__id'=>'123'));

        $this->assertTrue($this->callPrivateMethod($record, 'originalIsNumericallyEquivalent', array('tu__id')));
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Record::getAttribute
     * @group attribute
     * @group get
     */
    public function testGetAttribute()
    {
        $record = App::make('User');
        $record->id = 1;

        $this->assertEquals(1, $record->getAttribute('id'));
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Record::getAttribute
     * @group attribute
     * @group get
     */
    public function testGetAttribute_External()
    {
        $record = App::make('User');
        $record->external_id = 1;

        $this->assertEquals(1, $record->getAttribute('external_id'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::getAttribute
     * @group attribute
     * @group get
     * @group related
     */
    public function testGetAttribute_Related()
    {
        $record = App::make('User');
        $patient = App::make('Patient');
        $patient->pesel = '123456';

        $this->getPrivateProperty($record, 'related')->setValue($record, array('patient' => $patient));

        $class = get_class($patient);
        $this->assertTrue($record->getAttribute('patient') instanceof $class);
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::setAttribute
     * @group attribute
     * @group set
     */
    public function testSetAttribute_Field()
    {
        $record = App::make('User');
        $record->setAttribute('id', 2);

        $this->assertEquals(2, $record->id);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setAttribute
     * @group attribute
     * @group set
     */
    public function testSetAttribute_NoBlueprint()
    {
        $record = App::make('User');
        $record->setBlueprint(null);
        $record->setAttribute('id', 2);

        $this->assertEquals(2, $record->id);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setAttribute
     * @group attribute
     * @group set
     */
    public function testSetAttribute_External()
    {
        $record = App::make('User');
        $record->setAttribute('my_field', 2);

        $this->assertEquals(2, $record->my_field);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setAttribute
     * @group attribute
     * @group set
     */
    public function testSetAttribute_ExternalField()
    {
        $record = App::make('User');
        $record->setAttribute('ip', '127.0.0.1');

        $this->assertEquals('127.0.0.1', $record->ip);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setAttribute
     * @group attribute
     * @group set
     */
    public function testSetAttribute_ExternalTimestamp()
    {
        $record = App::make('User');
        $record->setAttribute('created_at', '2015-01-01 11:11:11');

        $this->assertTrue( $record->created_at instanceof \Carbon\Carbon);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setAttribute
     * @group attribute
     * @group set
     */
    public function testSetAttribute_FieldTimestamp()
    {
        $record = App::make('PatientData');
        $record->setAttribute('created_at', '2015-01-01 11:11:11');

        $this->assertTrue( $record->created_at instanceof \Carbon\Carbon);
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::fill
     * @group fill
     * @group attribute
     */
    public function testFill()
    {
        $record = App::make('Patient');
        $record->fill(
            array(
                'user__id' => 123,
                'pesel' => 9999,
            )
        );

        $this->assertEquals('123', $record->user__id);
        $this->assertEquals('9999', $record->pesel);

    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::fill
     * @group fill
     * @group attribute
     */
    public function testFill_ByStorageName()
    {
        $record = App::make('Patient');
        $record->fill(
            array(
                'patient'=>
                    array(
                        'user__id' => 123,
                        'pesel' => 9999,
                    ),
                'patient_data' => array(
                    'id' => 555
                )
            )
        );

        $this->assertEquals('123', $record->user__id);
        $this->assertEquals('9999', $record->pesel);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::fill
     * @group fill
     * @group attribute
     */
    public function testFill_SetAttribute()
    {
        $record = App::make('Patient');
        $mock = $this->getMockBuilder(get_class($record))
            ->setMethods( array('setAttribute'))
            ->getMock()
        ;

        $mock->expects($this->exactly(2))
            ->method('setAttribute')
            ->withAnyParameters()
        ;

        $mock->fill(
            array(
                'user__id' => 123,
                'pesel' => 9999,
            )

        );
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::getInput
     * @covers \Netinteractive\Elegant\Model\Record::fill
     * @group fill
     * @group get
     * @group input
     */
    public function testGetInput()
    {
        $record = App::make('User');
        $record->fill(array(
            'id' => 999,
            'pesel' => '123456'
        ));

        $input = $record->getInput();

        $this->assertEquals(2, count($input));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::getInput
     * @covers \Netinteractive\Elegant\Model\Record::fill
     * @group fill
     * @group get
     * @group input
     */
    public function testGetInput_ByKey()
    {
        $record = App::make('User');
        $record->fill(array(
            'some_data' => array(
                'id' => 999,
                'pesel' => '123456'
            ),
            'more_data' => array(
                'id' => 123
            ),
            'little_more_data' => array(
                'id' => 321
            )

        ));

        $input = $record->getInput('some_data');

        $this->assertEquals(2, count($input));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setCreatedAt
     * @group timestamp
     * @group set
     * @group create
     */
    public function testSetCreatedAt()
    {
        $record = App::make('Patient');
        $record->setCreatedAt('2015-01-01 12:20:30');

        $createdAt = $record->getBlueprint()->getCreatedAt();

        $this->assertEquals('2015-01-01 12:20:30', $record->$createdAt);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setCreatedAt
     * @group timestamp
     * @group set
     * @group create
     */
    public function testSetCreatedAt_FromCarbon()
    {
        $record = App::make('Patient');
        $record->setCreatedAt(new \Carbon\Carbon('2015-01-01 12:20:30'));

        $createdAt = $record->getBlueprint()->getCreatedAt();

        $this->assertEquals('2015-01-01 12:20:30', $record->$createdAt);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setUpdatedAt
     * @group timestamp
     * @group set
     * @group create
     */
    public function testSetUpdatedAt()
    {
        $record = App::make('Patient');
        $record->setUpdatedAt('2015-01-01 12:20:30');

        $updatedAt = $record->getBlueprint()->getUpdatedAt();

        $this->assertEquals('2015-01-01 12:20:30', $record->$updatedAt);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setUpdatedAt
     * @group timestamp
     * @group set
     * @group update
     */
    public function testSetUpdatedAt_FromCarbon()
    {
        $record = App::make('Patient');
        $record->setUpdatedAt(new \Carbon\Carbon('2015-01-01 12:20:30'));

        $updatedAt = $record->getBlueprint()->getUpdatedAt();

        $this->assertEquals('2015-01-01 12:20:30', $record->$updatedAt);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setDeletedAt
     * @group timestamp
     * @group set
     * @group delete
     */
    public function testSetDeletedAt()
    {
        $record = App::make('Patient');
        $record->setDeletedAt('2015-01-01 12:20:30');

        $deletedAt = $record->getBlueprint()->getDeletedAt();

        $this->assertEquals('2015-01-01 12:20:30', $record->$deletedAt);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::setDeletedAt
     * @group timestamp
     * @group set
     * @group delete
     */
    public function testSetDeletedAt_FromCarbon()
    {
        $record = App::make('Patient');
        $record->setDeletedAt(new \Carbon\Carbon('2015-01-01 12:20:30'));

        $deletedAt = $record->getBlueprint()->getDeletedAt();

        $this->assertEquals('2015-01-01 12:20:30', $record->$deletedAt);
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Record::disableValidation
     * @group validation
     */
    public function testDisableValidation()
    {
        $record = App::make('Patient');
        $record->disableValidation();

        $this->assertFalse($this->getPrivateProperty($record, 'validationEnabled')->getValue($record));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::enableValidation
     * @group validation
     */
    public function testEnableValidation()
    {
        $record = App::make('Patient');
        $record->disableValidation();
        $record->enableValidation();

        $this->assertTrue($this->getPrivateProperty($record, 'validationEnabled')->getValue($record));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::validate
     * @expectedException \Netinteractive\Elegant\Exception\ValidationException
     * @group validation
     */
    public function testValidate_ValidationException()
    {
        $record = App::make('Patient');
        $record->validate(
            array(
                'user__id' => 123
            )
        );
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::validate
     * @group validation
     */
    public function testValidate_ValidationDisabled()
    {
        $record = App::make('Patient');
        $record->disableValidation();
        $response = $record->validate(
            array(
                'user__id' => 123
            )
        );

        $this->assertEquals(get_class($record), get_class($response));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::validate
     * @group validation
     */
    public function testValidate_ValidationGroup()
    {
        $record = App::make('User');
        $response = $record->validate(
            array(
                'login' => 'User 11',
                'email' => 'user11@hot.com',
            ),
            'update'
        );

        $this->assertEquals(get_class($record), get_class($response));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::isNew
     * @group exists
     */
    public function testIsNew()
    {
        $record = App::make('User');
        $this->assertTrue($record->isNew());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::isNew
     * @group exists
     */
    public function testIsNew_UsesExists()
    {
        $record = App::make('User');

        $mock = $this->getMockBuilder(get_class($record))
            ->setMethods( array('exists'))
            ->getMock()
        ;

        $mock->expects($this->exactly(1))
            ->method('exists')
            ->withAnyParameters()
        ;

        $mock->isNew();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::exists
     * @group exists
     * @group get
     */
    public function testExists_False()
    {
        $record = App::make('User');

        $this->assertFalse($record->exists());
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::exists
     * @covers \Netinteractive\Elegant\Model\Record::setExists
     * @group exists
     * @group get
     * @group set
     */
    public function testExists_True()
    {
        $record = App::make('User');
        $record->setExists(true);

        $this->assertTrue($record->exists());
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Record::makeNoneExists
     * @group exists
     */
    public function testMakeNoneExists()
    {
        $record = App::make('User');
        $record->setExists(true);

        $record->makeNoneExists();
        $this->assertTrue($record->isNew());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::makeNoneExists
     * @group exists
     * @group related
     */
    public function testMakeNoneExists_TouchRelatedRecord()
    {
        $record = App::make('User');
        $record->setExists(true);

        $patient = App::make('Patient');
        $patient->setExists(true);

        $this->getPrivateProperty($record, 'related')->setValue($record, array('patient' => $patient));

        $record->makeNoneExists(true);

        $this->assertTrue($record->isNew());
        $this->assertTrue($record->patient->isNew());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::makeNoneExists
     * @group exists
     * @group related
     * @group collection
     */
    public function testMakeNoneExists_TouchRelatedCollection()
    {
        $record = \App::make('User');
        $record->setExists(true);

        $patient1 = \App::make('Patient');
        $patient1->setExists(true);

        $patient2 = \App::make('Patient');
        $patient2->setExists(true);

        $this->getPrivateProperty($record, 'related')->setValue($record, array('patients' => new \Netinteractive\Elegant\Model\Collection(array($patient1, $patient2))));

        $record->makeNoneExists(true);

        $this->assertTrue($record->isNew());
        foreach ($record->patients AS $patient){
            $this->assertTrue($patient->isNew());
        }

    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::makeNoneExists
     * @group display
     * @group filter
     */
    public function testDisplay()
    {
        $record = App::make('User');
        $record->fill(
            array(
                'login' => 'User 11',
                'email' => 'user11@hot.com',
            )
        );

        $this->assertEquals('USER 11',$record->display('login', array('upper'), false));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::initAttributes
     * @group attribute
     * @group init
     */
    public function testInitAttributes()
    {
        $record = App::make('Patient');
        $record->fill(
            array(
                'user__id' => '5',
                'pesel' => '47020915916',
            )
        );

        $attr = $record->getAttributes();

        $this->assertEquals(6, count($attr));
        $this->assertTrue(array_key_exists(\Netinteractive\Elegant\Tests\Models\Tu\Blueprint::$createdAt, $attr));
        $this->assertTrue(array_key_exists(\Netinteractive\Elegant\Tests\Models\Tu\Blueprint::$updatedAt, $attr));
        $this->assertTrue(array_key_exists(\Netinteractive\Elegant\Tests\Models\Tu\Blueprint::$deletedAt, $attr));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::getAttributes
     * @group attribute
     * @group get
     */
    public function testGetAttributes()
    {
        $record = App::make('User');
        $record->fill(
            array(
                'first_name' => 'John',
                'login' => 'User 11',
                'email' => 'user11@hot.com',
            )
        );

        $this->assertEquals(8, count($record->getAttributes()));
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Record::getAttributesKeys
     * @group attribute
     * @group get
     */
    public function testGetAttributesKeys()
    {
        $record = App::make('User');
        $record->fill(
            array(
                'first_name' => 'John',
                'login' => 'User 11',
                'email' => 'user11@hot.com',
            )
        );

        $fields = array_keys($record->getBlueprint()->getFields());
        $expected = array();
        foreach ($fields AS $field){
            if (!$record->getBlueprint()->isExternal($field)){
                $expected[] = $field;
            }
        }


        $this->assertEquals($expected, $record->getAttributesKeys());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::getExternals
     * @group attribute
     * @group external
     * @group get
     */
    public function testGetExternals()
    {
        $record = App::make('User');
        $record->fill(
            array(
                'first_name' => 'John',
                'login' => 'User 11',
                'email' => 'user11@hot.com',
                'age' => 23
            )
        );

        $this->assertEquals(3, count($record->getExternals()));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::syncOriginal
     * @group attribute
     * @group original
     * @group get
     * @group sync
     */
    public function testSyncOriginal()
    {
        $record = App::make('User', array(array(
            'first_name' => 'John',
            'login' => 'User 11',
            'email' => 'user11@hot.com',
            'age' => 23
        )));


        $originals = $record->getOriginals();

        $this->assertTrue(isSet($originals['first_name']));
        $this->assertEquals('John', $originals['first_name']);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::syncOriginal
     * @group attribute
     * @group original
     * @group sync
     */
    public function testSyncOriginal_synchronizeTimestamps()
    {
        $record = App::make('Patient');
        $mock = $this->getMockBuilder(get_class($record))
            ->setMethods( array('synchronizeTimestamps'))
            ->getMock()
        ;

        $mock->expects($this->exactly(1))
            ->method('synchronizeTimestamps')
            ->withAnyParameters()
        ;

        $mock->syncOriginal();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::syncOriginal
     * @group attribute
     * @group original
     * @group dirty
     * @group sync
     */
    public function testSyncOriginal_CleanDirty()
    {
        $record = App::make('User');

        $record->fill(
            array(
                'first_name' => 'John',
                'login' => 'User 11',
                'email' => 'user11@hot.com',
                'age' => 23
            )
        );

        $originalDirty = $record->getDirty();

        $record->syncOriginal();

        $reflectedProperty = $this->getPrivateProperty($record, 'dirty');
        $dirty = $reflectedProperty->getValue($record);

        $this->assertNotEmpty($originalDirty);
        $this->assertEmpty($dirty);
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::getOriginals
     * @group attribute
     * @group original
     * @group get
     */
    public function testGetOriginals()
    {
        $record = App::make('User', array(
            array(
                'first_name' => 'John',
                'login' => 'User 11',
                'email' => 'user11@hot.com',
                'age' => 23
            )
        ));


        $record->getOriginals();

        $record->first_name = 'Adam';
        $original = $record->getOriginals();

        $this->assertTrue(isSet($original['first_name']));
        $this->assertEquals('John', $original['first_name']);
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::isAttribute
     * @group attribute
     * @group is
     */
    public function testIsAttribute_WithBluePrint_True()
    {
        $record = App::make('User');


        $this->assertTrue($record->isAttribute('login'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::isAttribute
     * @group attribute
     * @group is
     */
    public function testIsAttribute_WithBluePrint_False()
    {
        $record = App::make('User');


        $this->assertFalse($record->isAttribute('login2'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::isAttribute
     * @group attribute
     * @group is
     */
    public function testIsAttribute_NoBluePrint_True()
    {
        $record = App::make('User');
        $record->setBlueprint(null);

        $this->assertTrue($record->isAttribute('login2'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::getKey
     * @group key
     * @group get
     */
    public function testGetKey()
    {
        $record = App::make('User');
        $key = $record->getKey();

        $this->assertTrue(is_array($key));
        $this->assertTrue(array_has($key, 'id'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::getKey
     * @group key
     * @group get
     */
    public function testGetKey_Value()
    {
        $record = App::make('User', array(
            array(
                'id' => 999
            )
        ));


        $key = $record->getKey();

        $this->assertEquals(999, $key['id']);
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::fromDateTime
     * @group timestamp
     * @group datetime
     * @group format
     */
    public function testFromDateTime()
    {
        $record = App::make('User');
        $dateTime = '2015-01-01 11:12:00';

        $this->assertEquals($dateTime, $record->fromDateTime($dateTime));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::fromDateTime
     * @group timestamp
     * @group datetime
     * @group format
     */
    public function testFromDateTime_Format()
    {
        $record = App::make('User');
        $dateTime = '2015-01-01';

        $this->assertEquals('2015/01/01', $record->fromDateTime($dateTime, 'Y/m/d'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::fromDateTime
     * @group timestamp
     * @group datetime
     * @group format
     */
    public function testFromDateTime_FromTimestamp()
    {
        $record = App::make('User');
        $timestamp = strtotime('2015-01-01');

        $this->assertEquals('2015-01-01', $record->fromDateTime($timestamp, 'Y-m-d'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::fromDateTime
     * @group timestamp
     * @group datetime
     * @group format
     */
    public function testFromDateTime_FromSimpleDate()
    {
        $record = App::make('User');

        $this->assertEquals('2015-01-01 00:00:00', $record->fromDateTime('2015-01-01'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::fromDateTime
     * @group timestamp
     * @group datetime
     * @group format
     */
    public function testFromDateTime_DateTime()
    {
        $record = App::make('User');

        $dateTime = new DateTime('2015-01-01 11:12:00');

        $this->assertEquals($dateTime->format('Y-m-d H:i:s'), $record->fromDateTime($dateTime));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::createTimestamp
     * @group datetime
     * @group timestamp
     */
    public function testCreateTimestamp()
    {
        $record = App::make('User');
        $dateTime = $record->createTimestamp('2015-01-01 00:10:20');

        $this->assertTrue($dateTime instanceof \Carbon\Carbon);
        $this->assertEquals('2015-01-01 00:10:20', $dateTime->format('Y-m-d H:i:s'));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::getDirty
     * @group dirty
     * @group get
     */
    public function testGetDirty_Empty()
    {
        $record = App::make('User');


        $this->assertEmpty($record->getDirty());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::getDirty
     * @group dirty
     * @group get
     */
    public function testGetDirty_NewRecord()
    {
        $record = App::make('User', array(
           ['first_name' => 'Ken',
            'last_name' => 'Adams',
            'med__id' => 1,
            'tu__id' => 2
           ]
        ));

        $record->fill(
            array(
                'id' => 999,
                'my_filed' => 123,
                'last_name' => 'New',
                'first_name' => 'John'
            )
        );

        $this->assertEquals(3, count($record->getDirty()));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::getDirty
     * @group dirty
     * @group get
     */
    public function testGetDirty_Exists()
    {
        $record = \App::make('User',  array(array(
            'id' => 999,
            'my_filed' => 123,
            'first_name' => 'John',
            'last_name' => 'London',
        )));

        $record->setExists(true);
        $record->first_name = 'Adam';

        $this->assertEquals(1, count($record->getDirty()));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::getDirty
     * @group dirty
     * @group get
     */
    public function testGetDirty_Exists_Numerical()
    {
        $record = \App::make('User',  array(array(
            'tu__id' => 999,
            'my_filed' => 123,
            'first_name' => 'John',
            'last_name' => 'London',
        )));

        $record->setExists(true);
        $record->tu__id = '999';

        $this->assertEquals(0, count($record->getDirty()));
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Record::isDirty
     * @group dirty
     * @group is
     */
    public function testIsDirty_Record_True()
    {
        $record = \App::make('User',  array(array(
            'tu__id' => 999,
            'my_filed' => 123,
            'first_name' => 'John',
            'last_name' => 'London',
        )));

        $record->first_name = 'Adam';

        $this->assertTrue($record->isDirty());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::isDirty
     * @group dirty
     * @group is
     */
    public function testIsDirty_Record_False()
    {
        $record = \App::make('User');

        $this->assertFalse($record->isDirty());
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::isDirty
     * @group dirty
     * @group is
     */
    public function testIsDirty_Field_True()
    {
        $record = \App::make('User',  array(array(
            'tu__id' => 999,
            'my_filed' => 123,
            'first_name' => 'John',
            'last_name' => 'London',
        )));

        $record->tu__id = 1;

        $this->assertTrue($record->isDirty('tu__id'));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::isDirty
     * @group dirty
     * @group is
     */
    public function testIsDirty_Field_False()
    {
        $record = \App::make('User',  array(array(
            'my_filed' => 123,
            'first_name' => 'John',
            'last_name' => 'London',
        )));

        $this->assertFalse($record->isDirty('tu__id'));
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::isDirty
     * @group dirty
     * @group is
     */
    public function testIsDirty_ArrayOfFields_True()
    {
        $record = \App::make('User',  array(array(
            'tu__id' => 999,
            'my_filed' => 123,
            'last_name' => 'London',
        )));

        $record->fill(
            array(
                'tu__id' => 998,
            )
        );

        $this->assertTrue($record->isDirty( array('tu__id') ));
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::getRelation
     * @group related
     * @group relation
     * @group get
     */
    public function testGetRelation()
    {
        $record = \App::make('User');

        $this->assertTrue($record->getRelation('patient') instanceof Netinteractive\Elegant\Relation\Relation);
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::updateTimestamps
     * @group datetime
     * @group timestamp
     */
    public function testUpdateTimestamps_CreateTimestamp()
    {
        $record = App::make('User');

        $mock = $this->getMockBuilder(get_class($record))
            ->setMethods( array('createTimestamp'))
            ->getMock()
        ;

        $mock->expects($this->atLeast(1))
            ->method('createTimestamp')
            ->withAnyParameters()
        ;

        $mock->updateTimestamps();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::updateTimestamps
     * @group datetime
     * @group timestamp
     */
    public function testUpdateTimestamps_Force()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::toArray
     * @group array
     * @group to
     */
    public function testToArray()
    {
        $record = \App::make('User',  array(array(
            'tu__id' => 999,
            'first_name' => 123,
            'last_name' => 'London',
            'ext' => 888
        )));

        $result = $record->toArray();

       // $this->assertEquals()
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::toJson
     * @group json
     * @group to
     */
    public function testToJson()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::jsonSerialize
     * @group json
     * @group serialize
     */
    public function testJsonSerialize()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::__toString
     * @group string
     * @group to
     */
    public function testToString()
    {
        $this->markTestIncomplete();
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Record::__set
     * @group attribute
     * @group set
     */
    public function testSet()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::__get
     * @group attribute
     * @group get
     */
    public function testGet()
    {
        $this->markTestIncomplete();
    }

    /**
     * @covers \Netinteractive\Elegant\Model\Record::__isset
     * @group attribute
     * @group isset
     */
    public function testIsset()
    {
        $this->markTestIncomplete();
    }


    /**
     * @covers \Netinteractive\Elegant\Model\Record::__unset
     * @group attribute
     * @group unset
     */
    public function testUnset()
    {
        $this->markTestIncomplete();
    }


}