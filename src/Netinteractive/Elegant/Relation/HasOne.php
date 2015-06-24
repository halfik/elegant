<?php namespace Netinteractive\Elegant\Relation;


use Netinteractive\Elegant\Model\Collection;

/**
 * Class HasOne
 * @package Netinteractive\Elegant\Relation
 */
class HasOne extends HasOneOrMany
{

	/**
	 * Get the results of the relationship.
	 *
	 * @return mixed
	 */
	public function getResults()
	{
		return $this->query->first();
	}

	/**
	 * Initialize the relation on a set of models.
	 *
	 * @param  \Netinteractive\Elegant\Model\Collection   $records
	 * @param  string  $relation
	 * @return array
	 */
	public function initRelation(Collection $records, $relation)
	{
		foreach ($records as $record){
            $record->setRelated($relation, null);
		}

		return $records;
	}

	/**
	 * Match the eagerly loaded results to their parents.
	 *
	 * @param  \Netinteractive\Elegant\Model\Collection   $records
	 * @param  \Netinteractive\Elegant\Model\Collection $results
	 * @param  string  $relation
	 * @return array
	 */
	public function match(Collection $records, Collection $results, $relation)
	{
		return $this->matchOne($records, $results, $relation);
	}

}
