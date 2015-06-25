# Mapper

Mapper is a class that is a bridge between record and data source. It means record dosn't know anything about data source.
Mapper is responsible to know how to read|write|delete from data source and how to build records and collections.

So mapper main role is to separate data from data source.

Each mapper should implement Netinteractive\Elegant\Model\MapperInterface.
This interface forces on mapper class to provide most basic method to work with data source.

At this point there is only one mapper class implemented - database mapper.
Later we are going to add XML and CSV mappers.

If you are going to create own mapper, please be sure it will work with real data source.
Data source should allow read, write and delete data. If something don't allow at least one of this things, then it's not a data source.

Please notice that in example DbMapper allows you to read data from one database and then save that data to another database (Example 1)

It is possible to read records with all their related data and save them to other database. Database mapper allow this kind of operation, but
this operation is limited. This kind of migrations will copy all data, including autoincrementing files. So if you will try copy record with id=1
to the second database where you already have record with id=1 - it will fail. Basic example of this kind of data migrations: Example 2


## Examples

### Example 1

        $dbMapper = new DbMapper('Patient'); #this mappers is connected to postgresql database
        $pgSqlPatient = $dbMapper->find(119);

        $mySqlConnection = \DB::connection('mysql');

        $pgSqlPatient->exists = false;

        $dbMapper->setConnection($mySqlConnection);
        $dbMapper->save($pgSqlPatient);

        #OR
        //$mySqlDbMapper = new DbMapper('Patient', $mySqlConnection);
        //$mySqlDbMapper->save($pgSqlPatient);

### Example 2

        $mySqlConnection = \DB::connection('mysql');
        $pgSqlConnection = \DB::connection('pgsql');

        $dbMapper = new DbMapper('Patient', $pgSqlConnection); #this mappers is connected to postgresql database

        $patientCollection = $dbMapper
            ->with('patientData')
            ->limit(10)
            ->get()
        ;

        # here we have to set all records we have (main ones and related patientData) to be treated as new
        # plus we have to mark all record data as dirty (see Record.md what dirty is)
        $patientCollection->makeNoneExists(true);
        $patientCollection->makeDirty(array(), true);

        # we switch connection to mysql
        $dbMapper->setConnection($mySqlConnection);
        $dbMapper->saveMany($patientCollection, true);