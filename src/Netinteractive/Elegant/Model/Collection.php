<?php namespace Netinteractive\Elegant\Model;

use Illuminate\Support\Collection AS BaseCollection;


/**
 * Class Collection
 * @package Netinteractive\Elegant\Model
 */
class Collection extends BaseCollection
{
    /**
     * Add an item to the collection
     *
     * @param  \Netinteractive\Elegant\Model\Record  $item
     * @return $this
     */
    public function add(Record $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Makes all records dirty
     * @param array $attributes
     * @param bool $touchRelated
     * @return $this
     */
    public function makeDirty(array $attributes=array(), $touchRelated=false)
    {
        foreach ($this->items AS $item){
            if ($item instanceof Record){
                $item->makeDirty($attributes, $touchRelated);
            }
        }

        return $this;
    }

    /**
     * Mark records as new
     * @param bool $touchRelated
     * @return $this
     */
    public function makeNoneExists($touchRelated=false)
    {
        foreach ($this->items AS $item){
            if ($item instanceof Record){
                $item->markAsNew($touchRelated);
            }
        }

        return $this;
    }
}