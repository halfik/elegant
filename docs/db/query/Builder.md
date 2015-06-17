# Netinteractive\Elegant\Query\Builder

We have added some functionality to original Elegant query builder. New things we've added to builder:

* we throwed away list of available clause operators, so now anything is possible when you are building where statement.
* add sql comments (Example 1)
* with statements (Example 2)
* selecting from other builder object (Example 3)
* overriding already bind where values. To do able to do so, you have to use $alias parameter when building where statement (Example 4).
* ni.elegant.db.builder.modify event that allows to modify query before execution
* we are removing double join statements


## Methods

* newQuery(): \Netinteractive\Elegant\Db\Query\Builder

        It uses IoC to create object (\App('ni.elegant.db.query.builder')), so can use own QueryBuilder class to work with package.


* from(mixed $from, $alias = null) : $this

        You can select from "tableName" or other Builder object. $alias is used when you select from other Builder object.
        If you do not specify it, by default it takes table name from passed object.


* addWith(Netinteractive\Elegant\Query\Builder $query, string $alias) : $this

        It won't work with mysql. It works with postgresql and allows to build with statement (Example 2).


* find( int|array $ids, $columns = array('*') ) : mixed

        Allows to find a single record. Id may consist of multiple fields.


* delete( int|array $ids = null) : int

        Delete records and return information how many records were deleted.


* first( array $columns = array('*') ) : \Netinteractive\Elegant\Model\Record|static|null

        Execute the query and returns the first found record.


* getFrom() : string

        Returns from value. In most cases it will be table name. But if you are selecting from another Builder, then this method will return sql string.


*  addComment( string $comment ) : $this

        It adds comment to sql query.
        It's useful when you want to build query in one place of your application and have mechanism in other that modifies query.
        This kind of global mechanics aren't safe in use and can lead to many bugs that are going to be hard to find.
        So it's good idea to allow them to add comment to query if and when they modify it.


* setBinding( string $type, string $alias, mixed $value ) : bool

        Method allows  to change values already bind to the query. See Example 3.


* addBinding(string $value, string $type = 'where', string $alias = null) : $this

        Add a binding to the query.


* where (string $column, string $operator = null, mixed $value = null, string $boolean = 'and', string $alias = null) : $this

        Allows to build where. All where methods were taken from Eloquent builder. We only add $alias to each of them as last parameter. So we won't list all of them here.

* toSql(): string

        Returns the SQL representation of the query.

* removeOrder() : $this

        Removed order from query.




Please notice that to be allowed to change value of already binded value, first you have give it an alias when using
where methods.

So why this is usefull?

When you have once place in code where query is built and other that can modify this query. Here is an example with
acl filters (we use in our projects):

        $policyPatient =  function($q, $userData){

            #we get patient row to be able to filter policy query
            $patient = App::make('Patient')->where('user__id', '=', App::make('sentry')->getUser()->id)->remember(24*60*80)->first();

            #jesli nie mamy w query odpwiedniego whera, to dodajemy - jesli mamy, zmieniamy binding
            #here we check if variable is already binded. if not then we need to add proper where statment.
            if (!$q->setBinding('where','policy.patient__id', $patient->id)){
                $q->where('policy.patient__id', '=', $patient->id);
            }
        };

This mechanism is global. This $policyPatient function will trigger only for patient acl role. So if patient role will
try to get any data from policy table - this function will trigger automaticly and modify query.


## Examples

### Example 1
     $q = \App::make('ni.elegant.db.query.builder');
    $q  ->from('patient')
        ->where('id', '>', 10)
        ->addComment('My comment')
        ->get()
    ;

    /*
        /*
        *  My comment
        */
        select * from "patient" where ("id" > '10')
    */

### Example 2

    $q1 = \App::make('ni.elegant.db.query.builder');
    $q1->from('patient')
        ->where('id', '>', 10)
        ->where('id', '<', 999)
    ;

    $q2 = \App::make('ni.elegant.db.query.builder');
    $q2->from('patient_data')
        ->addWith($q1, 'patient_with')
        ->join('patient_with', 'patient_with.id', '=', 'patient_data.patient__id')
        ->get()
    ;

    /**
        WITH patient_with AS (
            SELECT
                *
            FROM
                "patient"
            WHERE
                "id" > '10'
            AND "id" < '999'
        ) SELECT
            *
        FROM
            "patient_data"
        INNER JOIN "patient_with" ON "patient_with"."id" = "patient_data"."patient__id"
     **/

### Example 3
    $q1 = \App::make('ni.elegant.db.query.builder');
    $q1->from('patient')
        ->where('id', '>', 10)
    ;

    $q2 = \App::make('ni.elegant.db.query.builder');
    $q2->from($q1)
        ->get()
    ;

    /*
        select * from (select * from "patient" where ("id" > '10')) as patient
    */

    #OR you can use alias

    $q2 = \App::make('ni.elegant.db.query.builder');
        $q2->from($q1, 'test')
            ->get()
        ;
     /*
        select * from (select * from "patient" where ("id" > '10')) as test
     */


### Example 3

     $q = $dbMapper
        ->getQuery()
        ->whereIn('user__id',  array( '215'))
        ->whereDate('created_at', '<', '2015-02-10' ,'and', 'created_at')

    ;

    $q->setBinding('where', 'created_at', '2015-02-14');

    $results = $q->get();

    /*
        /*
        *  [Rebinding] [created_at] 2015-02-10 => 2015-02-14
        */
        select * from "patient" where ("user__id" in ('215') and date("created_at") < '2015-02-14')
    */