<?php namespace Netinteractive\Elegant\Model;

use Illuminate\Support\Collection AS BaseCollection;


/**
 * Class Collection
 * @package Netinteractive\Elegant\Model
 */
class Collection extends BaseCollection
{
    /**
     * Add an item to the collection.
     *
     * @param  mixed  $item
     * @return $this
     */
    public function add($item)
    {
        $this->items[] = $item;

        return $this;
    }
}