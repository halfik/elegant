<?php
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 06.03.15
 * Time: 10:57
 */

namespace Netinteractive\Elegant\Model;

/**
 * Class Model
 * @package Netinteractive\Elegant\Model
 */
abstract class Model
{
    /**
     * @var Blueprint
     */
    protected $blueprint;

    /**
     * Model attributes
     * @var array
     */
    protected $attributes = array();

    /**
     * Model external attributes (attributes that dosnt normaly belong to this model)
     * @var array
     */
    protected $external = array();

    /**
     * Model original attributes (before any changes were made)
     * @var array
     */
    protected $original = array();

    /**
     * Information if model already exists in data base
     * @var bool
     */
    public $exists = false;

    /**
     * Create a new Model instance.
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
     * Fills model with data
     * @param array $attributes
     * @return $this
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value)
        {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Sets model blueprint
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
     * Returns model blueprint
     *
     * @return Blueprint
     */
    public function getBlueprint()
    {
        return $this->blueprint;
    }


    /**
     * Set a given attribute on the model
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        #we check if data if model attribute or not
        if ($this->getBlueprint()->isField($key)){
            $this->attributes[$key] = $value;
        }else{
            $this->external[$key] = $value;
        }

        return $this;
    }

    /**
     * Get an attribute from the model
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return isSet($this->attributes[$key]) ? $this->attributes[$key] : $this->external[$key];
    }

    /**
     * Get all of the current attributes on the model
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
     * Return list of external attributes (attributes that don't belong to this model)
     * @return array
     */
    public function getExternals()
    {
        return $this->external;
    }



    /**
     * Determine if the model or given attribute(s) have been modified.
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
     * Convert the model instance to JSON.
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
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = $this->attributes;


        return $attributes;
    }




    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }


    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the model.
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
     * Determine if an attribute exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return ((isset($this->attributes[$key]) || isset($this->external[$key])) );
    }

    /**
     * Unset an attribute on the model.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key], $this->external[$key]);
    }

} 