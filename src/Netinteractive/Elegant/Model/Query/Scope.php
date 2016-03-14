<?php  namespace Netinteractive\Elegant\Model\Query;


class Scope
{
    protected $table = null;


    public function __construct($table){
        $this->table = $table;
    }
}