<?php
use Illuminate\Database\Schema\Blueprint;
$serializer = new SuperClosure\Serializer(null, null);


return array(
    'tables' => array(
        'user' => $serializer->serialize(function(){
            if(Schema::hasTable('user')){
                DB::statement(DB::raw('DROP TABLE "user" CASCADE;'));
            }


            Schema::create('user', function(Blueprint $table)
            {
                $table->increments('id');
                $table->string('login');
                $table->string('email');
                $table->string('password');
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->integer('med__id')->nullable();
                $table->integer('tu__id')->nullable();
            });
        }),
        'med' => $serializer->serialize(function(){
            Schema::dropIfExists('med');

            Schema::create('med', function(Blueprint $table)
            {
                $table->increments('id');
                $table->string('name',100);
                $table->string('city',100);
                $table->string('street',100);
                $table->char('zip_code',6);
                $table->char('nip',10);
                $table->string('regon',14);
                $table->string('krs',20);
                $table->string('spokesman',100);
                $table->string('phone',20);
                $table->string('cell_phone',20)->nullable();
                $table->string('email',150);

                $table->timestamps();
                $table->timestamp('deleted_at')->nullable();

            });
        }),
        'med_personnel' => $serializer->serialize(function(){
            Schema::dropIfExists('med_personnel');

            Schema::create('med_personnel', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer('user__id');
                $table->integer('med__id');
                $table->string('first_name',50);
                $table->string('last_name',100);

            });
        }),
        'med_science_degree' => $serializer->serialize(function(){
            Schema::dropIfExists('med_science_degree');

            Schema::create('med_science_degree', function(Blueprint $table)
            {
                $table->increments('id');
                $table->string('name',250);

            });
        }),
        'med_personnel__med_sience_degree' => $serializer->serialize(function(){
            Schema::dropIfExists('med_personnel__med_sience_degree');

            Schema::create('med_personnel__med_sience_degree', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer('med_personnel__id');
                $table->integer('med_sience_degree__id');

            });
        }),
        'tu' => $serializer->serialize(function(){
            Schema::dropIfExists('tu');

            Schema::create('tu',function(Blueprint $table){
                $table->increments('id');
                $table->string('name',100);
                $table->string('city',100)->nullable();
                $table->char('zip_code',6)->nullable();
                $table->string('street',100)->nullable();
                $table->char('nip',10)->nullable();
                $table->string('regon',14)->nullable();
                $table->string('krs',20)->nullable();
                $table->string('phone',20)->nullable();
                $table->string('mobile',20)->nullable();
                $table->string('email',150)->nullable();
                $table->string('main_representative',150)->nullable();

                $table->timestamps();
                $table->timestamp('deleted_at')->nullable();
            });
        }),
        'patient' => $serializer->serialize(function(){
            Schema::dropIfExists('patient');

            Schema::create('patient',function(Blueprint $table){
                $table->increments('id');
                $table->integer('user__id')->nullable();
                $table->char('pesel',11)->unique();

                $table->timestamps();
                $table->timestamp('deleted_at')->nullable();

            });
        }),
        'patient_data' => $serializer->serialize( function(){
            Schema::dropIfExists('patient_data');

            Schema::create('patient_data',function(Blueprint $table){
                $table->increments('id');
                $table->integer('patient__id');
                $table->integer('med__id')->nullable();
                $table->integer('tu__id')->nullable();
                $table->string('first_name',100);
                $table->string('last_name',100);
                $table->date('birth_date');
                $table->char('zip_code',6);
                $table->string('city',100);
                $table->string('street',100);
                $table->string('email',150)->nullable();
                $table->string('phone',20);
                $table->text('notes')->nullable();

                $table->timestamps();
                $table->timestamp('deleted_at')->nullable();

                $table->dropPrimary();
                $table->primary(array('id','patient__id'));

            });
        }),

    ),
    'data' => array(
        #User
        '\Netinteractive\Elegant\Tests\Models\User\Record' => array(
            array(
                'data' => array(
                    'id'=>1,
                    'login' => 'User 1',
                    'email' => 'user1@hot.com',
                    'password' => 'user1@hot.com',
                    'first_name' =>  'User',
                    'last_name' => 'One',
                )
            ),
            array(
                'data' => array(
                    'id'=>2,
                    'login' => 'User 2',
                    'email' => 'user2@hot.com',
                    'password' => 'user2@hot.com',
                    'first_name' =>  'User',
                    'last_name' => 'Two',
                )
            ),
            array(
                'data' => array(
                    'id'=>3,
                    'login' => 'User 3',
                    'email' => 'user3@hot.com',
                    'password' => 'user3@hot.com',
                    'first_name' =>  'User',
                    'last_name' => 'Tree',
                )
            ),
            array(
                'data' => array(
                    'id'=>4,
                    'login' => 'User 4',
                    'email' => 'user4@hot.com',
                    'password' => 'user4@hot.com',
                    'first_name' =>  'User',
                    'last_name' => 'Four',
                    'tu__id' => 1
                )
            ),
            array(
                'data' => array(
                    'id'=>5,
                    'login' => 'User 5',
                    'email' => 'user5@hot.com',
                    'password' => 'user5@hot.com',
                    'first_name' =>  'User',
                    'last_name' => 'Five',
                    'med__id' => 1
                )
            )
        ),
        #Med
        '\Netinteractive\Elegant\Tests\Models\Med\Record' => array(
            array(
                'data' => array(
                    'id'=>1,
                    'name' => 'Med 1',
                    'email' => 'med@med1.com',
                    'city' => 'Warsaw',
                    'street' => 'Unknown 1',
                    'zip_code' =>  '00-111',
                    'nip' => '2743424750',
                    'regon' => '63985222839628',
                    'krs' => 'krs1',
                    'spokesman' => 'med 1 spokesman',
                    'phone' => '+48 600 10 10 10'
                )
            ),
            array(
                'data' => array(
                    'id'=>2,
                    'name' => 'Med 2',
                    'email' => 'med@med2.com',
                    'city' => 'Warsaw',
                    'street' => 'Unknown 2',
                    'zip_code' =>  '00-222',
                    'nip' => '1283954829',
                    'regon' => '01594320168108',
                    'krs' => 'krs2',
                    'spokesman' => 'med 2 spokesman',
                    'phone' => '+48 600 20 20 20'
                )
            )
        ),
        #MedPersonel
        '\Netinteractive\Elegant\Tests\Models\MedPersonnel\Record' => array(
            array(
                'data' => array(
                    'id'=>1,
                    'user__id' => 5,
                    'med__id' => 1,
                    'first_name' => 'Greg',
                    'last_name' => 'Johnson',
                )
            ),
            array(
                'data' => array(
                    'id'=>2,
                    'user__id' => 3,
                    'med__id' => 2,
                    'first_name' => 'Adam',
                    'last_name' => 'Johnson',
                )
            ),
        ),
        #MedPersonel
        '\Netinteractive\Elegant\Tests\Models\MedScienceDegree\Record' => array(
            array(
                'data' => array(
                    'id'=>1,
                    'name' => 'degree 1'
                )
            ),
            array(
                'data' => array(
                    'id'=>2,
                    'name' => 'degree 2'
                )
            ),
            array(
                'data' => array(
                    'id'=>3,
                    'name' => 'degree 3'
                )
            )
        ),
        #TU
        '\Netinteractive\Elegant\Tests\Models\Tu\Record' => array(
            array(
                'data' => array(
                    'id'=>1,
                    'name' => 'Tu 1',
                    'email' => 'tu@tu.com',
                    'city' => 'Warsaw',
                    'street' => 'Tu Street 1',
                    'zip_code' =>  '11-111',
                    'nip' => '8944895519',
                    'regon' => '29834542376272',
                    'krs' => 'tu krs 1',
                    'spokesman' => 'tu 1 spokesman',
                    'phone' => '+48 500 10 10 10'
                )
            ),
            array(
                'data' => array(
                    'id'=>2,
                    'name' => 'Tu 2',
                    'email' => 'tu@tu.com',
                    'city' => 'Berlin',
                    'street' => 'Unknown 2',
                    'zip_code' =>  '22-222',
                    'nip' => '1484171040',
                    'regon' => '17263775889002',
                    'krs' => 'tu krs 2',
                    'spokesman' => 'med 2 spokesman',
                    'phone' => '+48 500 20 20 20'
                )
            )
        ),
        #Patient
        '\Netinteractive\Elegant\Tests\Models\Patient\Record' => array(
            array(
                'data' => array(
                    'id'=>1,
                    'user__id' => 1,
                    'pesel' => '92091811263'
                )
            ),
            array(
                'data' => array(
                    'id'=>2,
                    'user__id' => 2,
                    'pesel' => '30090416782'
                )
            ),
        ),
        #PatientData
        '\Netinteractive\Elegant\Tests\Models\PatientData\Record' => array(
            array(
                'data' => array(
                    'id'=>1,
                    'patient__id' => 1,
                    'med__id' => 1,
                    'tu__id' => 1,
                    'first_name' => 'John',
                    'last_name' => 'First',
                    'birth_date' => '1970-02-12',
                    'zip_code' => '00-001',
                    'city' => 'New York',
                    'street' => 'First Street',
                    'email' => 'one@patient.com',
                    'phone' => '501 00 00 00',
                )
            ),
            array(
                'data' => array(
                    'id'=>2,
                    'patient__id' => 2,
                    'med__id' => 2,
                    'tu__id' => 2,
                    'first_name' => 'Adam',
                    'last_name' => 'Second',
                    'birth_date' => '1975-05-22',
                    'zip_code' => '00-002',
                    'city' => 'Moscow',
                    'street' => 'Second Street',
                    'email' => 'second@patient.com',
                    'phone' => '502 00 00 00',
                )
            ),
            array(
                'data' => array(
                    'id'=>3,
                    'patient__id' => 2,
                    'med__id' => 1,
                    'tu__id' => 2,
                    'first_name' => 'Adam',
                    'last_name' => 'Second',
                    'birth_date' => '1975-05-22',
                    'zip_code' => '00-002',
                    'city' => 'Moscow',
                    'street' => 'Second Street',
                    'email' => 'second@patient.com',
                    'phone' => '502 20 00 00',
                )
            ),
        ),


    )
);