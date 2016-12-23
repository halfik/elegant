Netinteractive\Elegant
======================

Elegant is a package that provides data mapper architecture model. 
He is similar to laravel's Eloquent and we used a lot of Eloquent code to build this package.

##Start
First you have to replace some laravel service providers:
* Illuminate\Database\DatabaseServiceProvider   ->  Netinteractive\Elegant\DatabaseServiceProvider
* Illuminate\Database\MigrationServiceProvider  ->  Netinteractive\Elegant\MigrationServiceProvider
* Illuminate\Database\SeedServiceProvider'      ->  Netinteractive\Elegant\SeedServiceProvider

Also some providers won't work after this chances (wil be fixed) so you have to remove them:
* Illuminate\Queue\QueueServiceProvider
* Illuminate\Notifications\NotificationServiceProvider


## Services
* Netinteractive\Elegant\ElegantServiceProvider - registers in App most important classes:
     * ni.elegant.repository - class that allows to work with databases.
     * ni.elegant.db.query.builder - database query builder.
     * ni.elegant.model.query.builder - model query builder. Responsible for relations.
     * ni.elegant.model.collection - data collection class
     * ni.elegant.model.relation.manager- class that allows to register relations translators for different data sources. ElegantServiceProvider register standard db translator.
     * ni.elegant.model.relation.translator.db - class that knows how to build database relations based on informations from blueprint
     * ni.elegant.search.db.translator - class that knows how to build and add to query proper where statements based on blueprint informations.

* Netinteractive\Elegant\FiltersServiceProvider - provides record filter mechanism (it allows to manipulate record data)
     * \Event::listen('ni.elegant.record.after.fill', 'Netinteractive\Elegant\Model\Filter\Event\Handler@fillFilters');
     * \Event::listen('ni.elegant.mapper.before.save', 'Netinteractive\Elegant\Model\Filter\Event\Handler@saveFilters');
     * \Event::listen('ni.elegant.record.display', 'Netinteractive\Elegant\Model\Filter\Event\Handler@displayFilters');
     * DisplayFilter::run - alias that allows to apply display filters on data


## Events
* Netinteractive\Elegant\Mapper\DbMapper

        ni.elegant.mapper.search.$recordClass    - event is fired in search method. You can use it to modify search query object.
        ni.elegant.mapper.saving.$recordClass    - event is fired before record is save to database.
        ni.elegant.mapper.saved.$recordClass     - event is fired after record is save to database.
        ni.elegant.mapper.updating.$recordClass  - event is fired  before record update on database.
        ni.elegant.mapper.updated.$recordClass   - event is fired after record update on database.
        ni.elegant.mapper.creating.$recordClass  - event is fired  before record is inserted to database.
        ni.elegant.mapper.created.$recordClass   - event is fired after record is inserted to database.
        ni.elegant.mapper.deleting.$recordClass  - event is fired before record is deleted from database.
        ni.elegant.mapper.deleted.$recordClass   - event is fired after record is deleted from database.
        ni.elegant.mapper.before.save            - event allows to modify data that are send to data source (but not modify record data).
        ni.elegant.mapper.touching.$recordClass  - event is fired for related record before touch
        ni.elegant.mapper.touched.$recordClass   - event is fired for related record after touch

*  Netinteractive\Elegant\Db\Query\Builder

        ni.elegant.db.builder.modify             - event allows to modify query before execution.

* Netinteractive\Elegant\Model\Record

        ni.elegant.record.before.fill                       - event allows to modify data before record is filled.
        ni.elegant.record.after.fill                        - event allows to modify record after it is filled.
        ni.elegant.record.display                           - event allows to modify data before they are displayed.
        ni.elegant.record.blueprint.before.set.$recordClass - it is fired before blueprint object is set on record
        ni.elegant.record.blueprint.before.set.$recordClass - it is fired after blueprint object is set on record

## Others
* Netinteractive\Elegant\Http\CrudTrait 
        
        trait that will provide CRUD for controllers you will apply it to

## Important
There is the requirement for naming foreign keys. If you wont meet this requirement - relations won't work.
In example if you have patient_data table where you have foreign key from patient table it has to be named: patient__id (related: table_name + __ + field_name)
Also we recommend pivot table names to looke like this: table_one__table_two

Also we recommend to use config:cache. It will increase perfomance a lot (becouse we serialize closures in config files).

## Testing
We used codeception here. To run tests: 
 codecept run unit --env=testing -f
Or on windows:
 php codecept.phar run unit --env=testing -f

Test were made on Potgresql 9.4
To run all tests at once, first you have to edit postgresql.conf and change max_locks_per_transaction. 256 works for us.

Add connection to your database config file:

     'testing' => [
        'driver' => 'pgsql',
        'host' => env('TEST_DB_HOST', 'localhost'),
        'database' => env('TEST_DB_DATABASE', 'testing'),
        'username' => env('TEST_DB_USERNAME', 'testing'),
        'password' => env('TEST_DB_PASSWORD', ''),
        'charset' => 'utf8',
        'schema' => 'public',
        'sslmode' => 'prefer',
    ],

## Package versioning
Semantic Versioning 2.0.0

## Documentation

In docs folder you can find more documentation about package.



## Changelog

* 3.0.0 - Moved all database classes from laravel to package.
          Also changed Mapper to Repository class name.

* 2.1.36
    * fixed: Declaration of Netinteractive\Elegant\Db\Query\Builder::whereRaw()
    