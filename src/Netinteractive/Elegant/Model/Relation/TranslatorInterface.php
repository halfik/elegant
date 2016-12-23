<?php

namespace Netinteractive\Elegant\Model\Relation;

use Netinteractive\Elegant\Model\Record;

/**
 * Interface TranslatorInterface
 * @package Netinteractive\Elegant\Models\Relation
 */
interface TranslatorInterface
{
    /**
     * Returns relation object
     * @param  \Netinteractive\Elegant\Model\Record  $record
     * @param  string $relationName
     * @param  array  $relationData
     * @return mixed
     */
    public function get(Record $record, $relationName, array $relationData);
} 