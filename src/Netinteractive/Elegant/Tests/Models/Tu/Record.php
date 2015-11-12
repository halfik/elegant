<?php namespace Netinteractive\Elegant\Tests\Models\Tu;


class Record extends \Netinteractive\Elegant\Model\Record
{
    public function init()
    {
        $this->setBlueprint( Blueprint::getInstance() );
        return $this;
    }
} 