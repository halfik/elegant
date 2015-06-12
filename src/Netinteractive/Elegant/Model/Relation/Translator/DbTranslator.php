<?php namespace Netinteractive\Elegant\Model\Relation\Translator;

use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Model\Relation\TranslatorInterface;
use Netinteractive\Elegant\Model\Query\Builder;
use Netinteractive\Elegant\Relation\HasOne;
use Netinteractive\Elegant\Relation\BelongsTo;
use Netinteractive\Elegant\Relation\BelongsToMany;
use Netinteractive\Elegant\Relation\HasMany;

/**
 * Class DbTranslator
 * @package Netinteractive\Elegant\Model\Relation\Translator
 */
class DbTranslator implements TranslatorInterface
{
    /**
     * @var \Netinteractive\Elegant\Model\Query\Builder|null
     */
    protected $query = null;

    /**
     * @var \Netinteractive\Elegant\Model\Record|null
     */
    protected $record = null;

    /**
     * @param Record $record
     * @param array $relationData
     * @return \Netinteractive\Elegant\Relation\Relation|mixed|null
     */
    public function get(Record $record, $relationName, array $relationData)
    {
        $this->record = $record;
        $relation = null;

        switch ($relationData[0]) {
            case 'belongsTo':
                $relation = $this->belongsTo($relationData[1], $relationData[2], $relationData[3], $relationName);
                break;
            case 'hasOne':
                $relation = $this->hasOne($relationData[1], $relationData[2], $relationData[3]);
                break;
            case 'hasMany':
                $relation = $this->hasMany($relationData[1], $relationData[2], $relationData[3]);
                break;
            case 'belongsToMany':
                $relation = $this->belongsToMany($relationData[1], $relationData[2][0], $relationData[2][1], $relationData[2][2], $relationName);
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
     * @return \Netinteractive\Elegant\Db\Query\Builder
     */
    public function getQuery()
    {
        return $this->query;
    }


    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $related
     * @param  string|array  $foreignKey
     * @param  string|array  $localKey
     * @return \Netinteractive\Elegant\Relation\HasOne
     */
    public function hasOne($related, $foreignKey, $localKey=null)
    {
        $instance = \App($related);

        $localKey = $localKey ? : $instance->getBlueprint()->getPrimaryKey();

        return new HasOne($this->getQuery(), $instance, $this->record,  $foreignKey, $localKey);
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
    public function belongsTo($related, $foreignKey, $otherKey, $relation)
    {
        $instance = \App($related);


        // Once we have the foreign key names, we'll just create a new Eloquent query
        // for the related models and returns the relationship instance which will
        // actually be responsible for retrieving and hydrating every relations.
        $query = $this->getQuery();

        return new BelongsTo($query, $instance, $this->record, $foreignKey, $otherKey, $relation);
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param  string        $related
     * @param  string        $table
     * @param  string|array  $foreignKey
     * @param  string|array  $otherKey
     * @param  string        $relation
     * @return \Netinteractive\Elegant\Relation\BelongsToMany
     */
    public function belongsToMany($related, $table, $foreignKey, $otherKey, $relation)
    {
        $instance = \App($related);

        // Now we're ready to create a new query builder for the related model and
        // the relationship instances for the relation. The relations will set
        // appropriate query constraint and entirely manages the hydrations.
        $dbMapper = \App('ElegantDbMapper', array($related));

        return new BelongsToMany( $dbMapper->getQuery(), $instance, $this->record, $table, $foreignKey, $otherKey, $relation);
    }
} 