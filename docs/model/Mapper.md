# Mapper

Mapper is a class that is a bridge between record and data source. It means record dosn't know anything about data source.
Mapper is responsible to know how to read|write|delete from data source and how to build records and collections.

Each mapper should implement Netinteractive\Elegant\Model\MapperInterface. This interface forces on mapper class to provide most basic method to work with data source.