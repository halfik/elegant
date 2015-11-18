`
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::mergeBindings
     * @group binding
     */
    public function testNewQuery()
    {
        $q = $this->builder->newQuery();

        $this->assertTrue( $q instanceof \Netinteractive\Elegant\Db\Query\Builder );
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::whereRaw
     * @group where
     */
    public function testWhereRaw()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->whereRaw('med__id = 2');

        $result = $q->get();

        $this->assertEquals(1 , count($result));

    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::whereRaw
     * @group where
     */
    public function testWhereRaw_Bindings()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->whereRaw('med__id = ?', array(2));

        $result = $q->get();

        $this->assertEquals(1 , count($result));
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::addBinding
     * @group binding
     */
    public function testAddBinding()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->whereRaw('id = ?');
        $q->addBinding(1);

        $result = $q->get();

        $this->assertEquals(1 , count($result));
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::addBinding
     * @group binding
     */
    public function testAddBinding_Alias()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->whereRaw('id = ?', array(), 'and', 'my_alias');
        $q->addBinding(1, 'where', 'my_alias');

        $result = $q->get();

        $this->assertEquals(1 , count($result));
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::addBinding
     * @group binding
     */
    public function testAddBinding_Array()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->whereRaw('id = ? OR id = ?');
        $q->addBinding(array(1,2));

        $result = $q->get();

        $this->assertEquals(2 , count($result));
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::addBinding
     * @group binding
     */
    public function testAddBinding_ArrayAlias()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->whereRaw('id = ? or id = ?', array(), 'and', 'my_alias');
        $q->addBinding(array(1,2), 'where', 'my_alias');

        $result = $q->get();

        $this->assertEquals(2 , count($result));
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::addBinding
     * @group binding
     * @expectedException InvalidArgumentException
     */
    public function testAddBinding_InvalidType()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->whereRaw('id = ? or id = ?', array(), 'and', 'my_alias');
        $q->addBinding(array(1,2), 'where2', 'my_alias');
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::getBinding
     * @group binding
     */
    public function testGetBindings()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->whereRaw('id = ? or id = ?', array(), 'and', 'my_alias');
        $q->addBinding(array(1,2), 'where', 'my_alias');

        $bindings = $q->getBindings();
        $this->assertContains(1, $bindings);
        $this->assertContains(2, $bindings);
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::getRawBindings
     * @group binding
     */
    public function testGetRawBindings()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->whereRaw('id = ? or id = ?', array(1,2), 'and');

        $bindings = $q->getRawBindings();
        ;
        $this->assertArrayHasKey('where', $bindings);
        $this->assertEquals(2, count($bindings['where']));
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::getRawBindings
     * @group binding
     */
    public function testGetRawBindings_WithAlias()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->whereRaw('id = ? or id = ?', array(), 'and', 'my_alias');
        $q->addBinding(array(1,2), 'where', 'my_alias');

        $bindings = $q->getRawBindings();
        ;
        $this->assertArrayHasKey('where', $bindings);
        $this->assertArrayHasKey('my_alias', $bindings['where']);
        $this->assertEquals(2, count($bindings['where']['my_alias']));
    }



    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::from
     * @group from
     */
    public function testFrom()
    {
        $q = $this->builder->newQuery();
        $q->from('user');


        $this->assertEquals('user', $q->getFrom());
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::from
     * @group from
     */
    public function testFrom_Query()
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::from
     * @group from
     */
    public function testFrom_QueryAlias()
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::from
     * @group from
     */
    public function testFrom_QueryAliasBindings()
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::mergeBindings
     * @group from
     */
    public function testMergeBindings()
    {
        $q = $this->builder->newQuery();
        $q->addBinding(9);

        $q2 = $this->builder->newQuery();
        $q2->addBinding(5);

        $q->mergeBindings($q2);
        $bindings = $q->getBindings();

        $this->assertContains(9, $bindings);
        $this->assertContains(5, $bindings);
    }


    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::addWith
     * @group with
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::find
     * @group get
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::find
     * @group get
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::find
     * @group get
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::find
     * @group get
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::delete
     * @group delete
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::delete
     * @group delete
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::delete
     * @group delete
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::first
     * @group get
     */
    public function testFirst()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');

        $result = $q->first();
        $this->assertEquals(1 , $result->id);
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::first
     * @group get
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::getFrom
     * @group from
     */
    public function testGetFrom()
    {
        $q = $this->builder->newQuery();
        $q->from('test');

        $this->assertEquals('test' , $q->getFrom());
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::getComments
     * @group comment
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
     * @covers \Netinteractive\Elegant\Db\Query\Builder::setBinding
     * @group biding
     */
    public function testSetBindings()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->where('patient_data.first_name', '=', 'Adam', 'and', 'my_alias');

        $bidingResponse = $q->setBinding('where','my_alias', 'John');
        $result = $q->get();

        $this->assertTrue($bidingResponse);
        $this->assertEquals(1 , count($result));
        $this->assertEquals('John' , $result[0]->first_name);
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::setBinding
     * @group biding
     */
    public function testSetBindings_False()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->where('patient_data.first_name', '=', 'Adam', 'and', 'my_alias');

        $bidingResponse = $q->setBinding('where','my_alias2', 'xxx');

        $this->assertFalse($bidingResponse);
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::setWheres
     * @group where
     */
    public function testSetWheres()
    {
        $method = $this->getPrivateMethod('\Netinteractive\Elegant\Db\Query\Builder', 'setWheres');

        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->where('id', '=', 1);

        $method->invokeArgs($q, array( array( array(
            "type" => "Basic",
            "column" => "first_name",
            "operator" => "=",
            "value" => 'John',
            "boolean" => "and"
        )) ));

        $result = $q->get();

        $this->assertEquals(1 , count($result));
        $this->assertEquals('John' , $result[0]->first_name);
    }


    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::getWheres
     * @group where
     */
    public function testGetWheres()
    {
        $method = $this->getPrivateMethod('\Netinteractive\Elegant\Db\Query\Builder', 'getWheres');

        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->where('id', '=', 1);

        $result = $method->invokeArgs($q, array());

        $this->assertEquals(1 , count($result));
        $this->assertEquals('id' , $result[0]['column']);
        $this->assertEquals('=' , $result[0]['operator']);
        $this->assertEquals(1 , $result[0]['value']);
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::clearWheres
     * @group where
     */
    public function testClearWheres()
    {
        $method = $this->getPrivateMethod('\Netinteractive\Elegant\Db\Query\Builder', 'clearWheres');

        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->where('id', '=', 1);

        $method->invokeArgs($q, array());

        $method = $this->getPrivateMethod('\Netinteractive\Elegant\Db\Query\Builder', 'getWheres');
        $result = $method->invokeArgs($q, array());

        $this->assertTrue(empty($result));
    }






    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::orWhereRaw
     * @group where
     */
    public function testOrWhereRaw()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->whereRaw('med__id = 2');
        $q->orWhereRaw('first_name = \'John\'');

        $result = $q->get();

        $this->assertEquals(2 , count($result));
    }



    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::whereNotBetween
     * @group where
     */
    public function testWhereBetween()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->whereBetween('med__id', array(1,2));

        $result = $q->get();

        $this->assertEquals(3 , count($result));
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::orWhereNotBetween
     * @group where
     */
    public function testOrWhereBetween()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->whereRaw('id = 1');
        $q->orWhereBetween('id', array(2,4));

        $result = $q->get();

        $this->assertEquals(4 , count($result));
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::orWhereNotBetween
     * @group where
     */
    public function testWhereNotBetween()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->whereNotBetween('id', array(1,4));

        $result = $q->get();

        $this->assertEquals(1 , count($result));
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::orWhereNotBetween
     * @group where
     */
    public function testOrWhereNotBetween()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->whereRaw('id = 1');
        $q->orWhereNotBetween('id', array(2,4));

        $result = $q->get();

        $this->assertEquals(2 , count($result));
    }



    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::whereSub
     * @group where
     */
    public function testWhereSub()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->select('*');

        $args = array(
            'med__id', 'IN', function($q){
                $q->from('med');
                $q->select('id');
            }, 'and'
        );

        $method = $this->getPrivateMethod($q, 'whereSub');
        $method->invokeArgs($q, $args);


        $result= $this->callPrivateMethod($q, 'getWheres');

        $this->assertTrue(isSet( $result[0]));
        $this->assertEquals('Sub' , $result[0]['type']);
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::whereSub
     * @group where
     */
    public function testWhereSub_Alias()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->select('*');

        $args = array(
            'med__id', 'IN', function($q){
                $q->from('med');
                $q->select('id');
            }, 'and', 'test'
        );

        $method = $this->getPrivateMethod($q, 'whereSub');
        $method->invokeArgs($q, $args);

        $result= $this->callPrivateMethod($q, 'getWheres');

        $this->assertTrue(isSet( $result['test']));
        $this->assertEquals('Sub' , $result['test']['type']);
    }



    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::addNestedWhereQuery
     * @group where
     */
    public function testAddNestedWhereQuery()
    {

        $q = $this->builder->newQuery();
        $q->from('user');


        $q2 = $this->builder->newQuery();
        $q2->from('med');
        $q2->where('id', '=', 1);

        $q->addNestedWhereQuery($q2);


        $result = $q->first();

        $this->assertTrue(isSet($result->id));
        $this->assertEquals(1, $result->id);
    }


    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::whereNested
     * @group where
     */
    public function testWhereNested()
    {
        $q = $this->builder->newQuery();
        $q->from('user');
        $q->whereNested(function($q){
            $q->where('id', '=', 1);
            $q->orWhere('id', '=', 2);
        });


        $result = $q->get();
        $this->assertEquals(2, count($result));
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::whereExists
     * @group where
     */
    public function testWhereExists()
    {
        $q = $this->builder->newQuery();
        $q->from('user');

        $q->whereExists(function($q2){
            $q2->from('patient');
            $q2->where('id', '=', 1);
        });


        $result = $q->get();
        $this->assertEquals(5, count($result));
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::where
     * @group where
     */
    public function testWhere_ColumnArray()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->where(array(
            'patient_data.first_name' => 'Adam',
            'med__id' => 1
        ));

        $result = $q->get();
        $this->assertEquals(1 , count($result));
        $this->assertEquals('Adam' , $result[0]->first_name);
        $this->assertEquals(1 , $result[0]->med__id);

    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::where
     * @group where
     */
    public function testWhere_TwoArgs()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->where('patient_data.med__id', 1);

        $result = $q->get();
        $this->assertEquals(2 , count($result));
        $this->assertEquals('John' , $result[0]->first_name);
        $this->assertEquals('Adam' , $result[1]->first_name);
        $this->assertEquals(1 , $result[0]->med__id);
    }


    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::where
     * @group where
     */
    public function testWhere_ClosureColumn()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->where('med__id','IN',function($q){
            $q->from('med');
            $q->select('id');

        });
        $q->get();

        $result = $q->get();

        $this->assertEquals(3 , count($result));
    }

    /**
     * @covers \Netinteractive\Elegant\Db\Query\Builder::orWhere
     * @group where
     */
    public function testOrWhere()
    {
        $q = $this->builder->newQuery();
        $q->from('patient_data');
        $q->where('med__id','=',1);
        $q->orWhere('med__id', '=', 2);

        $result = $q->get();

        $this->assertEquals(3 , count($result));
    }
}