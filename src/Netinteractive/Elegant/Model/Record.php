<?php namespace Netinteractive\Elegant\Model;


use Illuminate\Support\MessageBag AS MessageBag;
use Netinteractive\Elegant\Exception\ValidationException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Class Record
 * @package Netinteractive\Elegant\Model
 */
abstract class Record implements Arrayable, Jsonable
{
    /**
     * @var Blueprint
     */
    protected $blueprint;

    /**
     * Record attributes
     * @var array
     */
    protected $attributes = array();

    /**
     * Record external attributes (attributes that dosn't normaly belong to this record and won't be saved to data source)
     * @var array
     */
    protected $external = array();

    /**
     * Record original attributes (before any changes were made)
     * @var array
     */
    protected $original = array();

    /**
     * Related records
     * @var array
     */
    protected $relations = array();

    /**
     * Information if record already exists in data base
     * @var bool
     */
    public $exists = false;

    /**
     * @var bool
     */
    protected $validationEnabled = true;


    /**
     * Create a new Record instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = array())
    {
        $this->init();
        $this->fill($attributes);
    }

    /**
     * class init
     */
    public function init()
    {

    }


    /**
     * Fills record with data
     * @param array $attributes
     * @return $this
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value){
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Method validates input data
     * @param array $data
     * @param string $rulesGroups
     * @return $this
     * @throws \Netinteractive\Elegant\Exception\ValidationException
     */
    public function validate(array $data, $rulesGroups = 'all')
    {
        if ($this->validationEnabled == false) {
            return $this;
        }

        $messageBag = new MessageBag();
        $validator = \Validator::make($data, $this->getBlueprint()->getFieldsRules($rulesGroups, array_keys($data)));

        if ($validator->fails()) {
            $messages = $validator->messages()->toArray();
            foreach ($messages as $key => $message) {
                $messageBag->add($key, $message);
            }
            throw new ValidationException($messageBag);
        }

        return $this;
    }

    /**
     * Sets record blueprint
     *
     * @param Blueprint $blueprint
     * @return $this
     */
    public function setBlueprint(Blueprint $blueprint)
    {
        $this->blueprint = $blueprint;
        return $this;
    }

    /**
     * Returns record blueprint
     *
     * @return Blueprint|null
     */
    public function getBlueprint()
    {
        if (!$this->blueprint){
            return null;
        }

        return clone $this->blueprint;
    }

    /**
     * Enables data validations rules
     * @return $this
     */
    public function enableValidation()
    {
        $this->validationEnabled = true;
        return $this;
    }

    /**
     * Disabled data validations rules
     * @return $this
     */
    public function disableValidation()
    {
        $this->validationEnabled = false;
        return $this;
    }


    /**
     * Set a given attribute on the record
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $blueprint = $this->getBlueprint();

        #we check if we have a blueprint if not, then each field is an attribute
        if ($blueprint){
            #we take from blueprint information if field is a record field
            if ($blueprint->isField($key)){
                #we check if field should be stored in data storage or it's external data
                if (!$blueprint->isExternal($key)){
                    $this->attributes[$key] = $value;
                }else{
                    $this->external[$key] = $value;
                }
            }

        }else{
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    /**
     * Get an attribute from the record
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $response = null;

        if (isSet($this->attributes[$key])){
            $response = $this->attributes[$key];
        }
        elseif (isSet($this->external[$key])){
            $response = $this->external[$key];
        }
        elseif (isSet($this->relations[$key])){
            $response = $this->relations[$key];
        }

        return $response;
    }

    /**
     * Get all of the current attributes on the record
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Checks if field is as attribute
     *
     * @param $field
     * @return bool
     */
    public function isAttribute($field)
    {
        return array_key_exists($field,$this->attributes);
    }

    /**
     * Return list of external attributes (attributes that don't belong to this record)
     * @return array
     */
    public function getExternals()
    {
        return $this->external;
    }

    /**
     * Get the value of the records's primary key(s)
     *
     * @return array
     */
    public function getKey()
    {
        $result = array();
        $pkList =  $this->getBlueprint()->getPrimaryKey();

        foreach ($pkList AS $pk){
            $result[$pk] = $this->getAttribute($pk);
        }

        return $result;
    }


    /**
     * Determine if the record or given attribute(s) have been modified.
     *
     * @param  array|string|null  $attributes
     * @return bool
     */
    public function isDirty($attributes = null)
    {
        $dirty = $this->getDirty();

        if (is_null($attributes)) return count($dirty) > 0;

        if ( ! is_array($attributes)) $attributes = func_get_args();

        foreach ($attributes as $attribute)
        {
            if (array_key_exists($attribute, $dirty)) return true;
        }

        return false;
    }

    /**
     * Get the attributes that have been changed since last sync.
     *
     * @return array
     */
    public function getDirty()
    {
        $dirty = array();

        foreach ($this->attributes as $key => $value)
        {
            if ( ! array_key_exists($key, $this->original))
            {
                $dirty[$key] = $value;
            }
            elseif ($value !== $this->original[$key] &&
                ! $this->originalIsNumericallyEquivalent($key))
            {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    /**
     * Determine if the new and old values for a given key are numerically equivalent.
     *
     * @param  string  $key
     * @return bool
     */
    protected function originalIsNumericallyEquivalent($key)
    {
        $current = $this->attributes[$key];

        $original = $this->original[$key];

        return is_numeric($current) && is_numeric($original) && strcmp((string) $current, (string) $original) === 0;
    }


    /**
     * Sync the original attributes with the current.
     *
     * @return $this
     */
    public function syncOriginal()
    {
        $this->original = $this->attributes;

        return $this;
    }


    ##RELATIONS

    /**
     * @param $type
     * @param $relation
     * @return mixed
     */
    public function getRelation($type, $relation)
    {
        return $this->getBlueprint()->getRelationManager()->createRelation($type, $this, $relation);
    }

    /**
     * Set the specific relationship in the record.
     *
     * @param  string  $relation
     * @param  mixed   $value
     * @return $this
     */
    public function setRelation($relation, $value)
    {
        $this->relations[$relation] = $value;
        return $this;
    }

    /**
     * Set the entire relations array on the model.
     *
     * @param  array  $relations
     * @return $this
     */
    public function setRelations(array $relations)
    {
        $this->relations = $relations;

        return $this;
    }


    /**
     * Convert the record instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }


    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the record instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $relations = array();

        #here we are converting related record to array
        foreach ($this->relations AS $relationName=>$data){
            if ( $data instanceof \Netinteractive\Elegant\Model\Record ){
                $relations[$relationName] = $data->toArray();
            }
            else{
                if (!empty($data)){
                    foreach ($data AS $record){
                        if ( $record instanceof \Netinteractive\Elegant\Model\Record ){
                            $relations[$relationName][] = $record->toArray();
                        }
                    }
                }
            }
        }

        return array_merge($this->attributes, $this->external, $relations);
    }


    /**
     * Convert the record to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }


    /**
     * Dynamically retrieve attributes on the record.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the record.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if an attribute exists on the record.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return ((isset($this->attributes[$key]) || isset($this->external[$key])) || isset($this->relations[$key]) );
    }

    /**
     * Unset an attribute on the record.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key], $this->external[$key], $this->relations[$key]);
    }

} 