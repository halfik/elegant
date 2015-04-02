<?php namespace Netinteractive\Elegant\Model\Relation;


/**
 * Interface TranslatorInterface
 * @package Netinteractive\Elegant\Model\Relation
 */
interface TranslatorInterface
{
    /**
     * Returns relation object
     * @param $type
     * @param $params
     * @return mixed
     */
    public function get($type, $params);
} 