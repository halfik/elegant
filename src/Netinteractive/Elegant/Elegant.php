<?php namespace Netinteractive\Elegant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag AS MessageBag;
use Netinteractive\Elegant\Exception\ValidationException;
use Netinteractive\Elegant\Exception\AttachException;
use Netinteractive\Elegant\Exception\DeletionException;
use Netinteractive\Elegant\Searchable AS Searchable;
use Netinteractive\Utils\Utils AS Utils;
use Netinteractive\Elegant\Filters\Field\Display AS DisplayLogic;


abstract class Elegant extends Model
{
    /**
     * @var array
     */
    protected static $fields;

    /**
     * @var Netinteractive\Elegant\Exception\ValidationException
     */
    protected $error;
    /**
     * @var Illuminate\Support\Facades\Validator
     */
    protected $validator;

    /**
     * is validation on or off
     * @var bool
     */
    protected $validationEnabled = true;

    /**
     * enable or disable query acl filters
     * @var bool
     */
    protected static $queryAllowAcl = true;

    /**
     * lista mozliwych typow dla pol
     * @var array
     */
    public static $fieldTypes = array('string', 'text', 'int', 'bool', 'email', 'date', 'dateTime', 'html', 'file', 'image');

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        Searchable::$alias = $this->getTable();
        $this->init();
        parent::__construct($attributes);
    }


    /**
     *
     */
    protected function init()
    {

    }

    /**
     * metoda sluzy do zainicjowania pol modelu
     * @param array $fields
     * @return $this
     */
    public function initFields(array $fields)
    {
        self::$fields[get_class($this)] = $fields;
        return $this;
    }


    /**
     * Find a model by its primary key.
     * Przeciazylismy i dodajemy nazwe tabeli z modelu przed nazwa pola
     *
     * @param  mixed $id
     * @param  array $columns
     * @return \Illuminate\Support\Collection|static
     */
    public static function find($id, $columns = array('*'))
    {
        foreach ($columns AS &$column) {
            if (strpos('.', $column) == false) {
                $column = \App::make(get_called_class())->getTable() . '.' . $column;
            }
        }

        return parent::find($id, $columns);
    }

    /**
     * Get all of the models from the database.
     * Przeciazylismy i dodajemy nazwe tabeli z modelu przed nazwa pola
     *
     * @param  array $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function all($columns = array('*'))
    {
        foreach ($columns AS &$column) {
            if (strpos('.', $column) == false) {
                $column = \App::make(get_called_class())->getTable() . '.' . $column;
            }
        }

        return parent::all($columns);
    }

    /**
     * Zwraca obiekt walidatora
     * @return Illuminate\Support\Facades\Validator
     */
    public function validator()
    {
        if (is_null($this->validator)) {
            $this->validator = \Validator::make($this->attributes, $this->getFieldsRules());
        }
        return $this->validator;
    }


    /**
     * Zwraca tablice z lista pol modelu wraz z informacjami o walidacji etc. etc.
     * @return array
     */
    public function getFields()
    {
        if ( isSet(self::$fields[get_class($this)]) ){
            return self::$fields[get_class($this)];
        }

        return array();
    }


    /**
     * Metoda pozwala kierowac odpaleniem eventu acl w query builderze
     * @param bool $allow
     */
    public static function allowQueryAcl($allow = true)
    {
        self::$queryAllowAcl = $allow;
    }

    /**
     * Perform a model insert operation.
     * Przeciazylismy, aby dodac eventy after_create, z ktorych korzystaja np. userParams
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return bool|null
     */
    public function performInsert(\Illuminate\Database\Eloquent\Builder $query, array $options = array())
    {
        $this->validate('insert');

        $attributes = $this->attributes;
        $this->attributes = $this->getDirty();

        $this->fireModelEvent('elegant.before.insert', false);
        $this->fireModelEvent('elegant.before.saving', false);
        $result = parent::performInsert($query, $options);

        $this->attributes = array_merge($attributes, $this->attributes);

        $this->fireModelEvent('elegant.after.insert', false);

        return $result;
    }

    /**
     * Perform a model update operation.
     * Przeciazylismy, aby dodac eventy after_updated, z ktorych korzystaja np. userParams
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return bool|null
     */
    protected function performUpdate(\Illuminate\Database\Eloquent\Builder $query, array $options = array())
    {
        $this->validate('update');

        $this->fireModelEvent('elegant.before.update', false);
        $this->fireModelEvent('elegant.before.saving', false);
        $result = parent::performUpdate($query, $options);
        $this->fireModelEvent('elegant.after.update', false);

        return $result;
    }


    /**
     * Validate model fields
     * @param string $rulesGroups
     * @throws Netinteractive\Elegant\Exception\ValidationException
     * @return Elegant
     */
    public function validate($rulesGroups = 'all')
    {
        if ($this->validationEnabled == false) {
            return $this;
        }
        $messageBag = new MessageBag();
        $validator = $this->validator();
        $validator->setData($this->attributes);

        $rules = $this->getFieldsRules($rulesGroups);
        foreach ($rules AS $field => $val) {
            if ($this->exists && !$this->isDirty($field)) {
                unset($rules[$field]);
            }
        }

        $validator->setRules($rules);

        if ($validator->fails()) {
            $messages = $validator->messages()->toArray();
            foreach ($messages as $key => $message) {
                $messageBag->add($key, $message);
            }
            $this->error = new ValidationException($messageBag);
            throw $this->error;
        }

        return $this;
    }

    /**
     * metoda, ktora przeprowadza walidacje wskazanego pola/pol
     * @param array $field
     * @return $this
     */
    public function validateFields(array $fields, $rulesGroups = 'all')
    {
        if ($this->validationEnabled == false) {
            return $this;
        }

        $messageBag = new MessageBag();
        $validator = $this->validator();
        $validator->setData($this->attributes);

        $rules = $this->getFieldsRules($rulesGroups, $fields);
        foreach ($rules AS $field => $val) {
            if ($this->exists && !$this->isDirty($field)) {
                unset($rules[$field]);
            }
        }

        $validator->setRules($rules);

        if ($validator->fails()) {
            $messages = $validator->messages()->toArray();
            foreach ($messages as $key => $message) {
                $messageBag->add($key, $message);
            }
            $this->error = new ValidationException($messageBag);
            throw $this->error;
        }

        return $this;
    }

    /**
     * add error into validation exception
     * @param $key
     * @param $message
     * @throws Netinteractive\Elegant\Exception\ValidationException
     * @return Elegant
     */
    public function addValidationError($key, $message)
    {
        if (is_null($this->error)) {
            $MessageBag = new MessageBag();
            $this->error = new ElegantValidationException($MessageBag);
        } else {
            $MessageBag = $this->error->getMessageBag();
        }
        $MessageBag->add($key, $message);
        return $this;
    }

    /**
     * @param string $rulesGroups
     * @return bool
     */
    public function isValid($rulesGroups = 'all')
    {
        try {
            $this->validate($rulesGroups);
            return true;
        } catch (ElegantValidationException $e) {
            return false;
        }
    }

    /**
     * Walidacja przypisanych rekordów
     * @throws Netinteractive\Elegant\Exception\AttachException
     * @param $key
     */
    public function checkAttachedIds($key, $message = 'Brak powiązanych rekordów!')
    {
        if (!$this->exists && !$this->getAttribute($key)) {
            throw new AttachException($message);
        }

        if (isset($this->attributes[$key])) {
            $arr = explode(',', $this->attributes[$key]);
            if (count($arr) < 1 || empty($arr[0])) {
                throw new AttachException($message);
            }
        }
    }


    /**
     * Builds query alliases for fields
     * @param null $fields
     * @return array
     */
    public function makeFieldsAliases($fields = null)
    {
        if (!$fields) {
            $fields = array_keys($this->getFields());
        }
        $class = get_class($this);
        $result = [];
        foreach ($fields as $field) {
            $result[] = $class . '.' . $field . ' AS ' . $class . '_' . $field;
        }
        return $result;
    }


    /**
     * @param \Illuminate\Database\Eloquent\Builder $q
     * @param string $keyword
     * @param array $inFields
     * @return Elegant
     */
    public function makeLikeWhere(\Illuminate\Database\Eloquent\Builder &$q, $keyword, $inFields)
    {
        $keyword = trim($keyword);
        if (!is_array($inFields)) {
            $inFields = array($inFields);
        }
        $q->where(function (\Illuminate\Database\Eloquent\Builder $q) use ($keyword, $inFields) {
            foreach ($inFields as $field) {
                if ($this->isOriginal($field)) {
                    $searchable = $this->getFieldSearchable($field);

                    if ( $searchable ) {
                         if ($searchable instanceof \Closure) {
                             $searchable($q, $keyword);
                        }
                    }
                }
                /**
                 * przypadek dla pol z innego modelu
                 */
                elseif (is_array($field) && count($field) == 2) {
                    # $field[1] nazwa modelu
                    $relModel = \App($field[1]);

                    $searchable = $relModel->getFieldSearchable($field[0]);
                    if ( $searchable ) {
                        if ($searchable instanceof \Closure) {
                            $searchable($q, $keyword);
                        }
                    }
                }
            }
        });

        return $this;
    }

    /**
     * zwraca funkcje anonimowa, ktora decuduje o tym jak wyszukiwac po polu
     * @param string $field
     * @return mixed
     */
    public function getFieldSearchable($field)
    {
        if (!isSet(self::$fields[get_class($this)][$field]['searchable'])){
            return false;
        }

        return self::$fields[get_class($this)][$field]['searchable'];
    }

    /**
     * @param string $field
     * @param string $type
     * @param string $operator
     * @return Elegant
     */
    public function setFieldSearchable($field, $type, $operator = '=')
    {
        self::$fields[get_class($this)][$field]['searchable'] = Searchable::$type($field, $operator);
        return $this;
    }

    /**
     * @param array $params
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function searchInGrid(array $params = array())
    {
        $q = $this->newQuery();
        if (array_get($params, 'fields')) {
            $q->select($this->makeFieldsAliases($params['fields']));
            $q->from($this->getTable() . ' AS ' . get_class($this));
        }
        return $q;
    }

    /**
     * @param $query
     * @return mixed
     */
    protected function searchJoins($query)
    {
        return $query;
    }

    /**
     * Wyszukiwarka
     *
     * @param array $input
     * @param array collumns
     * @param string $operator
     * @param boolean $defaultJoin
     */
    public function search($input, $columns = array(), $operator = 'and', $defaultJoin = true)
    {
        $query = $this->getQuery();
        if (empty($columns)) {
            $columns[] = $this->table . '.*';
        }
        $query->select($columns);

        foreach ($input as $groupName => $groupFields) {
            if (is_array($groupFields)) {
                foreach ($groupFields AS $name => $val) {
                    if (is_array($val) && in_array('null', $val)) {
                        unset($input[$groupName][$name]);
                    }
                    if (empty($input[$groupName][$name])) {
                        unset($input[$groupName][$name]);
                    }
                }
            }
        }

        if ($defaultJoin) {
            $query = $this->searchJoins($query);
        }

        #opakowane w where, aby inne where dodane wczesniej lub pozniej dzialaly prawidlowo
        $query->where(function ($query) use ($input, $operator) {
            foreach ($input AS $modelName => $fields) {
                if (!empty($fields) && is_array($fields)) {
                    $model = \App::make($modelName);
                    foreach ($fields AS $field => $value) {
                        $query = $model->queryFieldSearch($field, $value, $query, $operator);
                    }
                }
            }
        });

        return $query;
    }

    /**
     * return validation|save|delete last exception
     * @return Netinteravtive/Elegant/Exception/DeletionException
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return bool|null
     * @throws Netinteravtive/Elegant/Exception/DeletionException
     * @return mixed
     */
    public function delete()
    {
        if (!parent::delete()) {
            $this->error = new DeletionException(_("Can't delete record!"));
            throw $this->error;
        }
        return true;
    }

    /**
     * Zwraca pola po ktorych mozna sortowac
     * @return array
     */
    public function getSortableFields()
    {
        $fields = array();

        foreach ($this->getFields() AS $key => $field) {
            if (array_get($field, 'sortable')) {
                $fields[$key] = $field;
            }
        }

        return $fields;
    }

    /**
     * Zwraca pola, po ktorych mozna wyszukiwac
     * @return array
     */
    public function getSearchableFields()
    {
        $fields = array();

        foreach ($this->getFields() AS $key => $field) {
            if (array_get($field, 'searchable')) {
                $fields[$key] = $field;
            }
        }

        return $fields;
    }

    /**
     * return titles for fields
     * @param array $fieldsKeys
     * @return array
     */
    public function getFieldsTitles($fieldsKeys = null)
    {
        if (is_null($fieldsKeys)) {
            $fieldsKeys = array_keys($this->getFields());
        }
        if (!is_array($fieldsKeys)) {
            $fieldsKeys = array($fieldsKeys);
        }
        $result = array();
        $fields = $this->getFields();
        foreach ($fields as $key => $field) {
            if (in_array($key, $fieldsKeys)) {
                $result[$key] = $field['title'];
            }

        }
        return $result;
    }

    /**
     * Metoda w oparciu o dane zdefiniowane w modelu, dodaje odpowiedni where do zapytania
     * @param string $field
     * @param string $keyword
     * @param Query $q
     * @param string $operator
     * @return mixed
     */
    public function queryFieldSearch($field, $keyword, $q, $operator = 'or')
    {
        if (isSet( self::$fields[get_class($this)][$field]['searchable'])) {
            $searchable = self::$fields[get_class($this)][$field]['searchable'];
            $searchable($q, $keyword, $operator);
        }

        return $q;
    }

    /**
     * zwraca label dla pola
     * @param string $field
     * @return mixed
     */
    public function getFieldTitle($field)
    {
        if (!isSet( self::$fields[get_class($this)][$field]['title'])) {
            return null;
        }

        return  self::$fields[get_class($this)][$field]['title'];
    }

    /**
     * return field validation rules
     * @param string $key
     * @return array
     */
    public function getFieldRules($key)
    {
        if (isSet( self::$fields[get_class($this)][$key]['rules'])) {
            return  self::$fields[get_class($this)][$key]['rules'];
        }
        return array();
    }

    /**
     * return field rules for selected group. default group is "any". always return at least any
     * @param string $rulesGroups
     * @param array $fieldsKeys
     * @return array
     */
    public function getFieldsRules($rulesGroups = 'any', $fieldsKeys = null)
    {
        $rulesGroups = Utils::paramToArray($rulesGroups);

        if (is_null($fieldsKeys)) {
            $fieldsKeys = array_keys($this->getFields());
        }

        $fieldsKeys = Utils::paramToArray($fieldsKeys);

        if (!in_array('any', $rulesGroups)) {
            array_push($rulesGroups, 'any');
        }

        $result = array();
        $fields = $this->getFields();
        foreach ($fields as $key => $field) {
            if (!in_array($key, $fieldsKeys) || !isSet($field['rules'])) {
                continue;
            }

            $rules = $field['rules'];
            $result[$key] = '';
            foreach ($rulesGroups as $ruleGroup) {
                if (in_array($ruleGroup, $rulesGroups)) {
                    $result[$key] .= '|' . array_get($rules, $ruleGroup);
                }
            }
        }
        return $result;
    }

    /**
     * return field types for selected fields
     * @param array $fieldsKeys
     * @return array
     */
    public function getFieldsTypes(array $fieldsKeys = array())
    {
        if (is_null($fieldsKeys)) {
            $fieldsKeys = array_keys($this->getFields());
        }
        if (!is_array($fieldsKeys)) {
            $fieldsKeys = array($fieldsKeys);
        }
        $result = array();
        $fields = $this->getFields();
        foreach ($fields as $key => $field) {
            if (in_array($key, $fieldsKeys)) {
                $result[$key] = $field['type'];
            }
        }
        return $result;
    }

    /**
     * zwraca informacje o typie pola
     * @param string $field
     * @return mixed
     */
    public function getFieldType($field)
    {
        if (!isSet( self::$fields[get_class($this)][$field]['type'])) {
            return null;
        }

        return  self::$fields[get_class($this)][$field]['type'];
    }

    /**
     * zwraca filtry dla pola
     * @param string $field
     * @return null
     */
    public function getFieldFilters($field)
    {
        if (!isSet( self::$fields[get_class($this)][$field]['filters'])) {
            return null;
        }

        return  self::$fields[get_class($this)][$field]['filters'];
    }

    /**
     * set up validation rules for selected field
     * @param string $field
     * @param string|array $rules
     * @param null|string $group
     * @return Elegant
     */
    public function setFieldRules($field, $rules, $group = null)
    {
        if ($group === null) {
            self::$fields[get_class($this)][$field]['rules'] = $rules;
        } else {
            self::$fields[get_class($this)][$field]['rules'][$group] = $rules;
        }
        return $this;
    }

    /**
     * enalbe/disable validation
     * @param bool $enable
     * @return Elegant
     */
    public function setValidationEnabled($enable = true)
    {
        $this->validationEnabled = $enable;
        return $this;
    }

    /**
     * return information if field is model field
     * @param string $field
     * @return bool
     */
    public function isOriginal($field)
    {
        $fields = array_keys($this->getFields());

        return in_array($field, $fields);
    }


    /**
     * Aliast dla isOriginal
     * @param string $field
     * @return bool
     */
    public function isInFields($field)
    {
        return $this->isOriginal($field);
    }

    /**
     * Get the attributes that have been changed since last sync.
     * Usuwamy z dirty pola, ktore nie pochodza z tego active recordu
     *
     * @return array
     */
    public function getDirty()
    {
        $dirty = parent::getDirty();

        $obj = new \stdClass();
        $obj->data = $dirty;
        $obj->Record = $this;

        \Event::fire('elegant.before.save', $obj);
        $dirty = $obj->data;

        foreach ($dirty as $field => $value) {
            /**
             * usuwamy pola, ktore nie pochodza z modelu
             */
            if (!$this->isOriginal($field)) {
                unset($dirty[$field]);
            } /**
             * usuwamy haslo jesli jest puste
             */
            elseif ($this->getFieldType($field) == 'password' && empty($dirty[$field])) {
                unset($dirty[$field]);
            }
        }

        return $dirty;
    }

    /**
     * zwraca obiekt walidatora
     * @return mixed
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * metoda zwraca informacje czy pole znauduje sie w modelu
     * @param $field
     * @return bool
     */
    public function isField($field)
    {
        if (isSet( self::$fields[get_class($this)][$field]) ){
            return true;
        }
        return false;
    }

    /**
     * Przeciazony fill - odpalamy nasz filtr acl sprawdzajacy prawo zapisu pol
     * @param array $attributes
     * @return mixed
     */
    public function fill(array $attributes)
    {
        if (count($attributes)) {
            $obj = new \stdClass();
            $obj->data = $attributes;
            $obj->Record = $this;
            \Event::fire('acl.filter.model.fill', $obj);
            $attributes = $obj->data;
        }

        return parent::fill($attributes);
    }


    /**
     * @param string $key
     * @param mixed $value
     */
    public function setAttribute($key, $value)
    {
        $this->fireModelEvent('elegant.before.setAttribute', false);
        parent::setAttribute($key, $value);
        $this->fireModelEvent('elegant.after.setAttribute', false);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();
        $grammar = $conn->getQueryGrammar();

        return \App::make('QueryBuilder', array($conn, $grammar, $conn->getPostProcessor()))->allowAclFilter(self::$queryAllowAcl);
    }


    /**
     * funckja, ktora zwraca wartosc pola modelu po jej przefiltrowaniu
     * @param string $field
     * @param array $filters
     * @param boolean $defaultFilters
     * @return mixed
     */
    public function display($field, $filters = array(), $defaultFilters = true)
    {
        $obj = new \stdClass();
        $obj->value = $this->$field;
        $obj->field = $field;
        $obj->Record = $this;

        if ($defaultFilters == true) {
            \Event::fire('elegant.before.display', $obj);
        }

        if (!empty($filters)) {
            DisplayLogic::apply($obj, $filters);
        }


        return $obj->value;
    }


    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return \App::make('ModelBuilder', array($query));
    }


    /**
     * @param array $models
     * @return \Illuminate\Database\Eloquent\Collection|mixed|Collection
     */
    public function newCollection(array $models = array())
    {
        try {
            $collection = \App::make('Collection', $models);
            return $collection;
        } catch (\ReflectionException $e) {
            return new Collection($models);
        }
    }

    /**
     * Convert the model instance to an array.
     * @param boolean $displayFilter - default true
     * @return array
     */
    public function toArray($displayFilter = false)
    {
        $attributes = $this->attributesToArray();

        $data = array_merge($attributes, $this->relationsToArray());

        if ($displayFilter == true) {
            foreach ($data as $key => $val) {
                $data[$key] = $this->display($key);
            }
        }


        return $data;
    }
}
