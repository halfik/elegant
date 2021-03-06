<?php

namespace Netinteractive\Elegant\Relation;

use Netinteractive\Elegant\Model\Record;

/**
 * Class Pivot
 * @package Netinteractive\Elegant\Relation
 */
class Pivot extends Record
{
	/**
	 * The parent record of the relationship.
	 *
	 * @var \Netinteractive\Elegant\Model\Record
	 */
	protected $parent;

    /**
     * The name of the foreign key column.
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * The name of the "other key" column.
     *
     * @var string
     */
    protected $otherKey;

    const PREFIX = 'pivot';


	/**
	 * Create a new pivot record instance.
	 *
	 * @param  \Netinteractive\Elegant\Model\Record  $parent
	 * @param  array   $attributes
	 * @param  string  $table
	 * @param  bool    $exists
	 * @return void
	 */
	public function __construct(Record $parent, $attributes, $table, $exists = false)
	{
		parent::__construct();

		// The pivot model is a "dynamic" model since we will set the tables dynamically
		// for the instance. This allows it work for any intermediate tables for the
		// many to many relationship that are defined by this developer's classes.
		$this->fill($attributes);


		// We store off the parent instance so we will access the timestamp column names
		// for the model, since the pivot model timestamps aren't easily configurable
		// from the developer's point of view. We can use the parents to get these.
		$this->parent = $parent;

		$this->setExists($exists);
	}

	/**
	 * Get the foreign key column name.
	 *
	 * @return string
	 */
	public function getForeignKey()
	{
		return $this->foreignKey;
	}

	/**
	 * Get the "other key" column name.
	 *
	 * @return string
	 */
	public function getOtherKey()
	{
		return $this->otherKey;
	}


	/**
	 * Set the key names for the pivot model instance.
	 *
	 * @param  string  $foreignKey
	 * @param  string  $otherKey
	 * @return $this
	 */
	public function setPivotKeys($foreignKey, $otherKey)
	{
        $this->foreignKey = $foreignKey;
        $this->otherKey = $otherKey;

		return $this;
	}


    /**
     * Get the query builder for a delete operation on the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getDeleteQuery($query)
    {
        $foreign = $this->getAttribute($this->foreignKey);

        $query->where($this->foreignKey, $foreign);

        return $query->where($this->otherKey, $this->getAttribute($this->otherKey));
    }

}
