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
- **1.1.0**
    + Elegant::search - metoda wyszukiwania na potrzeby CrudTrait::searchInGrid, zmieniona na searchInGrid
    + Elegant::search($input, $columns=array()) - wyszukiwarka
    + Elegant::searchJoins($query) - funckja, ktora pozwala w modelu dodac joiny na potrzeby wyszukiwarki
- 1.0.17
    + Fix: Elagat::toArray(), domyslnie dziala jak w eloquencie, ale mozna mu przekazac parametr, aby odpalil filtry display
- 1.0.15
    +   CollectionServiceProvider + przeciazenie tworzenia kolekcjiw Elegancie: zamiast przez new, robimy to przez App::make().
    +   Przeciazylem Elagat::toArray(), tak aby domyslnie odpalal mechanizm filtrow display

