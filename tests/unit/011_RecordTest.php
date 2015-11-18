<?php




class RecordTest extends ElegantTest
{


    /**
     * Standard fill test
     **/
    public function testStandardFill()
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
     * Storage name based fill test
     */
    public function testStorageNameFill()
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
}