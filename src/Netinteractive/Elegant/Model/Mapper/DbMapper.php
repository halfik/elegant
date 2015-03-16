<?php
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 06.03.15
 * Time: 10:58
 */

namespace Netinteractive\Elegant\Model\Mapper;


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
     * Create a new db mapper
     *
     * @return void
     */
    public function __construct($connection=null)
    {
        $this->connection = \App('db')->connection($connection);
    }


    public function getQuery()
    {
        return \App::make('Builder', array($this->connection,  $this->connection->getQueryGrammar(), $this->connection->getPostProcessor()));
    }

    public function makeFindQuery()
    {

    }
} 