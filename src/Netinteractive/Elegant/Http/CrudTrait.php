<?php

namespace Netinteractive\Elegant\Http;

use Illuminate\Http\Request;
use Netinteractive\Elegant\Exception\ParamRequiredException;
use Netinteractive\Elegant\Exception\RecordNotFoundException;


/**
 * Class CrudTrait
 * @package Netinteractive\Elegant\Http
 */
trait CrudTrait
{
    /**
     * @var \Netinteractive\Elegant\Domain\ServiceProvider
     */
    protected $domainServiceProvider;

    /**
     * Creates and returns record
     * @param \Illuminate\Http\Request $request
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function create(Request $request)
    {
        $params = $request->all();

        return \Response::build(
           $this->getProvider()->create($params)
        );
    }

    /**
     * Request $request
     * @throws \Netinteractive\Elegant\Exception\ParamRequiredException
     * @throws \Netinteractive\Elegant\Exception\RecordNotFoundException
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function update(Request $request)
    {
        $params = $request->all();

        if(!array_key_exists('id', $params)){
            throw new ParamRequiredException('id');
        }

        $displayFilter = isSet($params['display_filter']) ? (bool)  $params['display_filter'] : false;
        $record = $this->getProvider()->getRepository()->find($params['id']);

        if(!$record || empty($params)){
            throw new RecordNotFoundException();
        }

        $record->fill($params);
        $this->getProvider()->getRepository()->save($record);

        return \Response::build(
            $record->toArray($displayFilter)
        );
    }

    /**
     * Find a single record
     * @param \Illuminate\Http\Request $request
     * @throws \Netinteractive\Elegant\Exception\RecordNotFoundException
     * @return mixed
     */
    public function find(Request $request)
    {
        $params = $request->all();

        $columns = isSet($params['columns']) ?  $params['columns'] : array('*');
        $operator =  isSet($params['operator']) ?  $params['operator'] : 'and';
        $defaultJoin = isSet($params['default_join']) ?  $params['default_join'] : true;
        $displayFilter = isSet($params['display_filter']) ? (bool)  $params['display_filter'] : false;

        #search query from repository
        $q = $this->getProvider()->getRepository()->search($params, $columns, $operator, $defaultJoin);

        #options to modify provided query
        $this->modifyFindQuery($q, $params);

        #results
        $record = $q->first();

        if(!$record || empty($params)){
            throw new RecordNotFoundException();
        }

        return \Response::build(
            $record->toArray($displayFilter)
        );
    }


    /**
     * Search records
     * @@param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function search(Request $request)
    {
        $params = $request->all();

        $columns = isSet($params['columns']) ?  $params['columns'] : array('*');
        $operator =  isSet($params['operator']) ?  $params['operator'] : 'and';
        $defaultJoin = isSet($params['default_join']) ?  $params['default_join'] : true;
        $displayFilter = isSet($params['display_filter']) ? (bool)  $params['display_filter'] : false;

        #search query from repository
        $q = $this->getProvider()->getRepository()->search($params, $columns, $operator, $defaultJoin);

        #options to modify provided query
        $this->modifyFindQuery($q, $params);

        #results
        $records = $q->get();
        
        return \Response::build(
            $records->toArray($displayFilter)
        );
    }

    /**
     * Request $request
     * @throws \Netinteractive\Elegant\Exception\ParamRequiredException
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function delete(Request $request)
    {
        $params = $request->all();

        $records = $this->getProvider()->getRepository()->findMany($params);

        foreach ($records AS $record){
            $this->getProvider()->getRepository()->delete($record);
        }

        return \Response::build($records);
    }


    /**
     * Method allow to modify find query before we get results
     * @param \Netinteractive\Elegant\Model\Query\Builder $q
     * @param array $params
     */
    public function modifyFindQuery(\Netinteractive\Elegant\Model\Query\Builder &$q, $params = null)
    {
        
    }

    /**
     * Returns model domain provider
     * @return \Netinteractive\Elegant\Domain\ServiceProvider
     */
    public function getProvider()
    {
        if(is_null($this->domainServiceProvider)){
            $this->domainServiceProvider = $this->createDomainProvider();
        }
        return $this->domainServiceProvider;
    }


    /**
     * Method delivers model domain provider
     * @return \Netinteractive\Elegant\Domain\ServiceProvider
     */
    abstract protected function createDomainProvider();
}