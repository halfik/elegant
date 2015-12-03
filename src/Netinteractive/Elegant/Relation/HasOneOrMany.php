<?php namespace Netinteractive\Elegant\Relation;

use Netinteractive\Elegant\Exception\PkFkSizeException;
use Netinteractive\Elegant\Model\Query\Builder;
use Netinteractive\Elegant\Model\Record;
use Netinteractive\Elegant\Model\Collection;

/**
 * Class HasOneOrMany
 * @package Netinteractive\Elegant\Relation
 */
abstract class HasOneOrMany extends Relation
{

    /**
     * The foreign key of the parent model.
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * The local key of the parent model.
     *
     * @var string
     */
    protected $localKey;


    /**
     * Create a new has many relationship instance.
     *
     * @param  \Netinteractive\Elegant\Model\Query\Builder $query
     * @param  \Netinteractive\Elegant\Model\Record $related
     * @param  \Netinteractive\Elegant\Model\Record $parent
     * @param  string $foreignKey
     * @param  string $localKey
     *
     * @return void
     * @throws \Netinteractive\Elegant\Exception\PkFkSizeException
     */
    public function __construct(Builder $query, Record $related, Record $parent, $foreignKey, $localKey)
    {
        if (!is_array($foreignKey)) {
            $foreignKey = array($foreignKey);
        }

        if (!is_array($localKey)) {
            $localKey = array($localKey);
        }

        #we check if key sizes are same
        if (count($localKey) <> count($foreignKey)) {
            throw new PkFkSizeException($localKey, $foreignKey);
        }


        $this->localKey = $localKey;
        $this->foreignKey = $foreignKey;
        $this->related = $related;

        parent::__construct($query, $parent);
    }

    /**
     * Get the local key
     * @return array|string
     */
    public function getLocalKey()
    {
        return $this->localKey;
    }

    /**
     * Get the foreign key for the relationship.
     * @return array|string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        if (static::$constraints) {
            $keys = $this->getParentKey();

            foreach ($this->getForeignKey() AS $index=>$key){

                if (isSet($keys[$index])){
                    $this->getQuery()->where($key, '=', $keys[$index]);
                }
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
        $this->getQuery()->from($this->getRelated()->getBlueprint()->getStorageName());

        $keys = $this->getKeys($records, $this->getLocalKey());

        foreach ($this->getForeignKey() AS $fk) {
            $localKeys = array_shift($keys);
            $this->getQuery()->whereIn($fk, $localKeys);
        }
    }


    /**
     * Match the eagerly loaded results to their single parents.
     *
     * @param  \Netinteractive\Elegant\Model\Collection $records
     * @param  \Netinteractive\Elegant\Model\Collection $results
     * @param  string $relation
     * @return array
     */
    public function matchOne(Collection $records, Collection $results, $relation)
    {
        return $this->matchOneOrMany($records, $results, $relation, 'one');
    }

    /**
     * Match the eagerly loaded results to their many parents.
     *
     * @param  \Netinteractive\Elegant\Model\Collection $records
     * @param  \Netinteractive\Elegant\Model\Collection $results
     * @param  string $relation
     * @return array
     */
    public function matchMany(Collection $records, Collection $results, $relation)
    {
        return $this->matchOneOrMany($records, $results, $relation, 'many');
    }

    /**
     * Match the eagerly loaded results to their many parents.
     *
     * @param  \Netinteractive\Elegant\Model\Collection $records
     * @param  \Netinteractive\Elegant\Model\Collection $results
     * @param  string $relation
     * @param  string $type
     * @return array
     */
    protected function matchOneOrMany(Collection $records, Collection $results, $relation, $type)
    {
        $dictionary = $this->buildDictionary($results);

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($records as $record) {
            foreach ($this->getLocalKey() AS $lk) {
                $key = $record->getAttribute($lk);

                if (isset($dictionary[$key])) {
                    $related = $this->getRelationValue($dictionary, $key, $type);
                    $record->setRelated($relation, $related);
                }
            }

        }

        return $records;
    }

    /**
     * Get the value of a relationship by one or many type.
     *
     * @param  array $dictionary
     * @param  string $key
     * @param  string $type
     * @return mixed
     */
    protected function getRelationValue(array $dictionary, $key, $type)
    {
        $value = $dictionary[$key];

        return $type == 'one' ? reset($value) : \App('ni.elegant.model.collection', array($value));
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param  \Netinteractive\Elegant\Model\Collection $results
     * @return array
     */
    protected function buildDictionary(Collection $results)
    {
        $dictionary = array();

        foreach ($this->getForeignKey() AS $fk) {
            $foreign = $this->getPlainForeignKey($fk);

            // First we will create a dictionary of models keyed by the foreign key of the
            // relationship as this will allow us to quickly access all of the related
            // models without having to do nested looping which will be quite slow.
            foreach ($results as $record) {
                $dictionary[$record->{$foreign}][] = $record;
            }
        }

        return $dictionary;
    }



    /**
     * Get the plain foreign key.
     * @param  string $name
     * @return string
     */
    public function getPlainForeignKey($name)
    {
        $segments = array();

        foreach ($this->getForeignKey() AS $key => $val) {
            if ($val == $key) {
                $segments = explode('.', $val);
            }
        }


        return $segments[count($segments) - 1];
    }

    /**
     * Get the key value of the parent's local key.
     *
     * @return mixed
     */
    public function getParentKey()
    {
        $response = array();

        foreach ($this->getLocalKey() AS $key){
            $response[$key] = $this->getParent()->getAttribute($key);
        }

        return $response;
    }

    /**
     * Get the fully qualified parent key name.
     *
     * @return string
     */
    public function getQualifiedParentKeyName()
    {
        $response = $this->getLocalKey();

        foreach ($response AS $index=>$key){
            $response[$index] = $this->getParent()->getBlueprint()->getStorageName() . '.' . $key;
        }

        return $response;
    }

}
