<?php

namespace Netinteractive\Elegant\Model;

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

    #STANDARD PROTECTION LEVELS
    const PROTECTION_LOW_MIN = 51;
    const PROTECTION_LOW_MAX= 100;

    const PROTECTION_NORMAL_MIN = 11;
    const PROTECTION_NORMAL_MAX= 50;

    const PROTECTION_HIGH_MIN = 1;
    const PROTECTION_HIGH_MAX= 10;

    protected static  $PROTECTION_LOW = [self::PROTECTION_LOW_MIN, self::PROTECTION_LOW_MAX];
    protected static  $PROTECTION_NORMAL = [self::PROTECTION_NORMAL_MIN, self::PROTECTION_NORMAL_MAX];
    protected static  $PROTECTION_HIGH = [self::PROTECTION_HIGH_MIN, self::PROTECTION_HIGH_MAX];


    const PROTECTION_LOW = 1;
    const PROTECTION_NORMAL = 2;
    const PROTECTION_HIGH = 4;


    const PROTECT_CREATE = 1;
    const PROTECT_VIEW = 2;
    const PROTECT_UPDATE = 4;
    const PROTECT_DELETE = 8;


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
     * field type for enym data
     */
    const TYPE_ENUM = 'enum';
    
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
     * field type for booleans
     */
    const TYPE_BOOL = 'bool';


    /**
     * The hasher for the password and other fields
     *
     * @var \Netinteractive\Elegant\Hashing\HasherInterface
     */
    protected $hasher;

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
    public function getScopeObject()
    {
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
     * Returns field data
     * @param string $key
     * @return array
     */
    public function getField($key)
    {
        if (array_key_exists($key, $this->fields)){
            return $this->fields[$key];
        }

        return null;
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
     * @param array $fieldsKeys
     * @return array
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
    public function getFieldsRules($rulesGroups=array(), array $fieldsKeys=array())
    {
        if (!is_array($rulesGroups)){
            $rulesGroups = array_map('trim', explode(',', $rulesGroups));
        }

        #if no group is specified, we get all validators
        if (empty($rulesGroups)){
            $rulesGroups = ['any', 'insert', 'update'];
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
     * Returns field enum array
     * @param string $fieldKey
     * @return array|null
     */
    public function getEnum($fieldKey)
    {
        $field = $this->getField($fieldKey);

        if ($this->isEnum($fieldKey) && array_key_exists('enum', $field)){
            return $field['enum'];
        }
        
        return null;
    }

    /**
     * Returns field protection level
     * @param string $key
     * @return array
     */
    public function getProtections($fieldKey)
    {
        if ($this->isProtected($fieldKey)){
            return $this->fields[$fieldKey]['protected'];
        }

        return array();
    }


    /**
     * Returns protection lvl (bit sum of protections)
     * @param string $fieldKey
     * @return int
     */
    public function getProtectionLvl($fieldKey)
    {
        $protectionLvl = 0;
        if ($this->isProtected($fieldKey)){
            $protectionLvl = array_reduce($this->getProtections($fieldKey), function($a, $b) {
                return $a | $b;
            }, 0);
        }
        
        return $protectionLvl;
    }


    /**
     * Aliast for isField
     * @param string $key
     * @return bool
     */
    public function hasField($key)
    {
        return $this->isField($key);
    }

    /**
     * Check if field is required
     * @param $key
     * @param string $action
     * @return bool
     */
    public function isRequired($key, $action=null)
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
     * Checks if field is sortable
     * @param string $fieldKey
     * @return bool
     */
    public function isSortable($fieldKey)
    {
        if ($this->hasField($fieldKey) && array_key_exists('sortable', $this->fields[$fieldKey])){
            return $this->fields[$fieldKey]['sortable'];
        }
        
        return false;
    }

    /**
     * Checks if field is searchable
     * @param string $fieldKey
     * @return bool
     */
    public function isSearchable($fieldKey)
    {
        if ($this->hasField($fieldKey) && array_key_exists('searchable', $this->fields[$fieldKey])){
            return true;
        }

        return false;
    }


    /**
     * Checks if field is primary key
     * @param string $fieldKey
     * @return bool
     */
    public function isPk($fieldKey)
    {
        if ($this->hasField($fieldKey) && in_array($fieldKey, $this->primaryKey)){
            return true;
        }

        return false;
    }
    
    /**
     * Checks if field is protected
     * @param string $fieldKey
     * @return bool
     */
    public function isProtected($fieldKey)
    {
        if ($this->hasField($fieldKey) && array_key_exists('protected', $this->fields[$fieldKey])){
            return  true;
        }

        return false;
    }

    /**
     * @param int $lvl
     * @param int $value
     * @return bool
     */
    protected function checkProtection($lvl, $value)
    {
        if ( ~ $lvl &  $value){
            return false;
        }
        return true;
    }

    /**
     * Checks if field has view protection
     * @param string $fieldKey
     * @return bool
     */
    public function hasViewProtection($fieldKey)
    {
        if (!$this->isProtected($fieldKey)) {
            return false;
        }
        
        return $this->checkProtection(
            $this->getProtectionLvl($fieldKey),
            static::PROTECT_VIEW
        );
    }


    /**
     * Checks if field has create protection
     * @param string $fieldKey
     * @return bool
     */
    public function hasCreateProtection($fieldKey)
    {
        if (!$this->isProtected($fieldKey)) {
            return false;
        }

        return $this->checkProtection(
            $this->getProtectionLvl($fieldKey),
            static::PROTECT_CREATE
        );
    }

    /**
     * Checks if field has update protection
     * @param string $fieldKey
     * @return bool
     */
    public function hasUpdateProtection($fieldKey)
    {
        if (!$this->isProtected($fieldKey)) {
            return false;
        }

        return $this->checkProtection(
            $this->getProtectionLvl($fieldKey),
            static::PROTECT_UPDATE
        );
    }

    /**
     * Checks if field has delete protection
     * @param string $fieldKey
     * @return bool
     */
    public function hasDeleteProtection($fieldKey)
    {
        if (!$this->isProtected($fieldKey)) {
            return false;
        }

        return $this->checkProtection(
            $this->getProtectionLvl($fieldKey),
            static::PROTECT_DELETE
        );
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
     * Checks if field is boolean type
     * @param string $fieldKey
     * @return bool
     */
    public function isBool($fieldKey)
    {
        return $this->isType($fieldKey, [static::TYPE_BOOL]);
    }


    /**
     * Checks if field is date type
     * @param string $fieldKey
     * @return bool
     */
    public function isDate($fieldKey)
    {
        return $this->isType($fieldKey, [static::TYPE_DATE]);
    }

    /**
     * Checks if field is dateTime type
     * @param string $fieldKey
     * @return bool
     */
    public function isDateTime($fieldKey)
    {
        return $this->isType($fieldKey, [static::TYPE_DATETIME]);
    }

    /**
     * Checks if field is email type
     * @param string $fieldKey
     * @return bool
     */
    public function isEmail($fieldKey)
    {
        return $this->isType($fieldKey, [static::TYPE_EMAIL]);
    }

    /**
     * Checks if field is enum type
     * @param string $fieldKey
     * @return bool
     */
    public function isEnum($fieldKey)
    {
        return $this->isType($fieldKey, [static::TYPE_ENUM]);
    }
    
    /**
     * Checks if field is file type
     * @param string $fieldKey
     * @return bool
     */
    public function isFile($fieldKey)
    {
        return $this->isType($fieldKey, [static::TYPE_FILE]);
    }

    /**
     * Checks if field is html type
     * @param string $fieldKey
     * @return bool
     */
    public function isHtml($fieldKey)
    {
        return $this->isType($fieldKey, [static::TYPE_HTML]);
    }


    /**
     * Checks if field is image type
     * @param string $fieldKey
     * @return bool
     */
    public function isImage($fieldKey)
    {
        return $this->isType($fieldKey, [static::TYPE_IMAGE]);
    }

    /**
     * Checks if field is i[ type
     * @param string $fieldKey
     * @return bool
     */
    public function isIP($fieldKey)
    {
        return $this->isType($fieldKey, [static::TYPE_IP]);
    }

    /**
     * Checks if field is numeric
     * @param string $fieldKey
     * @return bool
     */
    public function isNumeric($fieldKey)
    {
        return $this->isType($fieldKey, [static::TYPE_DECIMAL, static::TYPE_INT]);
    }

    /**
     * Checks if field is password type
     * @param string $fieldKey
     * @return bool
     */
    public function isPassword($fieldKey)
    {
        return $this->isType($fieldKey, [static::TYPE_PASSWORD]);
    }

    /**
     * Checks if field is time type
     * @param string $fieldKey
     * @return bool
     */
    public function isTime($fieldKey)
    {
        return $this->isType($fieldKey, [static::TYPE_TIME]);
    }


    /**
     * Checks if field is url type
     * @param string $fieldKey
     * @return bool
     */
    public function isUrl($fieldKey)
    {
        return $this->isType($fieldKey, [static::TYPE_URL]);
    }


    /**
     * Checks if field is specific type
     *
     * @param string $fieldKey
     * @param string|array $type
     * @return bool
     */
    public function isType($fieldKey, $type)
    {
        if ( !is_array($type) ){
            $type = array($type);
        }

        if ( isSet($this->fields[$fieldKey]['type']) && in_array($this->fields[$fieldKey]['type'], $type)){
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
     * Returns list of hashable attributes
     *
     * @return array
     */
    public function getHashableAttributes()
    {
        $hashableAttributes = array();

        foreach($this->fields AS $field=>$data){
            if (array_key_exists('hashable', $data) && $data['hashable'] == true){
                $hashableAttributes[] = $field;
            }
        }

        return $hashableAttributes;
    }


    public function isHashable($attribute)
    {
        $hashableAttributes = $this->getHashableAttributes();

        foreach($hashableAttributes AS $hAttr){
            if ($hAttr == $attribute){
                return true;
            }
        }

        return false;
    }

    /**
     * Sets hasher object
     * @param \Netinteractive\Elegant\Hashing\HasherInterface $hasher
     * @return $this
     */
    public function setHasher(\Netinteractive\Elegant\Hashing\HasherInterface $hasher)
    {
        $this->hasher = $hasher;
        return $this;
    }

    /**
     * Returns hasher object
     * @return \Netinteractive\Elegant\Hashing\HasherInterface
     */
    public function getHasher()
    {
        if ($this->hasher == null){
            $this->hasher = \App::make('elegant.hasher');
        }
        return $this->hasher;
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
     * @throws  \Netinteractive\Elegant\Exception\ClassTypeException
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
    public function hasRelation($name)
    {
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