<?php namespace Netinteractive\Elegant\Model;
use Netinteractive\Elegant\Model\Relation\Manager;

/**
 * Class Blueprint
 * @package Netinteractive\Elegant\Model
 */
abstract class Blueprint
{

    /**
     * Blueprint instance
     * @var array
     */
    protected static $instances = array();

    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @var null|\Netinteractive\Elegant\Model\Relation\Manager
     */
    protected $relationManager = null;

    /**
     * @var string
     */
    protected $storage;

    /**
     * @var array
     */
    protected $primaryKey = array();

    /**
     * Indicates if the there is a primaryKey or part of it that is are auto-incrementing.
     *
     * @var bool
     */
    public  $incrementingPk = null;


    /**
     * Constructor
     */
    protected function __construct()
    {
        $relationManager = \App('ElegantRelationManager');
        $this->relationManager = $relationManager;

        $this->init();
    }


    /**
     * Creates Blueprint instance
     * @return Blueprint|null
     */
    static public function getInstance()
    {
        $class=get_called_class();
        if (empty(self::$instances[$class])) {
            $num = func_num_args();
            $args = func_get_args();

            $code = "self::\$instances[\$class]=new " . get_called_class() . "(";

            for ($i = 1; $i < $num; $i++) {
                $code .= "\$args[" . $i . "]";
                if ($i < $num - 1) {
                    $code .= ",";
                }
            }
            $code .= ");";
            eval ($code);
        }

        return self::$instances[$class];
    }


    /**
     * Initialize blueprint
     * @return $this
     */
    protected function init()
    {
        return $this;
    }


    /**
     * Returns list of fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Returns list of fields that are sortable
     *
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
     * Returns list of fields which can be searched
     *
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
     * Returns list of fields titles
     * @param array $fieldKeys
     * @return
     */
    public function getFieldsTitles(array $fieldsKeys = array())
    {
        if (empty($fieldsKeys)) {
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
     * Returns field title
     *
     * @param string $fieldKey
     * @return string
     */
    public function getFieldTitle($fieldKey)
    {
        $fields = $this->getFields();
        if (!isSet( $fields[$fieldKey]['title'])) {
            return null;
        }

        return  $fields[$fieldKey]['title'];
    }

    /**
     * Return list of fields validation rules
     *
     * @param string|array $rulesGroups
     * @param array $fieldsKeys
     * @return array
     */
    public function getFieldsRules($rulesGroups='all', $fieldsKeys=array())
    {

        if (!is_array($rulesGroups)){
            $rulesGroups = array_map('trim', explode(',', $rulesGroups));
        }

        if (is_null($fieldsKeys)) {
            $fieldsKeys = array_keys($this->getFields());
        }

        if (!is_array($fieldsKeys)){
            $fieldsKeys = array_map('trim', explode(',', $fieldsKeys));
        }

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
     * Return list of specific field validation rules
     *
     * @param string $fieldKey
     * @return array
     */
    public function getFieldRules($fieldKey)
    {
        $fields = $this->getFields();
        if (isSet($fields[$fieldKey]['rules'])) {
            return  $fields[$fieldKey]['rules'];
        }
        return array();
    }

    /**
     * Returns list of fields types
     *
     * @param array $fieldsKeys
     * @return array
     */
    public function getFieldsTypes($fieldsKeys = array())
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
     * Returns specific field type
     *
     * @param string $fieldKey
     * @return string|null
     */
    public function getFieldType($fieldKey)
    {
        $fields = $this->getFields();
        if (!isSet( $fields[$fieldKey]['type'] )) {
            return null;
        }

        return $fields[$fieldKey]['type'];
    }

    /**
     * Set validation rules for a field
     *
     * @param string $fieldKey
     * @param array $rules
     * @param string $group
     *
     * @return $this
     */
    public function setFieldRules($fieldKey, array $rules, $group='all')
    {
        if ($group === null) {
            $this->fields[$fieldKey]['rules'] = $rules;
        } else {
            $this->fields[$fieldKey]['rules'][$group] = $rules;
        }
        return $this;
    }

    /**
     * Checks if field exists in the fields list
     *
     * @param string $fieldKey
     * @return boolean
     */
    public function isField($fieldKey)
    {
        return in_array($fieldKey, array_keys($this->fields));
    }


    /**
     * function checks if field is external or not.
     * (external fields are not saved to the data source)
     * @param string $fieldKey
     * @return bool
     */
    public function isExternal($fieldKey)
    {
        if (!$this->isField($fieldKey)){
            return false;
        }

        if ( !isSet($this->fields[$fieldKey]['external']) ||  $this->fields[$fieldKey]['external'] == false){
            return false;
        }

        return true;
    }

    /**
     * Returns data storage name
     * @return string
     */
    public function getStorageName()
    {
        return $this->storage;
    }

    /**
     *  Sets data storage name
     * @param string $name
     * @return $this
     */
    public function setStorageName($name)
    {
        $this->storage = $name;
        return $this;
    }

    /**
     * Returns table primary key. Can contains multiple fields
     * @return array
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Sets primary key
     * @param string|array $key
     * @return $this
     */
    public function setPrimaryKey($key)
    {
        if (!is_array($key)){
            $key = array($key);
        }

        $this->primaryKey = $key;
        return $this;
    }

    /**
     * Return relation manager object
     * @return array
     */
    public function getRelationManager()
    {
        return $this->relationManager;
    }

    /**
     * Sets relationship manager
     * @param Manager $manager
     * @return $this
     */
    public function setRelationManager(Manager $manager)
    {
        $this->relationManager = $manager;
        return $this;
    }


} 