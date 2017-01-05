<?php

namespace Netinteractive\Elegant\Utils\FieldGenerator;

use Netinteractive\Elegant\Model\Blueprint;


/**
 * Class PgSql
 * @package Netinteractive\Elegant\Utils\FieldGenerator
 */
class PgSql implements DriverInterface
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'pgsql';
    }



    /**
     * Builds fields list
     * @param string $table
     * @return array
     */
    public function getFieldsList($table)
    {
        $fields = [];
        $sql = $this->getSql($table);
        $columns = \DB::select( \DB::raw($sql) );

        if($columns){
            foreach ($columns as $column){
                $fields[] = $this->analyzeColumn($column);
            }
        }

        return $fields;
    }

    /**
     * @param $column
     * @return array
     */
    protected function analyzeColumn($column)
    {
        $fieldType =  $this->getDataType($column->udt_name);

        $response = [
            $column->column_name => [
                'title' => $column->column_name,
                'type' => $this->getDataType($fieldType),
                'sortable' => false,
                'rules' => $this->getRules($column, $fieldType),
                'filters' => [
                    'fill' => [],
                    'save' => [],
                    'display' => []
                ]
            ]
        ];

        return $response;
    }

    protected function getRuleS($column, $fieldType)
    {
        $rules = [
            'any' => '',
            'insert' => '',
            'update' => '',
        ];

        switch ($fieldType){
            case Blueprint::TYPE_INT:
                 $rules['any'] = Blueprint::TYPE_INT.'|';
                break;
        }
        //PK
        if ( strpos($column->udt_name, 'serial') !== false || strpos($column->column_default, 'nextval') !== false ){
            $rules['update'] .= 'required|';
        }
        else{
            if(strtolower($column->is_nullable) === 'no'){
                $rules['any'] .= 'required|';
            }

            if($column->character_maximum_length){
                $rules['any'] .= "max:$column->character_maximum_length|";
            }
        }


        return $rules;
    }

    /**
     * @param $dbDataType
     * @return null|string
     */
    protected function getDataType($dbDataType)
    {
        $type = null;
        switch ($dbDataType){
            case 'int2':
            case 'int4':
            case 'int8':
            case 'serial2':
            case 'serial4':
            case 'serial8':
                $type = Blueprint::TYPE_INT;
                break;
            case 'float4':
            case 'float8':
            case 'decimal':
                $type = Blueprint::TYPE_DECIMAL;
                break;
            case 'date':
                $type = Blueprint::TYPE_DATE;
                break;
            case 'timestamp':
            case 'timetz':
            case 'timestamptz':
                $type = Blueprint::TYPE_DATETIME;
                break;
            default:
                $type = Blueprint::TYPE_STRING;
                break;
        }

        return $type;
    }


    /**
     * @param $table
     * @return string
     */
    protected function getSql($table)
    {
        $sql = "
            SELECT
                column_name,
                column_default,
                is_nullable,
                data_type,
                udt_name,
                character_maximum_length,
                numeric_precision,
                numeric_precision_radix,
                numeric_scale,
                datetime_precision
                
            FROM
                information_schema. COLUMNS
            WHERE
                TABLE_NAME = '$table';"
        ;

        return$sql;
    }
}
