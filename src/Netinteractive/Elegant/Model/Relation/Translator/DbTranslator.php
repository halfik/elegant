<?php namespace Netinteractive\Elegant\Model\Relation\Translator;

use Netinteractive\Elegant\Model\Record;
use \Netinteractive\Elegant\Model\Relation\TranslatorInterface;
use \Netinteractive\Elegant\Query\Builder;
use \Illuminate\Database\Eloquent\Relations\HasOne;
use Netinteractive\Elegant\Relation\BelongsTo;
use Netinteractive\Elegant\Relation\HasMany;

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

    protected $record = null;

    /**
     * @param Record $record
     * @param array $relationData
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|mixed|null
     */
    public function get(Record $record, array $relationData)
    {
        $this->record = $record;
        $relation = null;

        switch ($relationData[0]) {
            case 'belongsTo':
                $relation = $this->belongsTo($relationData[1], $relationData[2], $relationData[3], $relationData[4]);
                break;
            case 'hasOne':

                break;
            case 'hasMany':
                $relation = $this->hasMany($relationData[1], $relationData[2], $relationData[3]);
                break;
            case 'belongsToMany':
                echo "i equals belongsToMany";
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
        $this->query = $query;
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
     * @return \Netinteractive\Elegant\Relation\HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $instance = \App($related);

        $localKey = $localKey ?: $this->getKeyName();

        return new HasOne($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }


    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string|array  $foreignKey
     * @param  string|array  $localKey
     * @return \Netinteractive\Elegant\Relation\HasMany
     */
    public function hasMany($related, $foreignKey, $localKey = null)
    {
        $instance = \App($related);

        $localKey = $localKey ? : $instance->getBlueprint()->getPrimaryKey();

        return new HasMany($this->getQuery(), $instance, $this->record,  $foreignKey, $localKey);
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param  string  $related
     * @param  string|array  $foreignKey
     * @param  string|array  $otherKey
     * @param  string  $relation
     * @return \Netinteractive\Elegant\Relation\BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $otherKey = null, $relation = null)
    {

        // If no relation name was given, we will use this debug backtrace to extract
        // the calling method's name and use that as the relationship name as most
        // of the time this will be what we desire to use for the relationships.
        if (is_null($relation))
        {
            list(, $caller) = debug_backtrace(false, 2);

            $relation = $caller['function'];
        }

        // If no foreign key was supplied, we can use a backtrace to guess the proper
        // foreign key name by using the name of the relationship function, which
        // when combined with an "_id" should conventionally match the columns.
        if (is_null($foreignKey))
        {
            $foreignKey = snake_case($relation).'__id';
        }

        $instance = \App($related);


        // Once we have the foreign key names, we'll just create a new Eloquent query
        // for the related models and returns the relationship instance which will
        // actually be responsible for retrieving and hydrating every relations.
        $query = $this->getQuery();


        $otherKey = $otherKey ?: $instance->getBlueprint()->getPrimaryKey();
        $relation = new BelongsTo($query, $instance, $this->record, $foreignKey, $otherKey, $relation);

        $relation->getKeys();

        return $relation;
    }
} 