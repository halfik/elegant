<?php

namespace Netinteractive\Elegant\Http;


/**
 * Class CrudTrait
 * @package Netinteractive\Elegant\Http
 */
trait CrudTrait
{
    /**
     * @var \Netinteractive\Elegant\Domain\Provider
     */
    protected $domainProvider;

    /**
     * Creates and returns record
     * @param array $params
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function create(array $params=array())
    {
       return \Response::build(
           $this->getProvider()->create($params)
       );
    }

    /**
     * @param int $id
     * @param array $params
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function update($id, array $params=array())
    {
        $displayFilter = isSet($params['display_filter']) ? (bool)  $params['display_filter'] : false;
        $record = $this->getProvider()->getMapper()->find($id);

        if ($record){
            $record->fill($params);
            $this->getProvider()->getMapper()->save($record);
        }

        return \Response::build(
            $record->toArray($displayFilter)
        );
    }

    /**
     * Find a single record
     * @param array $params
     * @return mixed
     */
    public function find(array $params=array())
    {
        $columns = isSet($params['columns']) ?  $params['columns'] : array('*');
        $operator =  isSet($params['operator']) ?  $params['operator'] : 'and';
        $defaultJoin = isSet($params['default_join']) ?  $params['default_join'] : true;
        $displayFilter = isSet($params['display_filter']) ? (bool)  $params['display_filter'] : false;

        #search query from mapper
        $q = $this->getProvider()->getMapper()->search($params, $columns, $operator, $defaultJoin);

        #options to modify provided query
        $this->modifyFindQuery($q, $params);

        #results
        $record = $q->first();

        return \Response::build(
            $record->toArray($displayFilter)
        );
    }


    /**
     * Search records
     * @param array $params
     * @return mixed
     */
    public function search(array $params=array())
    {
        $columns = isSet($params['columns']) ?  $params['columns'] : array('*');
        $operator =  isSet($params['operator']) ?  $params['operator'] : 'and';
        $defaultJoin = isSet($params['default_join']) ?  $params['default_join'] : true;
        $displayFilter = isSet($params['display_filter']) ? (bool)  $params['display_filter'] : false;

        #search query from mapper
        $q = $this->getProvider()->getMapper()->search($params, $columns, $operator, $defaultJoin);

        #options to modify provided query
        $this->modifyFindQuery($q, $params);

        #results
        $records = $q->get();
        
        return \Response::build(
            $records->toArray($displayFilter)
        );
    }

    /**
     * Find and deletes records
     * @param array $params
     */
    public function delete(array $params=array())
    {
        $records = $this->getProvider()->getMapper()->findMany($params);
        
        foreach ($records AS $record){
            $this->getProvider()->getMapper()->delete($record);
        }

        return \Response::build($params);
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
     * @return \Netinteractive\Elegant\Model\Provider|void
     */
    public function getProvider()
    {
        if(is_null($this->domainProvider)){
            $this->domainProvider = $this->createDomainProvider();
        }
        return $this->domainProvider;
    }


    /**
     * Method delivers model domain provider
     * @return \Netinteractive\Elegant\Model\Provider
     */
    abstract protected function createDomainProvider();
}