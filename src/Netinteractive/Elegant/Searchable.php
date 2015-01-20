<?php namespace Netinteractive\Elegant;


/**
 * Class Searchable
 * @package Netinteractive\Elegant
 * Klasa dostarczajaca modelowi metod do definiowana wyszukiwania po polach
 */
class Searchable{
    public static $alias;
    const LIKE = 'LIKE';

    /**
     * @param $field
     * @param string $operator
     * @return callable
     */
    public static function orText($field,$operator=self::LIKE){
        return function (&$q, $keyword) use ($field, $operator){
            if ($operator == self::LIKE){
                $keyword = '%'.$keyword.'%';
            }
            $q->orWhere(self::$alias.'.'.$field, $operator, $keyword);
        };
    }

    /**
     * @param $field
     * @return callable
     */
    public static function orTextLeft($field){
        return function (&$q, $keyword) use ($field){
            $q->orWhere(self::$alias.'.'.$field, self::LIKE, '%'.$keyword);
        };
    }

    /**
     * @param $field
     * @return callable
     */
    public static function orTextRight($field){
        return function (&$q, $keyword) use ($field){
            $q->orWhere(Self::$alias.'.'.$field, self::LIKE, $keyword.'%');
        };
    }

    /**
     * @param $field
     * @param string $operator
     * @return callable
     */
    public static function orInt($field, $operator='='){
        return function (&$q, $keyword) use ($field, $operator){
            if (is_numeric($keyword)){
                $q->orWhere(self::$alias.'.'.$field, $operator, $keyword);
            }
        };
    }

    /**
     * @param $field
     * @param string $operator
     * @return callable
     */
    public static function orDate($field, $operator='='){
        return function (&$q, $keyword) use ($field, $operator){
            if (isDate($keyword) && !is_numeric($keyword)){
                $q->orWhere(self::$alias.'.'.$field, $operator, $keyword);
            }
        };
    }
}