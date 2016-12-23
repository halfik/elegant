<?php

namespace Netinteractive\Elegant\Model\Relation;


use Netinteractive\Elegant\Exception\RelationDoesntExistsException;
use Netinteractive\Elegant\Exception\TranslatorNotRegisteredException;
use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Model\Relation\Translator\DbTranslator;


/**
 * Class Manager
 * @package Netinteractive\Elegant\Models\Relation
 */
class Manager
{
    /**
     * @var array
     */
    protected $relations = array();

    /**
     * @var array
     */
    protected $translators = array();

    /**
     * @var null
     */
    protected $currentTranslator = null;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->registerTranslator('db', new DbTranslator());
        $this->setCurrentTranslator('db');
    }


    /**
     * Sets current translator name
     * @param string $name
     * @return $this
     */
    public function setCurrentTranslator($name)
    {
        $this->currentTranslator = $name;
    }


    /**
     * Return current translator name
     * @return null|string
     */
    public function getCurrentTranslator()
    {
        return $this->currentTranslator;
    }

    /**
     * Sets relations translator object
     * @param TranslatorInterface $translator
     * @return $this
     */
    public function registerTranslator($name, TranslatorInterface $translator)
    {  
        $this->translators[$name] = $translator;
        return $this;
    }

    /**
     * Returns relations translator object
     * @return null|TranslatorInterface
     * @throws \Netinteractive\Elegant\Exception\TranslatorNotRegisteredException
     */
    public function getTranslator($name=null)
    {
        if (empty($name)){
            $name = $this->getCurrentTranslator();
        }

        if (!$this->hasTranslator($name)){
            throw new TranslatorNotRegisteredException($name);
        }

        return $this->translators[$name];
    }

    /**
     * Method checks if translator is registered
     * @param string $name
     * @return bool
     */
    public function hasTranslator($name)
    {
        if (!array_key_exists($name, $this->translators)){
            return false;
        }

        return true;
    }

    /**
     * Returns relation definition
     * @param string $relationName
     * @return mixed
     * @throws \Netinteractive\Elegant\Exception\RelationDoesntExistsException
     */
    public function getRelation($relationName)
    {
        if (!$this->hasRelation($relationName)){
            throw new RelationDoesntExistsException($relationName);
        }

        return $this->relations[$relationName];
    }

    /**
     * Method checks if relation exists
     * @param $relationName
     * @return bool
     */
    public function hasRelation($relationName)
    {
        if (!isSet($this->relations[$relationName])){
            return false;
        }
        return true;
    }

    /**
     * @param \Netinteractive\Elegant\Model\Record  $record
     * @param string $relationName
     * @param string $type
     * @return mixed
     */
    public function createRelation(Record $record, $relationName, $type=null)
    {
        return $this->getTranslator($type)->get($record, $relationName, $this->getRelation($relationName));
    }


    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param  string  $relationName
     * @param  string  $relatedModel
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  string  $relation
     * @return $this
     */
    public function belongsTo($relationName, $relatedModel, $foreignKey = null, $otherKey = null, $relation = null)
    {
        $this->relations[$relationName] = array('belongsTo', $relatedModel, $foreignKey, $otherKey, $relation);
        return $this;
    }


    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $relationName
     * @param  string  $relatedModel
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return $this
     */
    public function hasOne($relationName,$relatedModel, $foreignKey = null, $localKey = null)
    {
        $this->relations[$relationName] = array('hasOne', $relatedModel, $foreignKey, $localKey);
        return $this;
    }


    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $relationName
     * @param  string  $relatedModel
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return $this
     */
    public function hasMany($relationName, $relatedModel, $foreignKey = null, $localKey = null)
    {
        $this->relations[$relationName] = array('hasMany', $relatedModel, $foreignKey, $localKey);
        return $this;
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param  string  $relationName
     * @param  string  $relatedModel
     * @param  string  $table
     * @param  string  $foreignKey
     * @param  string  $otherKey
     * @param  string  $relation
     * @return $this
     */
    public function belongsToMany($relationName, $relatedModel, $table = null, $foreignKey = null, $otherKey = null, $relation = null)
    {
        $this->relations[$relationName] = array('belongsToMany', $relatedModel, $table, $foreignKey, $otherKey, $relation);
        return $this;
    }

} 