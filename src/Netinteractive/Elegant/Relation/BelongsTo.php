<?php

namespace Netinteractive\Elegant\Relation;

use Netinteractive\Elegant\Exception\PkFkSizeException;
use Netinteractive\Elegant\Model\Query\Builder;
use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Model\Collection;


/**
 * Class BelongsTo
 * @package Netinteractive\Elegant\Relation
 */
class BelongsTo extends Relation
{

    /**
     * The foreign key of the parent record.
     *
     * @var array
     */
    protected $foreignKey;

    /**
     * The associated key on the parent record.
     *
     * @var array
     */
    protected $otherKey;

    /**
     * The name of the relationship.
     *
     * @var string
     */
    protected $relation;

    /**
     * Create a new belongs to relationship instance.
     *
     * @param  \Netinteractive\Elegant\Model\Query\Builder $query
     * @param  \Netinteractive\Elegant\Model\Record $related
     * @param  \Netinteractive\Elegant\Model\Record $parent
     * @param  string|array $foreignKey
     * @param  string|array $otherKey
     * @param  string $relation
     * @return void
     * @throws \Netinteractive\Elegant\Exception\PkFkSizeException
     */
    public function __construct(Builder $query, Record $related, Record $parent, $foreignKey, $otherKey, $relation)
    {
        if (!is_array($foreignKey)) {
            $foreignKey = array($foreignKey);
        }

        if (!is_array($otherKey)) {
            $otherKey = array($otherKey);
        }

        #we check if key sizes are same
        if (count($otherKey) <> count($foreignKey)) {
            throw new PkFkSizeException($otherKey, $foreignKey);
        }

        $this->otherKey = $otherKey;
        $this->relation = $relation;
        $this->foreignKey = $foreignKey;
        $this->related = $related;

        parent::__construct($query, $parent);
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        $this->getQuery()->setRecord($this->getRelated());
        return $this->getQuery()->first();
    }

    /**
     * Returns related records
     * (We need to set proper class empty record on query builder)
     * @return Collection|static[]
     */
    public function get($columns = array())
    {
        $this->getQuery()->setRecord($this->getRelated());
        return $this->getQuery()->get();
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            // For belongs to relationships, which are essentially the inverse of has one
            // or has many relationships, we need to actually query on the primary key
            // of the related models matching on the foreign key that's on a parent.
            $table = $this->getRelated()->getBlueprint()->getStorageName();
            $fkList = $this->getForeignKey();

            foreach ($this->getOtherKey() AS $otherKey) {
                $fk = array_shift($fkList);
                $this->getQuery()->where($table . '.' . $otherKey, '=', $this->getParent()->{$fk});
            }
        }
    }


    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  \Netinteractive\Elegant\Model\Collection $records
     * @return void
     */
    public function addEagerConstraints(Collection $records)
    {
        $keys = $this->getEagerRecordKeys($records);
        $this->getQuery()->from($this->getRelated()->getBlueprint()->getStorageName());

        // We'll grab the primary key name of the related models since it could be set to
        // a non-standard name and not "id". We will then construct the constraint for
        // our eagerly loading query so it returns the proper models from execution.

        foreach ($this->getOtherKey() AS $otherKey) {
            $key = $this->getRelated()->getBlueprint()->getStorageName() . '.' . $otherKey;
            $this->getQuery()->whereIn($key, array_shift($keys));
        }
    }

    /**
     * Gather the keys from an array of related records.
     *
     * @param  \Netinteractive\Elegant\Model\Collection $records
     * @return array
     */
    protected function getEagerRecordKeys(Collection $records)
    {
        $keys = array();

        // First we need to gather all of the keys from the parent models so we know what
        // to query for via the eager loading query. We will add them to an array then
        // execute a "where in" statement to gather up all of those related records.
        foreach ($records as $record) {
            foreach ($this->getForeignKey() AS $fk) {
                if (!is_null($value = $record->{$fk})) {
                    $keys[$fk][] = $value;
                }
            }
        }

        // If there are no keys that were not null we will just return an array with 0 in
        // it so the query doesn't fail, but will not return any results, which should
        // be what this developer is expecting in a case where this happens to them.
        foreach ($keys AS $key => $val) {
            if (count($keys[$key]) == 0) {
                $keys[$key][] = 0;
            }
        }

        #unique on key values
        foreach ($keys AS $key => $val) {
            $keys[$key] = array_unique($keys[$key]);
        }

        return $keys;
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  \Netinteractive\Elegant\Model\Collection $records
     * @param  string $relation
     * @return array
     */
    public function initRelation(Collection $records, $relation)
    {
        foreach ($records as $record) {
            $record->setRelated($relation, null);
        }

        return $records;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  \Netinteractive\Elegant\Model\Collection $records
     * @param  \Netinteractive\Elegant\Model\Collection $results
     * @param  string $relation
     * @return array
     */
    public function match(Collection $records, Collection $results, $relation)
    {

        // First we will get to build a dictionary of the child models by their primary
        // key of the relationship, then we can easily match the children back onto
        // the parents using that dictionary and the primary key of the children.
        $dictionary = array();

        foreach ($results as $result) {
            foreach ($this->getOtherKey() AS $otherKey) {
                $dictionary[$result->$otherKey] = $result;
            }
        }

        // Once we have the dictionary constructed, we can loop through all the parents
        // and match back onto their children using these keys of the dictionary and
        // the primary key of the children to map them onto the correct instances.
        foreach ($records as $record) {
            foreach ($this->getForeignKey() AS $fk) {
                if (isset($dictionary[$record->$fk])) {
                    $record->setRelated($relation, $dictionary[$record->$fk]);
                }
            }

        }

        return $records;
    }

    /**
     * Associate the model instance to the given parent.
     *
     * @param  \Netinteractive\Elegant\Model\Record $record
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function associate(Record $record)
    {
        $otherKeys = $this->getOtherKey();
        foreach ($this->getForeignKey() AS $fk) {
            $this->getParent()->setAttribute($fk, $record->getAttribute(array_shift($otherKeys)));
        }

        return $this->getParent()->setRelated($this->relation, $record);
    }

    /**
     * Dissociate previously associated record from the given parent.
     *
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function dissociate()
    {
        foreach ($this->getForeignKey() AS $fk) {
            $this->getParent()->setAttribute($fk, null);
        }

        return $this->getParent()->setRelated($this->relation, null);
    }


    /**
     * Get the foreign key of the relationship.
     *
     * @return array
     */
    public function getForeignKey(Record $record=null)
    {
        return $this->foreignKey;
    }

    /**
     * Get the fully qualified foreign key of the relationship.
     *
     * @return string
     */
    public function getQualifiedForeignKey()
    {
        $fkList = $this->getForeignKey();

        foreach ($fkList AS $index=>$fk){
            $fkList[$index] = $this->getParent()->getBlueprint()->getStorageName() . '.' . $fk;
        }
        return $fkList;
    }

    /**
     * Get the associated key of the relationship.
     *
     * @return array
     */
    public function getOtherKey()
    {
        return $this->otherKey;
    }

}
