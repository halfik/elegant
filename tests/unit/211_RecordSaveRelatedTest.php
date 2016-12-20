<?php

/**
 * Class RecordSaveRelatedTest
 * Testy zapisu rekordow powiazanych
 */
class RecordSaveRelatedTest extends ElegantTest
{
    /**
     * @group related
     * @group save
     * @grup HasMany
     */
    public function testHasMany()
    {
        \DB::beginTransaction();
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');

        $med = \App::make('Med',  array(array(
            'name' => 'Med 33',
            'city' => 'Krakow',
            'street' => 'test',
            'zip_code' => '45-555',
            'nip' => '2627080109',
            'regon' => '1222222',
            'krs' => 'krs333',
            'spokesman' => 'aaaa',
            'phone' => '111222333',
            'email'=>'med33@med.pl'
        )));

        $user = \App::make('User',  array(array(
            'login' => 'testMed',
            'email' => 'testmed@wp.pl',
            'password' => 'test',
            'first_name' => 'John Med 1',
            'last_name' => 'London Med 1',
        )));

        $med->user = $user;
        $dbMapper->save($med, true);

        $this->assertEquals($user->med__id, $med->id);

        \DB::rollback();
    }


    /**
     * @group related
     * @group save
     * @grup HasOne
     */
    public function testHasOne()
    {
        \DB::beginTransaction();
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('User');

        $user = \App::make('User',  array(array(
            'login' => 'test',
            'email' => 'test@wp.pl',
            'password' => 'test',
            'first_name' => 'John',
            'last_name' => 'London',
        )));

        $patient = \App::make('Patient',  array(array(
            'pesel' => '03220110672',
        )));


        $user->patient = $patient;

        $dbMapper->save($user, true);

        $this->assertEquals($user->id, $patient->user__id);

        \DB::rollback();
    }

    /**
     * @group related
     * @group save
     * @group BelongsToMany
     */
    public function testBelongsToMany()
    {
        \DB::beginTransaction();
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('User');

        $user = \App::make('User',  array(array(
          'login' => 'test11',
          'email' => '11test@wp.pl',
          'password' => 'test',
          'first_name' => 'John',
          'last_name' => 'London',
        )));

        $dbMapper->save($user);

        $dbMapper = $dbMapper->setRecordClass('MedPersonnel');

        $doctor = \App::make('MedPersonnel',  array(array(
          'user__id' => $user->id,
          'med__id' => 1,
          'first_name' => 'John',
          'last_name' => 'Cash',
        )));

        $dbMapper->setRecordClass('MedScienceDegree');
        $medScienceDegree = $dbMapper->createRecord(array(
          'name' => 'new sience deg'
        ));

        $doctor->addRelated('med_degree', $medScienceDegree);

        $dbMapper->save($doctor, true);

        $dbMapper = $dbMapper->setRecordClass('MedPersonnel');
        $result = $dbMapper->with('med_degree')->find($doctor->id);


        $this->assertEquals(1, count($result->med_degree));
        $this->assertTrue($result->med_degree[0] instanceof \Netinteractive\Elegant\Tests\Models\MedScienceDegree\Record);


        $this->assertEquals($result->med_degree[0]->pivot->med_personnel__id, $doctor->id);
        $this->assertEquals($result->med_degree[0]->pivot->med_sience_degree__id, $medScienceDegree->id);

        \DB::rollback();
    }

    /**
     * @group related
     * @group save
     * @group BelongsToMany
     */
    public function testBelongsTo()
    {
        $this->markTestIncomplete("BelongsTo should be saved manualy");

        \DB::beginTransaction();
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('User');

        $user = \App::make('User',  array(array(
            'login' => 'test11',
            'email' => '11test@wp.pl',
            'password' => 'test',
            'first_name' => 'John',
            'last_name' => 'London',
        )));

        $dbMapper->save($user);

        $dbMapper->setRecordClass('MedPersonnel');


        $doctor = \App::make('MedPersonnel',  array(array(
            'user__id' => $user->id,
            'first_name' => 'John',
            'last_name' => 'Cash',
        )));

        $med = \App::make('Med',  array(array(
            'name' => 'Med 33',
            'city' => 'Krakow',
            'street' => 'test',
            'zip_code' => '45-555',
            'nip' => '2627080109',
            'regon' => '1222222',
            'krs' => 'krs333',
            'spokesman' => 'aaaa',
            'phone' => '111222333',
            'email'=>'med33@med.pl'
        )));

        $doctor->med = $med;
        $dbMapper->save($doctor, true);


        \DB::rollback();
    }
}