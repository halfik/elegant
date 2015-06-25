# Netinteractive\Elegant\Model\Collection

This class extends Illuminate\Support\Collection.

## Methods

*  add( \Netinteractive\Elegant\Model\Record $item ) : $this

        Adds record to the collection.

* makeDirty( array $attributes=array(), bool $touchRelated=false ) : $this

        Makes all records  (and related if needed) dirty.

* makeNoneExists( bool $touchRelated=false ) : $this

        Mark records (and related if needed) as new.

