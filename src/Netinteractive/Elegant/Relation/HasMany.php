<?php namespace Netinteractive\Elegant\Relation;

use Netinteractive\Elegant\Model\Collection;

/**
 * Class HasMany
 * @package Netinteractive\Elegant\Relation
 */
class HasMany extends HasOneOrMany
{

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->query->get();
    }

    /**
     * Returns related records
     * (We need to set proper class empty record on query builder)
     * @return Collection|static[]
     */
    public function get()
    {
        $this->query->setRecord($this->related);
        return $this->query->get();
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array $records
     * @param  string $relation
     * @return array
     */
    public function initRelation(array $records, $relation)
    {
        foreach ($records as $record) {
            $record->setRelation($relation, \App('ni.elegant.model.collection', array($this->related)) );
        }

        return $records;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array $models
     * @param  \Netinteractive\Elegant\Model\Collection $results
     * @param  string $relation
     * @return array
     */
    public function match(array $records, Collection $results, $relation)
    {
        return $this->matchMany($records, $results, $relation);
    }

}