<?php

namespace Netinteractive\Elegant\Tests\Models\MedScienceDegree;

/**
 * Class Record
 * @package Netinteractive\Elegant\Tests\Models\MedScienceDegree
 */
class Record extends \Netinteractive\Elegant\Model\Record
{
    public function init()
    {
        $this->setBlueprint( Blueprint::getInstance() );
        return $this;
    }
}
