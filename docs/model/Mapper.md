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

Please notice that in example DbMapper allows you to read data from one database and then save that data to another database.

