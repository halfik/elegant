<?php

namespace Netinteractive\Elegant\Tests\Models\Med;

/**
 * Class Record
 * @package Netinteractive\Elegant\Tests\Models\Med
 */
class Record extends \Netinteractive\Elegant\Model\Record
{
    public function init()
    {
        $this->setBlueprint( Blueprint::getInstance() );
        return $this;
    }
} 