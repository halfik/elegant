<?php namespace Netinteractive\Elegant\Http;


/**
 * Class CrudTrait
 * @package Netinteractive\Elegant\Http
 */
trait CrudTrait
{
    /**
     * @var \Netinteractive\Elegant\Model\Provider
     */
    protected $domainProvider;

    /**
     * Creates and returns record
     * @param array $params
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function create(array $params=array())
    {
       return $this->getProvider()->create($params);
    }

    /**
     *
     * @param int $id
     * @param array $params
     * @return \Netinteractive\Elegant\Model\Record
     */
    public function update($id, array $params=array())
    {
        $record = $this->getProvider()->getMapper()->find($id);

        if ($record){
            $record->fill($params);
            $this->getProvider()->getMapper()->save($record);
        }

        return $record;
    }

    /**
     * Find records
     * @param array $params
     * @return mixed
     */
    public function find(array $params=array())
    {
        return $this->getProvider()->getMapper()->findMany($params);
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