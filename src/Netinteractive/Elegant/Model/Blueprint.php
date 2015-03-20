<?php
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 06.03.15
 * Time: 10:57
 */

namespace Netinteractive\Elegant\Model;

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
     * @var array
     */
    protected $relations = array();

    /**
     * @var string
     */
    protected $table;

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

    }

    /**
     * Returns list of fields which can be searched
     *
     * @return array
     */
    public function getSearchableFields()
    {

    }

    /**
     * Returns list of fields titles
     *
     * @return
     */
    public function getFieldsTitles()
    {

    }

    /**
     * Returns field title
     *
     * @param string $fieldKey
     * @return string
     */
    public function getFieldTitle($fieldKey)
    {

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

    }

    /**
     * Returns list of fields types
     *
     * @param array $fieldsKeys
     * @return array
     */
    public function getFieldsTypes($fieldsKeys = array())
    {

    }

    /**
     * Returns specific field type
     *
     * @param string $fieldKey
     * @return string|null
     */
    public function getFieldType($fieldKey)
    {

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
     * Returns table name
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    public function setTable($name)
    {
        $this->table = $name;
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
     * Return list of relations
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * Get a specified relationship.
     *
     * @param  string  $relation
     * @return array
     */
    public function getRelation($relation)
    {
        return $this->relations[$relation];
    }



} 