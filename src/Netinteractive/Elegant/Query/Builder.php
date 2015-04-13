<?php
namespace Netinteractive\Elegant\Query;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Query\Builder AS BaseBuilder;


/**
 * Class Builder
 * @package Netinteractive\Elegant\Query
 */
class Builder extends BaseBuilder
{

    /**
     * All of the available clause operators.
     *
     * @var array
     */
    protected $operators = array(
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'regexp', 'not regexp', '&&', '<@', '@>', '||'
    );

    /**
     * Execute a query for a single record by ID.
     *
     * @param  array    $ids
     * @param  array  $columns
     * @return mixed|static
     */
    public function find($ids, $columns = array('*'))
    {
        if (is_array($ids)){
            foreach ($ids AS $key=>$val){
                $this->where($key, '=', $val);
            }
        }else{
            $this->where('id', '=', $ids);
        }

        return $this->first($columns);
    }


    /**
     * Delete a record from the database.
     *
     * @param  mixed  $ids
     * @return int
     */
    public function delete($ids = null)
    {
        // If an ID is passed to the method, we will set the where clause to check
        // the ID to allow developers to simply and quickly remove a single row
        // from their database without manually specifying the where clauses.
        if ( ! is_null($ids)){
            if (is_array($ids)){
                foreach ($ids AS $key=>$val){
                    $this->where($key, '=', $val);
                }
            }else{
                $this->where('id', '=', $ids);
            }
        }

        $sql = $this->grammar->compileDelete($this);

        return $this->connection->delete($sql, $this->getBindings());
    }


    /**
     * Execute the query and get the first result.
     *
     * @param  array  $columns
     * @return \Netinteractive\Elegant\Model\Record|static|null
     */
    public function first($columns = array('*'))
    {
        return $this->take(1)->get($columns)->first();
    }

    /**
     * Returns table name
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }
}