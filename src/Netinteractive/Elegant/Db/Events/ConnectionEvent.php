<?php

namespace Netinteractive\Elegant\Db\Events;

abstract class ConnectionEvent
{
    /**
     * The name of the connection.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The database connection instance.
     *
     * @var \Netinteractive\Elegant\Db\Connection
     */
    public $connection;

    /**
     * Create a new event instance.
     *
     * @param  \Netinteractive\Elegant\Db\Connection  $connection
     * @return void
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->connectionName = $connection->getName();
    }
}
