<?php


class RecordTest extends ElegantTest
{
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

    /**
     * @covers \Netinteractive\Elegant\Model\Record::getBlueprint
     * @group blueprint
     */
    public function testGetBlueprint()
    {
        $record = App::make('User');

        $this->assertTrue($record->getBluePrint() instanceof \Netinteractive\Elegant\Model\Blueprint);
    }
}