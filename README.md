Netinteractive\Elegant
======================

Elegant is a domain model package. He is similar to laravels Eloquent and we used a lot of Eloquent code to build this package.


## Services
* Netinteractive\Elegant\ElegantServiceProvider - registers in App most important classes:
     * ni.elegant.mapper.db - class that allows to work with databases.
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
 codecept run

Test were made on Potgresql 9.4
To run all tests at once, first you have to edit postgresql.conf and change max_locks_per_transaction. 256 works for us.

## Package versioning

x.y.z -
    x - a whole new version of package, where architecture is completely changed
    y - we change it always when package is no more compatible with lasted version (no matter why)
    z - we chage it when we fix bug or add new features and package is still compatible

## Documentation

In docs folder you can find more documentation about package.


All examples are based on this 3 classes:


## Changelog


* 2.1.23:
    * fixed:
        * Netinteractive\Elegant\Model\Record::validate - fixed bug where each message was an array added to MessageBag

* 2.1.22:
    * new: 
        * Netinteractive\Elegant\Model\Blueprint::isPk($fieldKey) - checks if field is part of primary key
    

* 2.1.20 - 2.1.21:
    * changed: Netinteractive\Elegant\Model\Record::toArray($displayFilters=true) - display filters now by default are set to true

* 2.1.19:
    * fixed: Netinteractive\Elegant\Mapper\DbMapper::search
             Now if we send data in format record.field = value - \App::make($record) will be used to create record class.
             If we send data in format field = value - mapper current record class will be used.

* 2.1.18:
    * new: Netinteractive\Elegant\Model\Record - added methods to check if field is specific type.

* 2.1.17:
    * new: 
        * Netinteractive\Elegant\Http\CrudTrait - now returns \Response::build()
        * Netinteractive\Elegant\Model\Collection::toArray($displayFilters=false) - now can apply display filters on items if they are instance of Record
        * Netinteractive\Elegant\Model\Record::toArray($displayFilters=false) - now can apply display filters
        * Netinteractive\Elegant\Model\Blueprint::isProtected($key) - checks if field has 'protected' parameter and its true. 
          Protected feature isn't used by Elegant. It should be used by other mechanisms in example: you can use to defined if fields are visible somwhere in app.

* 2.1.16:
    * fixed: Netinteractive\Elegant\Mapper\DbMapper::search - fixed bug where not mapper record was used to build query.
    * new: Netinteractive\Elegant\Http\CrudTrait - trait that will provide CRUD for yours controlllers

* 2.1.15:
    * change: we haved changed jeremeamia/superclosure package to opis/closure. It gave us 4x performance boost when cache:config is not used.

* 2.1.9 - 2.1.14:
    * fixed: the way how ElegantServiceProvider publishes and merges config file

* 2.1.8: 
     * fixed: codeception configuration file
     * new: abstraction layer for business logic \Netinteractive\Elegant\Model\Provider

* 2.1.7 : 
    * new: new fill filters: emptyToFalse, emptyToZero

* 2.1.6 : 
    * fixed: bug when db mapper tried to perform update when none of attributes has changed.

* 2.1.5 : 
    * fixed: db mapper find bug. we tried to created record when we already had one from model query builder.

* 2.1.3 - 2.1.4 : 
    * fixed: filter bug. there was bug in filter list cleaning code.

* 2.1.0 - 2.1.2 : 
    * new: hashers added.

* 2.0.0 : 
    * first stable version. realsed 15.03.2016.