<?php

namespace Netinteractive\Elegant\Utils;

use Netinteractive\Elegant\Utils\FieldGenerator\DriverInterface;
use Netinteractive\Elegant\Utils\FieldGenerator\PgSql;


/**
 * Class FieldGenerator
 * @package Netinteractive\Elegant\Utils
 */
class FieldGenerator
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * FieldGenerator constructor.
     */
    public function __construct()
    {
        $this->add(
            new PgSql()
        );
    }

    /**
     * Adds strategy
     * @param DriverInterface $item
     * @param string $key
     */
    public function add(DriverInterface $item, $key = 'default')
    {
        if (!$this->hasKey($key)) {
            $this->items[$key] = [];
        }

        $name = $item->getName();
        $this->items[$key][$name] = $item;
    }
    /**
     * Delete item
     * @param string $name
     * @return void
     * @throws \Exception
     */
    public function delete($name, $key = 'default')
    {
        if(!$this->has($name, $key)){
            throw new \Exception("Item $name doesn't exists!");
        }
        unset($this->items[$key][$name]);
    }
    /**
     * Checks if item exists
     * @param string $name
     * @param string $key
     * @return bool
     */
    public function has($name, $key = 'default')
    {
        if (!$this->hasKey($key)) {
            return false;
        }
        return array_key_exists($name, $this->items[$key]);
    }
    /**
     * @param $key
     * @return bool
     */
    public function hasKey($key)
    {
        return array_key_exists($key, $this->items);
    }
    /**
     * Returns item
     * @param string $name
     * @param string $key
     * @return DriverInterface
     * @throws \Exception
     */
    public function get($name, $key = 'default')
    {
        if(!$this->has($name, $key)){
            throw new \Exception("Item $name dosn't exists!");
        }
        return $this->items[$key][$name];
    }
    /**
     * Return all items
     * @param string $key
     * @return array
     */
    public function all($key = 'default')
    {
        if (!$this->hasKey($key)) {
            return [];
        }
        return $this->items[$key];
    }
}
