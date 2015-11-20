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
     * @group relation
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
    public function testSetAttribute()
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
    public function testSetAttribute_External()
    {
        $record = App::make('User');
        $record->setAttribute('my_field', 2);

        $this->assertEquals(2, $record->my_field);
    }



    /**
     * @covers \Netinteractive\Elegant\Model\Record::fill
     * @group fill
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


}