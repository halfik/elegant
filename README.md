Netinteractive\Elegant
=====================

Elegant to bazowa klasa dla modeli. Rozszerzenie Eloquenta.

## Usługi
- FiltersServiceProvider - usługa dostarcza 3 mechanizmów filtrów dla modelu:
    +   \Event::listen('eloquent.elegant.after.setAttribute: *', 'Netinteractive\Elegant\Events\EventHandler@fillFilters');
    +	\Event::listen('elegant.before.save', 'Netinteractive\Elegant\Events\EventHandler@saveFilters');
    +	\Event::listen('elegant.before.display', 'Netinteractive\Elegant\Events\EventHandler@displayFilters');

- CollectionServiceProvider - usługa binduje klase Netinteractive\Elegant\Collection jako Collection, ktora pozniej wykorzystana jest w modelu. Chwilowo nie ma zastosowania.

## Wersje
- 1.2.4
    + Zmiana w mechanizmie filtrow pol: display, fill, save - teraz mozna przekazywac do zdefiniowanych filtrow, liste parametrow:  'display' => array('date:Y-m-d')
- 1.2.3
    + Searchable::clearKeyword - fix bledu z trim
- 1.2.2
    + Searchable::clearKeyword - funckaj do oszyczania danych dla where. narazie trimuje. uzyta we wszystkich metodach Searchable.
- 1.2.1
    + Fix Elegant::search - byl problem, jesli trafialy jakies parametry jak np. numer strony, nazwa buttona itd.
- **1.2.0**
    + Elegant::search($input, $columns=array(), $operator='and', $defaultJoin=true) - doszedl parametr operatora
    + Searchable - zmienil sie caly interface. Szczegoly w kodzie.
- 1.1.1
    + do search doszeld nowy parametr: search($input, $columns=array(), $defaultJoin=true) - definiuje on, czy do zapytania dodać zdefiniowane dla searcha joiny.
- **1.1.0**
    + Elegant::search - metoda wyszukiwania na potrzeby CrudTrait::searchInGrid, zmieniona na searchInGrid
    + Elegant::search($input, $columns=array()) - wyszukiwarka
    + Elegant::searchJoins($query) - funckja, ktora pozwala w modelu dodac joiny na potrzeby wyszukiwarki
- 1.0.17
    + Fix: Elagat::toArray(), domyslnie dziala jak w eloquencie, ale mozna mu przekazac parametr, aby odpalil filtry display
- 1.0.15
    +   CollectionServiceProvider + przeciazenie tworzenia kolekcjiw Elegancie: zamiast przez new, robimy to przez App::make().
    +   Przeciazylem Elagat::toArray(), tak aby domyslnie odpalal mechanizm filtrow display


##Typy pół

- int
- float
- string
- text
- date
- dateTime
- bool
- file
- image

