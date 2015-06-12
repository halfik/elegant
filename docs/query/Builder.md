# Netinteractive\Elegant\Query\Builder



* function addWith(Builder $query, $alias) - it won't work with mysql. It works with postgresql. It allows to build
statments like this one:

            WITH my_filter AS (
                SELECT * FROM uses WHERE first_name == 'John'
            )

            SELECT * FROM profiles
            INNER JOIN my_filter ON my_filter.id = profiles.user_id


* public function addComment($comment) - it adds comment to sql query.
  It's usefull when you build query in one place of your application and have mechanism in other that modifies query.
  This kind of global mechanics aren't safe in use and can lead to many bugs that gona be hard to find.
  So it's good idea to allow them to add comment to query if and when they modify it.



* function allowFilter($allow = true) - it turns on/off QueryBuilder filter mechanism. Its described in this document.


* function from($from, $alias = null) - we override this method to allow building more complex queries. Example:
            $dbMapper = new DbMapper('Patient');

            $q = $dbMapper
                ->getQuery()
                ->from($dbMapper->getQuery(), 'my_alias')
                ->whereIn('user__id',  array('213', '215'))
                ->where('created_at', '>', '2015-02-15')
            ;

            $results = $q->get();

This will give as this sql:

            SELECT
                *
            FROM
                (SELECT * FROM `patient`) AS my_alias
            WHERE
                (
                    `user__id` IN ('213', '215')
                    AND `created_at` > '2015-02-15'
                )



* setBinding($type, $alias, $value) - method allows coder to change values already binded to query. Example:
        $dbMapper = new DbMapper('Patient');

        $q = $dbMapper
            ->getQuery()
            ->whereIn('user__id',  array('213', '215'))
            ->where('created_at', '>', '2015-02-15', 'and', 'created_at')
        ;

        $q->setBinding('where', 'created_at', '2015-02-14');

        $results = $q->get();

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