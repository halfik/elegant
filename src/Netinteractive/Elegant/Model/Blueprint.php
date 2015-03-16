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
     * @var bool
     */
    protected $validationEnabled = true;


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
     * @param string $field
     * @return string
     */
    public function getFieldTitle($field)
    {

    }

    /**
     * Return list of fields validation rules
     *
     * @param string|array $rulesGroups
     * @param array $fields
     * @return array
     */
    public function getFieldsRules($rulesGroups='all', $fields=array())
    {

    }

    /**
     * Return list of specific field validation rules
     *
     * @param string $field
     * @return array
     */
    public function getFieldRules($field)
    {

    }

    /**
     * Returns list of fields types
     *
     * @param array $fields
     * @return array
     */
    public function getFieldsTypes($fields = array())
    {

    }

    /**
     * Returns specific field type
     *
     * @param string $field
     * @return string|null
     */
    public function getFieldType($field)
    {

    }

    /**
     * Set validation rules for a field
     *
     * @param string $field
     * @param array $rules
     * @param string $group
     *
     * @return $this
     */
    public function setFieldRules($field, array $rules, $group='all')
    {

        return $this;
    }

    /**
     * Checks if field exists in the fields list
     *
     * @param string $field
     * @return boolean
     */
    public function isField($field)
    {
        return false;
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


    /**
     * Enables fields validations rules
     * @return $this
     */
    public function enableValidation()
    {
        $this->validationEnabled = true;
        return $this;
    }

    /**
     * Disabled fields validations rules
     * @return $this
     */
    public function disableValidation()
    {
        $this->validationEnabled = false;
        return $this;
    }
} 