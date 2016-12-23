<?php

namespace Netinteractive\Elegant\Tests\Models\User;

/**
 * Class Record
 * @package Netinteractive\Elegant\Tests\Models\User
 */
class Record extends \Netinteractive\Elegant\Model\Record
{
    public function init()
    {
        $this->setBlueprint(Blueprint::getInstance());
        return $this;
    }
}