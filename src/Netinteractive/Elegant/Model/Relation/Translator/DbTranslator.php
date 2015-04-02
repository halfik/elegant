<?php namespace Netinteractive\Elegant\Model\Relation\Translator;

use \Netinteractive\Elegant\Model\Relation\TranslatorInterface;
use \Netinteractive\Elegant\Query\Builder;
use \Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class DbTranslator
 * @package Netinteractive\Elegant\Model\Relation\Translator
 */
class DbTranslator implements TranslatorInterface
{
    /**
     * @var \Netinteractive\Elegant\Query\Builder
     */
    protected $query = null;

    public function get($type, $params)
    {
        $relation = null;

        switch ($type) {
            case 'belongsTo':
                echo "i equals 0";
                break;
            case 'hasOne':
                echo "i equals 1";
                break;
            case 'hasMany':
                echo "i equals 2";
                break;
            case 'belongsToMany':
                echo "i equals 2";
                break;
        }

        return $relation;
    }

    /**
     * Set up query builder object
     * @param Builder $query
     * @return $this
     */
    public function setQuery(Builder $query)
    {
        \debug(11);
       // $this->query = $query;
        return $this;
    }

    /**
     * Returns Query Builder object
     * @return \Netinteractive\Elegant\Query\Builder
     */
    public function getQuery()
    {
        return $this->query;
    }


    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = \App($related);

        $localKey = $localKey ?: $this->getKeyName();

        return new HasOne($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }
} 