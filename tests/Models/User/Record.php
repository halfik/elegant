<?php namespace App\Sandbox\Models\User;

use Netinteractive\Elegant\Model\Record AS BaseRecord;

class Record extends BaseRecord
{
    public function init()
    {
        $this->setBlueprint( Blueprint::getInstance() );
        return $this;
    }
} 