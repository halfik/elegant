<?php
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 06.03.15
 * Time: 10:57
 */

namespace Netinteractive\Elegant\Model;

/**
 * Class Blueprint
 * @package Netinteractive\Elegant\Model
 */
abstract class Blueprint {
    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @var array
     */
    protected $relations = array();


    /**
     * Returns list of fields
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Returns list of fields that are sortable
     * @return array
     */
    public function getSortableFields()
    {

    }

    /**
     * Returns list of fields which can be searched
     * @return array
     */
    public function getSearchableFields()
    {

    }

    /**
     * @return
     */
    public function getFieldsTitles()
    {

    }


    /**
     *
    + getFieldsTitles(): array
    + getFieldTitle(field: string): string|null
    + getFieldRules(field: string): array
    + getFieldsRules (rulesGroups: array, fields: array): array
    + getFieldsTypes (fields: array): array
    + getFieldType (field: string): string|null
    + setFieldRules (field: string, rules: array, group: string): this
    + isInFields (field: string): boolean


    + enableValidation (): this
    + disableValidation (): this

     */
} 