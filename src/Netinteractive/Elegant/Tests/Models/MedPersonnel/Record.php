<?php

namespace Netinteractive\Elegant\Tests\Models\MedPersonnel;

/**
 * Class Record
 * @package Netinteractive\Elegant\Tests\Models\MedPersonnel
 */
class Record extends \Netinteractive\Elegant\Model\Record
{
    public function init()
    {
        $this->setBlueprint( Blueprint::getInstance() );
        return $this;
    }
}
