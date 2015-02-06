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
        $avaibleDir = array('asc', 'desc');
        $avaibleOperator = array('and', 'or');
        $dir = 'asc';
        $operator = 'and';

        if (empty($params)){
            $params = Input::all();
        }

        $columns = array('*');
        if (isSet($params['columns']) && is_array($params['columns'])){
            $columns = $params['columns'];
        }


        if (isSet($params['operator']) && in_array($params['operator'], $avaibleOperator)){
            $operator = $params['operator'];
        }

        $query = $this->Model()->search($params, $columns, $operator);

        $this->modifySearchQuery($query);

        if (isSet($params['limit']) && is_numeric($params['limit'])){
            $query->limit($params['limit']);
        }


        if (isSet($params['orderByDir']) && in_array($params['orderByDir'], $avaibleDir)){
            $dir = $params['orderByDir'];
        }

        if (isSet($params['orderBy']) && $this->Model()->isField($params['orderBy'])){

            $query->orderBy($params['orderBy'], $dir);
        }


        $result = $query->get();

        if (!$result){
            $result = array();
        }

        return $result;
    }
}