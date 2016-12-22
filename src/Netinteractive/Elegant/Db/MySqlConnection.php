<?php

namespace Netinteractive\Elegant\Db;

use  Netinteractive\Elegant\Db\Schema\MySqlBuilder;
use  Netinteractive\Elegant\Db\Query\Processors\MySqlProcessor;
use Doctrine\DBAL\Driver\PDOMySql\Driver as DoctrineDriver;
use  Netinteractive\Elegant\Db\Query\Grammars\MySqlGrammar as QueryGrammar;
use  Netinteractive\Elegant\Db\Schema\Grammars\MySqlGrammar as SchemaGrammar;

class MySqlConnection extends Connection
{
    /**
     * Get a schema builder instance for the connection.
     *
     * @return \Netinteractive\Elegant\Db\Schema\MySqlBuilder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new MySqlBuilder($this);
    }

    /**
     * Get the default query grammar instance.
     *
     * @return \Netinteractive\Elegant\Db\Query\Grammars\MySqlGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar);
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return \Netinteractive\Elegant\Db\Schema\Grammars\MySqlGrammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new SchemaGrammar);
    }

    /**
     * Get the default post processor instance.
     *
     * @return \Netinteractive\Elegant\Db\Query\Processors\MySqlProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new MySqlProcessor;
    }

    /**
     * Get the Doctrine DBAL driver.
     *
     * @return \Doctrine\DBAL\Driver\PDOMySql\Driver
     */
    protected function getDoctrineDriver()
    {
        return new DoctrineDriver;
    }
}
