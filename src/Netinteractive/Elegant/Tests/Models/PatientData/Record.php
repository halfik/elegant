<?php

namespace Netinteractive\Elegant\Tests\Models\PatientData;


class Record extends \Netinteractive\Elegant\Model\Record
{
    public function init()
    {
        $this->setBlueprint( Blueprint::getInstance() );
        return $this;
    }
}
