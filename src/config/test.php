<?php
use Illuminate\Database\Schema\Blueprint;
$serializer = new SuperClosure\Serializer(null, 'ni-elegant-test');


return array(
    'tables' => array(
        'user' => $serializer->serialize(function(){
            Schema::drop('user');

            Schema::create('user', function(Blueprint $table)
            {
                $table->increments('id');
                $table->string('login');
                $table->string('email');
                $table->string('password');
                $table->timestamp('last_login')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
            });
        }),
    ),
    'data' => array(
        'User' => array(
            array(
                'data' => array(
                    'id'=>1,
                    'login' => 'test',
                    'email' => 'test@wp.pl',
                    'password' => 'test@wp.pl',
                    'first_name' =>  'Adam',
                    'last_name' => 'Nowak',
                )
            )
        ),
    )
);