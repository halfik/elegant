<?php namespace Netinteractive\Elegant;
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 10.09.14
 * Time: 15:33
 */


class Searchable  {
    public static $alias;
    public static $like = 'LIKE';

    /**
     * @param $field
     * @param string $operator
     * @return callable
     */
    public static function orText($field,$operator=null){
        if (!$operator){
            $operator = static::$like;
        }
        return function (&$q, $keyword) use ($field, $operator){
            if ($operator == static::$like){
                $keyword = '%'.$keyword.'%';
            }
            $q->orWhere(static::$alias.'.'.$field, $operator, $keyword);
        };
    }

    /**
     * @param $field
     * @return callable
     */
    public static function orTextLeft($field){
        return function (&$q, $keyword) use ($field){
            $q->orWhere(static::$alias.'.'.$field, static::$like, '%'.$keyword);
        };
    }

    /**
     * @param $field
     * @return callable
     */
    public static function orTextRight($field){
        return function (&$q, $keyword) use ($field){
            $q->orWhere(static::$alias.'.'.$field, static::$like, $keyword.'%');
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
                $q->orWhere(static::$alias.'.'.$field, $operator, $keyword);
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
                $q->orWhere(static::$alias.'.'.$field, $operator, $keyword);
            }
        };
    }
}