<?php



class DbMapperTest extends ElegantTest
{

    /**
     * Single field PK find test
     */
    public function testFindSimplePk()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $record = $dbMapper->find(1);

        $this->assertEquals(1,$record->id);
    }


    /**
     * Multiple field PK find test
     */
    public function testFindMultiPk()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $record = $dbMapper->find(array('id'=>1, 'patient__id'=>1));

        $this->assertEquals(1,$record->id);
        $this->assertEquals(1,$record->patient__id);
    }

    /**
     * FindMany test 1
     **/
    public function testFindManySimple()
    {
        $searchParams = array(
            'PatientData' => array('zip_code'=>'00-002')
        );

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $searchResult = $dbMapper->findMany($searchParams);

        $this->assertEquals(2, count($searchResult));
    }

    /**
     * FindMany test 2
     **/
    public function testFindManyComplex()
    {
        $searchParams = array(
            'PatientData' => array(
                'zip_code' => '00-002',
                'med__id' => 1,
            )
        );

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $searchResult = $dbMapper->findMany($searchParams);

        if (count($searchResult) != 1){
            $this->assertEquals(1, count($searchResult));
        }
    }

    /**
     * With test 1
     */
    public function testWith()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $results = $dbMapper->where('patient_data.id', '=', 1)
            ->with('patient.user')
            ->get()
        ;

        $this->assertTrue(isSet($results[0]));
        $this->assertEquals(1, $results[0]->id);

        $this->assertTrue(isSet($results[0]->patient));
        $this->assertTrue(isSet($results[0]->patient->user));

        $this->assertEquals(1, count($results[0]->patient));
        $this->assertEquals(1, count($results[0]->patient->user));


        $this->assertEquals(1, $results[0]->patient->id);
        $this->assertEquals(1, $results[0]->patient->user->id);
    }

    /**
     * Delete test 1
     */
    public function testRecordDelete()
    {
        DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $record = $dbMapper->find(1);

        $dbMapper->delete( $record );

        $record2 = $dbMapper->find(1);

        $this->assertNull($record2);

        DB::rollback();
    }

    /**
     * Delete test 2
     */
    public function testRecordDeleteModifiedQuery()
    {
        DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $dbMapper->where('med.id', '=', 2);
        $record = $dbMapper->find(1);

        $dbMapper->delete( $record );

        $record2 = $dbMapper->find(1);

        $this->assertNull($record2);

        DB::rollback();
    }

    public function testSoftDelete()
    {

    }


    /**
     * Delete test 2
     */
    public function testSimpleQueryBuilderDelete()
    {
        DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');


        $dbMapper->getQuery()->delete( 1 );

        $record2 = $dbMapper->find(1);

        $this->assertNull($record2);

        DB::rollback();
    }

    /**
     * Delete test 2
     */
    public function testComplexQueryBuilderDelete()
    {
        DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');


        $result = $dbMapper->getQuery()->delete( array('tu__id'=>1, 'med__id'=>1) );
        $this->assertEquals(1, $result);

        DB::rollback();

        DB::beginTransaction();

        $result = $dbMapper->getQuery()->delete( array('med__id'=>1) );
        $this->assertEquals(2, $result);


        DB::rollback();
    }

    /**
     * set record class test 1
     */
    public function testSetRecordClass()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $dbMapper->setRecordClass('Tu');
        $record = $dbMapper->find(1);

        $this->assertEquals('Netinteractive\Elegant\Tests\Models\Tu\Record', get_class($record));
    }

    /**
     *  create record test 1
     */
    public function testCreateRecord()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $record = $dbMapper->createRecord(array('user__id'=>1, 'pesel'=>'54062609749'));

        $this->assertEquals('Netinteractive\Elegant\Tests\Models\Patient\Record', get_class($record));
        $this->assertEquals(1, $record->user__id);
        $this->assertEquals('54062609749', $record->pesel);
    }


    /**
     * create collection of records test 1
     */
    public function testCreateMany()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $result = $dbMapper->createMany(
            array(
                array('user__id'=>1, 'pesel'=>'54062609749'),
                array('user__id'=>2, 'pesel'=>'00293016049'),
                array('user__id'=>3, 'pesel'=>'03310505094')
            )

        );

        $this->assertEquals('Netinteractive\Elegant\Model\Collection', get_class($result));
        $this->assertEquals(3, count($result));
    }



}
