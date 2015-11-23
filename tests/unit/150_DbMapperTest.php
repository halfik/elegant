<?php



class DbMapperTest extends ElegantTest
{

    /**
     * Single field PK find test
     */
    public function testFind_SimplePk()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $record = $dbMapper->find(1);

        $this->assertEquals(1,$record->id);
    }


    /**
     * Multiple field PK find test
     */
    public function testFind_MultiPk()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $record = $dbMapper->find(array('id'=>1, 'patient__id'=>1));

        $this->assertEquals(1,$record->id);
        $this->assertEquals(1,$record->patient__id);
    }

    /**
     * Find exists test
     */
    public function testFind_Exists()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $record = $dbMapper->find(1);

        $this->assertTrue($record->exists);
    }

    /**
     * FindMany test 1
     **/
    public function testFindMany_Simple()
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
    public function testFindMany_Complex()
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
     * FindMany test 3
     */
    public function testFindMany_SoftDelete()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $record = $dbMapper->find(1);

        $dbMapper->delete( $record );

        $results = $dbMapper->findMany(array(
            'city' => 'Warsaw'
        ));

        $this->assertEquals(1, count($results));

        \DB::rollback();
    }

    /**
     * FindMany exists test
     */
    public function testFindMany_Exist()
    {
        $searchParams = array(
            'PatientData' => array('zip_code'=>'00-002')
        );

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $searchResult = $dbMapper->findMany($searchParams);

        foreach ($searchResult AS $record){
            $this->assertTrue($record->exists);
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
    public function testDelete()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $record = $dbMapper->find(1);

        $dbMapper->delete( $record );

        $record2 = $dbMapper->find(1);

        $this->assertNull($record2);
        $this->assertFalse($record->exists);

        \DB::rollback();
    }

    /**
     * delete test 2
     */
    public function testDelete_NewRecord()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');

        $record = $dbMapper->createRecord(
            array(
                'id' => 999,
                'patient__id'=>1,
                'first_name'=>'a',
                'last_name'=>'b',
                'birth_date'=>'1999-01-01',
                'zip_code'=>'00-000',
                'city'=>'c',
                'street'=>'d',
                'email'=>'d@hotmail.com',
                'phone'=>'1234567',
            )
        );


        $dbMapper->save($record);


        $dbMapper->where('phone', '=', '1234567');
        $result = $dbMapper->get();

        $dbMapper->delete($record);
        \DB::beginTransaction();

        $this->assertFalse($record->exists);
    }

    /**
     * Delete test 2
     */
    public function testDelete_ModifiedQuery()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $dbMapper->where('med.id', '=', 2);
        $record = $dbMapper->find(1);

        $dbMapper->delete( $record );

        $record2 = $dbMapper->find(1);

        $this->assertNull($record2);

        \DB::rollback();
    }

    /**
     * Delete test 2
     */
    public function testDelete_SimpleQueryBuilder()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');


        $dbMapper->getNewQuery()->delete( 1 );

        $record2 = $dbMapper->find(1);

        $this->assertNull($record2);

        \DB::rollback();
    }

    /**
     * Delete test 3
     */
    public function testDelete_ComplexQueryBuilder()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');


        $result = $dbMapper->getNewQuery()->delete( array('tu__id'=>1, 'med__id'=>1) );
        $this->assertEquals(1, $result);

        \DB::rollback();

        \DB::beginTransaction();

        $result = $dbMapper->getNewQuery()->delete( array('med__id'=>1) );
        $this->assertEquals(2, $result);


        \DB::rollback();
    }

    /**
     * Soft delete test 1
     */
    public function testDelete_SoftDelete()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $record = $dbMapper->find(1);

        $dbMapper->delete( $record );

        $record2 = $dbMapper->find(1);

        $deletedAt = $record->getBlueprint()->getDeletedAt();
        $this->assertNotNull($record->$deletedAt);
        $this->assertNull($record2);

        \DB::rollback();
    }


    /**
     * Soft delete test 2
     */
    public function testDelete_BuilderSoftDelete()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $dbMapper->getNewQuery()->delete( 1 );

        $record = $dbMapper->find(1);

        $this->assertNull($record);

        \DB::rollback();
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
        $this->assertFalse($record->exists);
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

    /**
     * Fist test 1
     */
    public function testFirst()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $record = $dbMapper->first();

        $this->assertEquals('Netinteractive\Elegant\Tests\Models\Patient\Record', get_class($record));
        $this->assertEquals(1, $record->id);
    }


    /**
     * Fist test 2
     */
    public function testFirst_OrderBy()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $record = $dbMapper->orderBy('pesel')->first();

        $this->assertEquals('Netinteractive\Elegant\Tests\Models\Patient\Record', get_class($record));
        $this->assertEquals(2, $record->id);
    }

    /**
     * Fist test 3
     */
    public function testFirst_Where()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $record = $dbMapper->where('patient__id', '>', 0)->first();

        $this->assertEquals('Netinteractive\Elegant\Tests\Models\PatientData\Record', get_class($record));
        $this->assertEquals(1, $record->id);
    }



    /**
     * @covers \Netinteractive\Elegant\Mapper\DbMapper::save
     * @group db
     * @group mapper
     * @group save
     */
    public function testSave_NewRecord()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');

        $record = $dbMapper->createRecord(
            array(
                'id' => 999,
                'patient__id'=>1,
                'first_name'=>'a',
                'last_name'=>'b',
                'birth_date'=>'1999-01-01',
                'zip_code'=>'00-000',
                'city'=>'c',
                'street'=>'d',
                'email'=>'d@hotmail.com',
                'phone'=>'1234567',
            )
        );

        $dbMapper->save($record);


        $dbMapper->where('phone', '=', '1234567');
        $result = $dbMapper->get();

        $this->assertEquals(1, count($result));
        $this->assertTrue($record->exists);
        $this->assertEquals('1234567', $result[0]->phone);

        $dbMapper->delete($record);
    }



    /**
     * @covers \Netinteractive\Elegant\Mapper\DbMapper::save
     * @group db
     * @group mapper
     * @group save
     * @group exists
     */
    public function testSave_ExistsRecord()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');

        $record = $dbMapper->find(1);
        $record->name = "xxx";

        $dbMapper->save($record);

        $record2 = $dbMapper->find(1);

        $this->assertEquals('xxx', $record->name);
        $this->assertEquals('xxx', $record2->name);


        \DB::rollback();
    }


    /**
     * @covers \Netinteractive\Elegant\Mapper\DbMapper::save
     * @group db
     * @group mapper
     * @group save
     * @group timestamps
     */
    public function testSave_NewRecordTimestamps()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');

        $record = $dbMapper->createRecord(array(
            'id' => 3,
            'user__id' => 3,
            'pesel' => '61010511575'
        ));

        $dbMapper->save($record);

        $record2 = $dbMapper->find(1);

        $dateTime = new DateTime();
        $this->assertEquals($dateTime->format('Y-m-d H:i'), $record->created_at->format('Y-m-d H:i'));
        $this->assertEquals($dateTime->format('Y-m-d H:i'), $record->updated_at->format('Y-m-d H:i'));
        ;
        $this->assertEquals($dateTime->format('Y-m-d H:i'), $record2->created_at->format('Y-m-d H:i'));
        $this->assertEquals($dateTime->format('Y-m-d H:i'), $record2->updated_at->format('Y-m-d H:i'));

        \DB::rollback();
    }

    /**
     * @covers \Netinteractive\Elegant\Mapper\DbMapper::save
     * @group db
     * @group mapper
     * @group save
     * @group timestamps
     * @group exists
     */
    public function testSave_ExistsRecordTimestamps()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');

        $record = $dbMapper->find(1);
        $record->name = "My Med 1";

        sleep(1); #sleep so we can see that created_at has changed and update_at not
        $dbMapper->save($record);

        $record2 = $dbMapper->find(1);

        $dateTime = new DateTime();

        $this->assertNotEquals($dateTime->format('Y-m-d H:i:s'), $record->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals($dateTime->format('Y-m-d H:i'), $record->updated_at->format('Y-m-d H:i'));

        $this->assertNotEquals($dateTime->format('Y-m-d H:i:s'), $record2->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals($dateTime->format('Y-m-d H:i'), $record2->updated_at->format('Y-m-d H:i'));



        \DB::rollback();
    }

    /**
     * save many test 1
     */
    public function testSaveMany_Array()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $dbMapper->saveMany(
            array(
                array(
                    'id' => 4,
                    'user__id' => 4,
                    'pesel' => '96062201507'
                ),
                array(
                    'id' => 5,
                    'user__id' => 5,
                    'pesel' => '17032101458'
                ),
            )
        );

        $record1 = $dbMapper->find(4);
        $record2 = $dbMapper->find(5);

        $this->assertEquals('96062201507', $record1->pesel);
        $this->assertEquals('17032101458', $record2->pesel);

        \DB::rollback();
    }


    /**
     * save many test 2
     */
    public function testSaveMany_Collection()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $collection = new \Netinteractive\Elegant\Model\Collection();

        $record1 = $dbMapper->createRecord(array(
            'id' => 4,
            'user__id' => 4,
            'pesel' => '96062201507'
        ));

        $record2 = $dbMapper->createRecord(array(
            'id' => 5,
            'user__id' => 5,
            'pesel' => '17032101458'
        ));

        $collection->add($record1);
        $collection->add($record2);

        $dbMapper->saveMany(
            $collection
        );

        $record1 = $dbMapper->find(4);
        $record2 = $dbMapper->find(5);

        $this->assertEquals('96062201507', $record1->pesel);
        $this->assertEquals('17032101458', $record2->pesel);

        \DB::rollback();
    }

    /**
     * Search test 1
     */
    public function testSearch()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('User');
        $result = $dbMapper->search(
            array(
                'User' => array(
                    'first_name' => 'User'
                )
            )
        )->get();

        $this->assertEquals(5, count($result));

        \DB::rollback();
    }

    /**
     * Search test 2
     */
    public function testSearch_MultiField()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('User');
        $result = $dbMapper->search(
            array(
                'User' => array(
                    'first_name' => 'User',
                    'last_name'  => 'Two'
                )
            )
        )->get();

        $this->assertEquals(1, count($result));
        $this->assertEquals('User', $result[0]->first_name);
        $this->assertEquals('Two', $result[0]->last_name);

        \DB::rollback();
    }

    /**
     * Search test 3
     */
    public function testSearch_NotSearchable()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('User');
        $result = $dbMapper->search(
            array(
                'User' => array(
                    'first_name' => 'User',
                    'tu__id'  => '1'
                )
            )
        )->get();

        $this->assertEquals(5, count($result));


        \DB::rollback();
    }

    /**
     * Search test 3
     */
    public function testSearch_OrOperator()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('User');
        $result = $dbMapper->search(
            array(
                'User' => array(
                    'email' => 'user1@hot.com',
                    'last_name' => 'Five'
                )
            ), array(), 'OR'
        )->get();

        $this->assertEquals(2, count($result));


        \DB::rollback();
    }

    /**
     * get query test 1
     */
    public function testGetQuery()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $dbMapper->where('id', '=', 1);
        $q = $dbMapper->getQuery();

        $result = $q->get();

        $this->assertEquals('Netinteractive\Elegant\Model\Query\Builder', get_class($q));
        $this->assertEquals(1, count($result));

        \DB::rollback();
    }

    /**
     * get query test 2
     */
    public function testGetQuery_DeletedAt()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');

        $record = $dbMapper->find(1);
        $dbMapper->delete($record);

        $dbMapper->where('id', '=', 1);
        $q = $dbMapper->getQuery();

        $result = $q->get();

        $this->assertEquals(0, count($result));

        \DB::rollback();
    }

    /**
     * get new query test 1
     */
    public function testGetNewQuery()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $dbMapper->where('id', '=', 1);
        $q = $dbMapper->getNewQuery();

        $result = $q->get();

        $this->assertEquals('Netinteractive\Elegant\Model\Query\Builder', get_class($q));
        $this->assertEquals(2, count($result));

        \DB::rollback();
    }

    /**
     * get new query test 2
     */
    public function testGetNewQuery_DeletedAt()
    {
        \DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');

        $record = $dbMapper->find(1);
        $dbMapper->delete($record);

        $dbMapper->where('id', '=', 1);
        $q = $dbMapper->getNewQuery();

        $result = $q->get();

        $this->assertEquals(1, count($result));

        \DB::rollback();
    }

}
