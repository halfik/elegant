<?php

namespace Netinteractive\Elegant\Tests\Models\MedPersonnel;


use Netinteractive\Elegant\Model\Query\Scope AS BaseScope;
use Netinteractive\Elegant\Repository\Repository;

class Scope extends BaseScope
{
    /**
     * @param \Netinteractive\Elegant\Repository\Repository
     * @param int $medId
     * @return \Netinteractive\Elegant\Repository\Repository
     */
    public function scopeMed(Repository $repository, $medId)
    {
        $query = $repository->getQuery();

        $query->join('med',  $this->table .'.med__id', '=', 'med.id');
        $query->where('med.id', '=', $medId);

        return $repository;
    }
}
