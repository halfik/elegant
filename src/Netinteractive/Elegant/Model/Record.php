<?php namespace Netinteractive\Elegant\Model;


use Illuminate\Support\MessageBag AS MessageBag;
use Netinteractive\Elegant\Exception\ValidationException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Carbon\Carbon;

/**
 * Class Record
 * @package Netinteractive\Elegant\Model
 */
abstract class Record implements Arrayable, Jsonable
{
    /**
     * @var Blueprint
     */
    protected $blueprint = null;

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
     * list of dirty attributes (used to make attributes dirty on demand)
     * @var null|array
     */
    protected $dirty = array();

    /**
     * Related records
     * @var array
     */
    protected $related= array();

    /**
     * Information if record already exists in database
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
        $this->syncOriginal();

        static::bootTraits();
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
        $obj = new \stdClass();
        $obj->data = $attributes;
        $obj->record = $this;

        \Event::fire('ni.elegant.record.before.fill', $obj);

        $attributes = $obj->data;

        foreach ($attributes as $key => $value){
            $this->setAttribute($key, $value);
        }

        \Event::fire('ni.elegant.record.after.fill', $obj);

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
        if ($this->validationEnabled === false) {
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
     * @param \Netinteractive\Elegant\Model $blueprint
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
        return  $this->blueprint;
    }

    /**
     * Function checks if record has a blueprint
     * @return bool
     */
    public function hasBlueprint()
    {
        if ($this->blueprint instanceof Blueprint){
            return true;
        }
        return false;
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
            else{
                $this->external[$key] = $value;
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
     * @return mixed|null
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
        elseif (isSet($this->related[$key])){
            $response = $this->related[$key];
        }

        return $response;
    }

    /**
     * Apply display filters and returns field value and
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
        $obj->record = $this;

        if ($defaultFilters == true) {
            \Event::fire('ni.elegant.record.display', $obj);
        }

        if (!empty($filters)) {
            DisplayLogic::apply($obj, $filters);
        }


        return $obj->value;
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
     * Returns list of attributes names
     * @return array
     */
    public function getAttributesKeys()
    {
        return array_keys($this->attributes);
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
     * Returns list of external attributes (attributes that don't belong to this record)
     * @return array
     */
    public function getExternals()
    {
        return $this->external;
    }


    /**
     * Returns list of attributes  in their original state
     * @return array
     */
    public function getOriginals()
    {
        if (empty($this->original)){
            $this->syncOriginal();
        }

        return $this->original;
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

        if (is_null($attributes)){
            return count($dirty) > 0;
        }

        if ( ! is_array($attributes)){
            $attributes = func_get_args();
        }

        foreach ($attributes as $attribute){
            if (array_key_exists($attribute, $dirty)){
                return true;
            }
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
        $dirty = $this->dirty;

        foreach ($this->attributes as $key => $value){
            if ( ! array_key_exists($key, $this->original)){
                $dirty[$key] = $value;
            }
            elseif ($value !== $this->original[$key] && !$this->originalIsNumericallyEquivalent($key)){
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    /**
     * Method enables to make attributes considered dirty.
     * @param array $attributes
     * @return $this
     */
    public function makeDirty(array $attributes=array())
    {
        $this->dirty  = $attributes;
        return $this;
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
     * Sync a single original attribute with its current value.
     *
     * @param  string  $attribute
     * @return $this
     */
    public function syncOriginalAttribute($attribute)
    {
        $this->original[$attribute] = $this->attributes[$attribute];

        return $this;
    }


    /**
     * Convert a DateTime to a storable string.
     *
     * @param  \DateTime|int  $value
     * @param string $format
     * @return string
     */
    public function fromDateTime($value, $format='Y-m-d H:i:s')
    {
        // If the value is already a DateTime instance, we will just skip the rest of
        // these checks since they will be a waste of time, and hinder performance
        // when checking the field. We will just return the DateTime right away.
        if ( !($value instanceof \DateTime) ){
            // If the value is totally numeric, we will assume it is a UNIX timestamp and
            // format the date as such. Once we have the date in DateTime form we will
            // format it according to the proper format for the database connection.
            if (is_numeric($value))
            {
                $value = Carbon::createFromTimestamp($value);
            }

            // If the value is in simple year, month, day format, we will format it using
            // that setup. This is for simple "date" fields which do not have hours on
            // the field. This conveniently picks up those dates and format correct.
            elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value))
            {
                $value = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
            }

            // If this value is some other type of string, we'll create the DateTime with
            // the format used by the database connection. Once we get the instance we
            // can return back the finally formatted DateTime instances to the devs.
            else
            {
                $value = Carbon::createFromFormat($format, $value);
            }
        }


        return $value->format($format);
    }



     ## TIEMSTAMP COLUMNS

    /**
     * Get a fresh timestamp for the model.
     *
     * @return \Carbon\Carbon
     */
    public function freshTimestamp()
    {
        return new Carbon();
    }

    /**
     * Update the creation and update timestamps.
     *
     * @return void
     */
    public function updateTimestamps()
    {
        $time = $this->freshTimestamp();

        if ( !$this->isDirty($this->getBlueprint()->getUpdatedAt())){
            $this->setUpdatedAt($time);
        }

        if ( !$this->exists && !$this->isDirty($this->getBlueprint()->getCreatedAt())){
            $this->setCreatedAt($time);
        }
    }

    /**
     * Set the value of the "created at" attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setCreatedAt($value)
    {
        $this->attributes[$this->getBlueprint()->getCreatedAt()] = $value;
    }

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setUpdatedAt($value)
    {
        $this->attributes[$this->getBlueprint()->getUpdatedAt()] = $value;
    }

    /**
     * Set the value of the "deleted at" attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setDeletedAt($value)
    {
        $this->attributes[$this->getBlueprint()->getDeletedAt()] = $value;
    }



    ##RELATIONS

    /**
     * Creates and returns relation object
     * @param $type
     * @param $relation
     * @return mixed
     */
    public function getRelation($type, $relation)
    {
        return $this->getBlueprint()->getRelationManager()->createRelation($type, $this, $relation);
    }

    /**
     * Returns related records
     * @param string|null $name
     * @return mixed
     */
    public function getRelated($name=null)
    {
        if (!empty($name)){
           return isSet( $this->related[$name] )?  $this->related[$name] : array();
        }

        return $this->related;
    }

    /**
     * Checks if record has any related records
     * @param string|null $name
     * @return bool
     */
    public function hasRelated($name=null)
    {
        if (!empty($name)){
            return isSet( $this->related[$name]) && count($this->related)>0 ? true : false;
        }

        return count($this->related)>0 ? true: false;
    }


    /**
     * Set the specific relationship in the record.
     *
     * @param  string  $name
     * @param  mixed   $records
     * @return $this
     */
    public function setRelated($name, $records)
    {
        $this->related[$name] = $records;
        return $this;
    }

    /**
     * Set the entire related records array on the record.
     *
     * @param  array  $related
     * @return $this
     */
    public function setRawRelated(array $related)
    {
        $this->related  = $related;

        return $this;
    }




    ##TO ARRAY, JSON, STRING


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
        $related = array();

        #here we are converting related record to array
        foreach ($this->related AS $relationName=>$data){
            if ( $data instanceof \Netinteractive\Elegant\Model\Record ){
                $related[$relationName] = $data->toArray();
            }
            else{
                $related[$relationName] = array();
                if (!empty($data)){
                    foreach ($data AS $record){
                        if ( $record instanceof \Netinteractive\Elegant\Model\Record ){
                            $related[$relationName][] = $record->toArray();
                        }
                    }
                }
            }
        }

        return array_merge($this->attributes, $this->external, $related);
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
        return ((isset($this->attributes[$key]) || isset($this->external[$key])) || isset($this->related[$key]) );
    }

    /**
     * Unset an attribute on the record.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key], $this->external[$key], $this->related[$key]);
    }

} 