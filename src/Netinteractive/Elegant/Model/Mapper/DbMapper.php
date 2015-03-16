<?php
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 06.03.15
 * Time: 10:58
 */

namespace Netinteractive\Elegant\Model\Mapper;
use Netinteractive\Elegant\Query\Builder;

/**
 * Class DbMapper
 * @package Netinteractive\Elegant\Model\Mapper
 */
abstract class DbMapper
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * The database query grammar instance.
     *
     * @var \Illuminate\Database\Query\Grammars\Grammar
     */
    protected $grammar;

    /**
     * The database query post processor instance.
     *
     * @var \Illuminate\Database\Query\Processors\Processor
     */
    protected $processor;


    /**
     * Create a new db mapper
     *
     * @return void
     */
    public function __construct()
    {
        $dbManager = \App::make('DatabaseManager');
        
        $this->grammar = $grammar;
        $this->processor = $processor;
        $this->connection = $connection;
    }

    public function getQuery()
    {
        return \App::make('Builder', array($this->connection, $this->grammar, $this->processor));
    }

    public function makeFindQuery()
    {

    }

/*+ getQuery(): QueryBuilder
+ makeFindQuery(): QueryBilder*/
} 