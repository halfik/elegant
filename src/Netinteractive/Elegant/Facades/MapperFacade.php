<?php
namespace Netinteractive\Elegant\Facades;
use Illuminate\Support\Facades\Facade;

class MapperFacade extends Facade {

    protected static function getFacadeAccessor() { return 'ni.elegant.model.mapper.db'; }

}