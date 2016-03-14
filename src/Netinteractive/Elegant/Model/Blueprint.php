<?php namespace Netinteractive\Elegant\Model;

use Netinteractive\Elegant\Exception\ClassTypeException;
use Netinteractive\Elegant\Model\Relation\Manager;

/**
 * Class Blueprint
 * @package Netinteractive\Elegant\Models
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
    protected $storage = null;

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
     * Indicates if the record should be timestamped.
     *
     * @var bool
     */
    protected $timestamps = false;


    /**
     * Indicates if the record should be soft deleted.
     *
     * @var bool
     */
    protected $softDelete = false;


    #TIMESTAMP FIELDS
    public static $createdAt = 'created_at';
    public static $updatedAt = 'updated_at';
    public static $deletedAt = 'deleted_at';


    #FIELD TYPES

    /**
     * field type for ints
     */
    const TYPE_INT = 'int';

    /**
     * field type for decimal
     */
    const TYPE_DECIMAL = 'decimal';

    /**
     * field type for date
     */
    const TYPE_DATE = 'date';

    /**
     * field type for datetime
     */
    const TYPE_DATETIME = 'dateTime';

    /**
     * field type for time
     */
    const TYPE_TIME = 'time';

    /**
     * field type for string
     */
    const TYPE_STRING = 'string';

    /**
     * field type for password
     */
    const TYPE_PASSWORD = 'password';

    /**
     * field type for html
     */
    const TYPE_HTML = 'html';

    /**
     * field type for ip
     */
    const TYPE_IP = 'ip';


    /**
     * field type for email
     */
    const TYPE_EMAIL = 'email';

    /**
     * field type for URL
     */
    const TYPE_URL = 'url';

    /**
     * field type for file
     */
    const TYPE_FILE = 'file';

    /**
     * field type for image
     */
    const TYPE_IMAGE= 'image';


    /**
     * Constructor
     */
    protected function __construct()
    {
        $relationManager = \App('ni.elegant.model.relation.manager');
        $this->relationManager = $relationManager;

        $this->init();

        #adding timestamps to field list
        if($this->hasTimestamps()){
            if (!$this->isField($this->getCreatedAt())){
                $this->fields[$this->getCreatedAt()] = array(
                    'title' => _('Created At'),
                    'type' => 'dateTime'
                );
            }

            if (!$this->isField($this->getUpdatedAt())){
                $this->fields[$this->getUpdatedAt()] = array(
                    'title' => _('Updated At'),
                    'type' => 'dateTime'
                );
            }
        }

        if ($this->softDelete()){
            if (!$this->isField($this->getDeletedAt())){
                $this->fields[$this->getDeletedAt()] = array(
                    'title' => _('Deleted At'),
                    'type' => 'dateTime'
                );
            }
        }


        static::bootTraits();
    }

    /**
     * Returns scope object
     * @return null
     */
    public function getScopeObject(){
        return null;
    }


    /**
     * Boot all of the bootable traits
     *
     * @return void
     */
    protected static function bootTraits()
    {
        foreach (class_uses_recursive(get_called_class()) as $trait){
            if (method_exists(get_called_class(), $method = 'boot'.class_basename($trait))){
                forward_static_call([get_called_class(), $method]);
            }
        }
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
    public function getFieldsTitles($fieldsKeys = array())
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
     * @return null|string
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
    public function getFieldsRules($rulesGroups='all', array $fieldsKeys=array())
    {
        if (!is_array($rulesGroups)){
            $rulesGroups = array_map('trim', explode(',', $rulesGroups));
        }

        if (empty($fieldsKeys)) {
            $fieldsKeys = array_keys($this->getFields());
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
                    $fieldRules = array_get($rules, $ruleGroup);
                    if ($fieldRules){
                        $result[$key] .= $fieldRules.'|';
                    }
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
        $result = array();
        if (isSet($fields[$fieldKey]['rules'])) {
            $result =  $fields[$fieldKey]['rules'];
        }

        return $result;
    }

    /**
     * Returns list of fields types
     *
     * @param array $fieldsKeys
     * @return array
     */
    public function getFieldsTypes(array $fieldsKeys = array())
    {
        if (empty($fieldsKeys)) {
            $fieldsKeys = array_keys($this->getFields());
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
     * Returns field filters
     * @param string $field
     * @param string $type
     * @return null|array
     */
    public function getFieldFilters($field, $type=null)
    {
        $fields = $this->getFields();

        if ($type == null){
            $filters =  isSet($fields[$field]['filters']) ? $fields[$field]['filters'] : null;
        }else{
            $filters = isSet($fields[$field]['filters'][$type]) ? $fields[$field]['filters'][$type] : null;
        }

        return $filters;
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
    public function setFieldRules($fieldKey, array $rules, $group=null)
    {
        if ($group === null) {
            $this->fields[$fieldKey]['rules'] = $rules;
        }
        else {
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
        return in_array((string)$fieldKey, array_keys($this->fields));
    }

    /**
     * Check if field is required
     * @param $key
     * @param string $action
     * @return bool
     */
    public function isFieldRequired($key, $action=null)
    {
        foreach ($this->fields[$key]['rules'] AS $group=>$rules){
            if ($action == null || $action == $group){
                $rules_array = explode('|', $rules);

                foreach($rules_array as $rule){
                    if($rule == 'required'){
                        return true;
                    }
                }
            }
        }

        return false;
    }


    /**
     * Function checks if field is external or not.
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
     * Returns information if given field is incrementing part of primary key
     * @param string $fieldKey
     * @return bool
     */
    public function isIncrementingPk($fieldKey)
    {
        return $fieldKey == $this->incrementingPk;
    }

    /**
     * Checks if field is timestamp type
     * @param string $field
     * @return bool
     */
    public function isTimestamp($field)
    {
        if ($this->getCreatedAt() == $field || $this->getUpdatedAt() == $field || $this->getDeletedAt() == $field){
            return true;
        }
        return false;
    }


    /**
     * Returns information if record has timestamps (created_at and updated_at)
     * @return bool
     */
    public function hasTimestamps()
    {
        return $this->timestamps;
    }

    /**
     * Returns information if record should be soft deleted
     * @return bool
     */
    public function softDelete()
    {
        return $this->softDelete;
    }

    /**
     * Returns data storage name
     * @return null|string
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
     * @return \Netinteractive\Elegant\Model\Relation\Manager|null
     */
    public function getRelationManager()
    {
        return $this->relationManager;
    }


    /**
     * Sets relationship manager
     * @param \Netinteractive\Elegant\Model\Relation\Manager|null $manager
     * @return $this
     */
    public function setRelationManager($manager=null)
    {
        if (!is_null($manager) && !$manager instanceof Manager){
            $msg = _(' Invliad class type of object.').' ';
            $msg .= _('Expected: \Netinteractive\Elegant\Model\Relation\Manager').' ';
            $msg .= _('Recived:').' '.get_class($manager);

            throw new ClassTypeException($msg);
        }

        $this->relationManager = $manager;
        return $this;
    }


    /**
     * Checks if relation is defined
     * @param string $name
     * @return bool
     */
    public function hasRelation($name){
        return $this->getRelationManager()->hasRelation($name);
    }

    ## AT COLUMNS


    /**
     * Returns the name of the "created at" field.
     * @return string
     */
    public function getCreatedAt()
    {
        return self::$createdAt;
    }


    /**
     * Returns the name of the "updated at" field.
     * @return string
     */
    public function getUpdatedAt()
    {
        return self::$updatedAt;
    }


    /**
     * Returns the name of the "deleted at" field.
     * @return string
     */
    public function getDeletedAt()
    {
        return self::$deletedAt;
    }
} 