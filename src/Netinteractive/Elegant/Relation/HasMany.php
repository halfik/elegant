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
        return $this->get();
    }

    /**
     * Returns related records
     * (We need to set proper class empty record on query builder)
     * @return Collection|static[]
     */
    public function get($columns = array('*'))
    {
        $this->getQuery()->setRecord($this->getRelated());
        $this->getQuery()->from($this->getRelated()->getBlueprint()->getStorageName());

        return $this->getQuery()->get($columns);
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
            $record->setRelated($relation, \App('ni.elegant.model.collection', array(array($this->getRelated()))) );
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
        return $this->matchMany($records, $results, $relation);
    }

}
