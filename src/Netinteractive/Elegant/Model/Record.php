<?php namespace Netinteractive\Elegant\Model;


use Illuminate\Support\MessageBag AS MessageBag;
use Netinteractive\Elegant\Exception\ValidationException;

/**
 * Class Record
 * @package Netinteractive\Elegant\Model
 */
abstract class Record
{
    /**
     * @var array
     */
    static protected $blueprints = array();

    /**
     * Record attributes
     * @var array
     */
    protected $attributes = array();

    /**
     * Record external attributes (attributes that dosnt normaly belong to this record)
     * @var array
     */
    protected $external = array();

    /**
     * Record original attributes (before any changes were made)
     * @var array
     */
    protected $original = array();

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

    public function init()
    {
        return $this;
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
    public static function setBlueprint(Blueprint $blueprint)
    {
      //  self::$blueprint[get_class(self)] = $blueprint;
    }

    /**
     * Returns record blueprint
     *
     * @return Blueprint
     */
    public static function getBlueprint()
    {
        return get_class(self);
        exit;
        return self::$blueprint[get_class(self)];
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
        #we check if data if record attribute or not
        if ($this->getBlueprint()->isField($key)){
            $this->attributes[$key] = $value;
        }else{
            $this->external[$key] = $value;
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
        return isSet($this->attributes[$key]) ? $this->attributes[$key] : $this->external[$key];
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
        $attributes = array_merge($this->attributes, $this->external);

        return $attributes;
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
        return ((isset($this->attributes[$key]) || isset($this->external[$key])) );
    }

    /**
     * Unset an attribute on the record.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key], $this->external[$key]);
    }

} 