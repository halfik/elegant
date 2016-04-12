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


    public function create(array $params=array())
    {

    }

    public function update(array $params=array())
    {

    }

    public function find(array $params=array())
    {

    }

    public function delete(array $params=array())
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