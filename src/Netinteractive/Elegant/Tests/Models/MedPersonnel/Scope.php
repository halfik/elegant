<?php
namespace Netinteractive\Elegant\Tests\Models\MedPersonnel;


use Netinteractive\Elegant\Mapper\DbMapper;
use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;

class Scope extends BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Mapper\DbMapper
     * @param int $medId
     * @return \Netinteractive\Elegant\Mapper\DbMapper
     */
    public function scopeMed(DbMapper $mapper, $medId)
    {
        $query = $mapper->getQuery();

        $query->join('med',  $this->table .'.med__id', '=', 'med.id');
        $query->where('med.id', '=', $medId);

        return $mapper;
    }
}