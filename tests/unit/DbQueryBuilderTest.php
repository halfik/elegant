
<?php

use \Netinteractive\Elegant\Db\Query\Builder AS Builder;

/**
 * Class DbQueryBuilderTest
 */
class DbQueryBuilderTest extends ElegantTest
{
    /**
     * @var \Netinteractive\Elegant\Db\Query\Builder
     */
    protected $builder = null;

    protected function setUp()
    {
        parent::setUp();

        $connection = \App::make('db')->connection(\Config::get('database.default'));

        $processor = $connection->getPostProcessor();
        $grammar = $connection->getQueryGrammar();

        $this->builder = new Builder($connection, $grammar, $processor);
    }

    /**
     * new query test 1.0
     */
    public function testNewQuery()
    {
        $q = $this->builder->newQuery();

        $this->assertTrue( $q instanceof \Netinteractive\Elegant\Db\Query\Builder );
    }

    /**
     * from test 2.0
     */
    public function testFrom()
    {
        $q = $this->builder->newQuery();
        $q->from('user');


        $this->assertEquals('user', $q->getFrom());
    }

    /**
     * from test 2.1
     */
    public function testFromQuery()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $patientQuery = $dbMapper->getNewQuery()->orderBy('id');

        $q = $this->builder->newQuery();
        $q->from($patientQuery);
        $result =  $q->get();

        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0]->id);
        $this->assertEquals(2, $result[1]->id);
        $this->assertEquals(3, $result[2]->id);
    }

    /**
     * from test 2.2
     */
    public function testFromQueryAlias()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $patientQuery = $dbMapper->getNewQuery()->orderBy('id');

        $q = $this->builder->newQuery();
        $q->from($patientQuery, 'test');
        $result =  $q->get();

        $this->assertEquals('(select * from "patient_data" order by "id" asc) as test', (string) $q->getFrom());
        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0]->id);
        $this->assertEquals(2, $result[1]->id);
        $this->assertEquals(3, $result[2]->id);
    }

    /**
     * from test 2.3
     */
    public function testFromQueryAliasBindings()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $patientQuery = $dbMapper->getNewQuery()->where('tu_id', '=', 2)->orWhere('med__id', '=', 1);

        $q = $this->builder->newQuery();
        $q->from($patientQuery, 'test');
        $result =  $q->getBindings();

        $this->assertEquals(2, count($result));
        $this->assertEquals(2, $result[0]);
        $this->assertEquals(1, $result[1]);
    }


    /**
     * add/get with test 3.0
     */
    public function testWith()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $patientQuery = $dbMapper->getNewQuery()->where('tu_id', '=', 2)->orWhere('med__id', '=', 1);

        $q = $this->builder->newQuery();
        $q->from('patient');
        $q->addWith($patientQuery, 'patient_data_filter');


        $this->assertTrue($q->getWith('patient_data_filter') instanceof \Netinteractive\Elegant\Db\Query\Builder);
        $this->assertEquals(1 , count($q->getWith()));
    }

    /**
     * find test 4.0
     */
    public function testFind_SimpleId()
    {
        $q = $this->builder->newQuery();
        $q->from('patient');
        $result = $q->find(1);


        $this->assertTrue($result instanceof stdClass);
        $this->assertEquals(1 , $result->id);
    }

    /**
     * find test 4.1
     */
    public function testFind_ByField()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $result = $q->find(array(
            'email' => 'one@patient.com'
        ));



        $this->assertEquals('one@patient.com' , $result->email);
    }

    /**
     * find test 4.2
     */
    public function testFind_MultiKeyId()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $result = $q->find(array(
            'tu__id' => 2,
            'med__id' => 1,
        ));


        $this->assertEquals(2 , $result->tu__id);
        $this->assertEquals(1 , $result->med__id);
    }


    /**
     * find test 4.3
     */
    public function testFind_Colums()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $result = $q->find(1, array('id', 'first_name'));


        $this->assertEquals('John' , $result->first_name);
        $this->assertEquals(1 , $result->id);
        $this->assertFalse(isSet($result->last_nane));
        $this->assertEquals(2 ,count((array)$result));

    }

    /**
     * delete test 5.0
     */
    public function testDelete()
    {
        \DB::beginTransaction();

        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->delete(1);

        $q2 = $this->builder->newQuery();
        $q2->from('patient_data');

        $this->assertEquals(2 , $q2->count());

        \DB::rollback();
    }

    /**
     * delete test 5.1
     */
    public function testDelete_MultiKey()
    {
        \DB::beginTransaction();

        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->delete(
            array(
                'med__id' => 1,
                'tu__id' => 1
            )
        );

        $q2 = $this->builder->newQuery();
        $q2->from('patient_data');
        $q2->where('med__id', '=', 1);
        $q2->where('tu__id', '=', 1);

        $this->assertEquals(0 , $q2->count());

        \DB::rollback();
    }

    /**
     * delete test 5.2
     */
    public function testDelete_NoId()
    {
        \DB::beginTransaction();

        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->delete();


        $this->assertEquals(0 , $q->count());

        \DB::rollback();
    }


    /**
     * first test 6.0
     */
    public function testFirst()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');

        $result = $q->first();
        $this->assertEquals(1 , $result->id);
    }

    /**
     * first test 6.1
     */
    public function testFirst_Columns()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');

        $result = $q->first(array('id', 'first_name'));

        $this->assertEquals('John' , $result->first_name);
        $this->assertEquals(1 , $result->id);
        $this->assertFalse(isSet($result->last_nane));
        $this->assertEquals(2 ,count((array)$result));
    }

    /**
     * test get from 7.0
     */
    public function testGetFrom()
    {
        $q = $this->builder->newQuery();
        $q->from('test');

        $this->assertEquals('test' , $q->getFrom());
    }

    /**
     * add comment test 8.0
     */
    public function testAddComment()
    {
        $q = $this->builder->newQuery();
        $q->addComment('my comment');

        $comments = $q->getComments();
        $this->assertEquals(1 , count($comments));
        $this->assertEquals('my comment' , $comments[0]);
    }

    /**
     * set bidings test 9.0
     */
    public function testSetBindings()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->where('patient_data.first_name', '=', 'Adam', 'and', 'my_alias');

        $q->setBinding('where','my_alias', 'John');
        $result = $q->get();

        $this->assertEquals(1 , count($result));
        $this->assertEquals('John' , $result[0]->first_name);
    }



}