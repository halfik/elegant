# Netinteractive\Elegant\Search\Searchable

This class allow coder to define how to search the field.

Options:

* Searchable::$begins - text type search. in db case type it should build LIKE query statment: '$val%'
* Searchable::$contains - text type search. in db case type it should build LIKE query statment: '%$val%'
* Searchable::$ends - text type search. in db case type it should build LIKE query statment: '%$val'
* Other options: =, <, >, >=, <=, !=