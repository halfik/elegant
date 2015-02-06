<?php  namespace Netinteractive\Elegant\Search;
use Cartalyst\Sentry\Auth\Providers\ElegantProvider;

/**
 * Class SearchTrait
 * @package Netinteractive\Elegant\Search
 * Trait, ktory wstrzykuje funkcje do wyszukiwania
 */
trait SearchTrait
{
    /**
     * metoda, ktora sluzy do przeciazania w kontrolerach, aby zmodyfikjowac zapytanie searcha
     * @param $query
     * @return mixed
     */
    protected function modifySearchQuery($query)
    {
        return $query;
    }

    /**
     * Metoda musi zwrocic model
     * @return Elegant
     */
    abstract public function Model();

    /**
     * wyszukiwarka
     * @param array $params
     * @return mixed
     */
    public function search($params=array())
    {
        if (empty($params)){
            $params = Input::all();
        }

        $columns = array('*');
        if (isSet($params['columns']) && is_array($params['columns'])){
            $columns = $params['columns'];
        }

        $query = $this->Model()->search($params, $columns);

        $this->modifySearchQuery($query);

        if (isSet($params['limit']) && is_numeric($params['limit'])){
            $query->limit($params['limit']);
        }

        if (isSet($params['orderBy'])){
            $avaibleDir = array('asc', 'desc');
            $dir = 'asc';

            if (isSet($params['orderByDir']) && in_array($params['orderByDir'], $avaibleDir)){
                $dir = $params['orderByDir'];
            }

            $query->orderBy($params['orderBy'], $dir);
        }

        $result = $query->get();

        if (!$result){
            $result = array();
        }

        return $result;
    }
}