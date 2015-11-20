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
}