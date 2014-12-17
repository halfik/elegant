<?php namespace Netinteractive\Elegant;
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 10.09.14
 * Time: 15:33
 */

class Searchable {
    public static $alias;
    const LIKE = 'LIKE';

    public static function orText($field,$operator=self::LIKE){
        return function (&$q, $keyword) use ($field, $operator){
            if ($operator == self::LIKE){
                $keyword = '%'.$keyword.'%';
            }
            $q->orWhere(self::$alias.'.'.$field, $operator, $keyword);
        };
    }

    public static function orTextLeft($field){
        return function (&$q, $keyword) use ($field){
            $q->orWhere(self::$alias.'.'.$field, self::LIKE, '%'.$keyword);
        };
    }

    public static function orTextRight($field){
        return function (&$q, $keyword) use ($field){
            $q->orWhere(Self::$alias.'.'.$field, self::LIKE, $keyword.'%');
        };
    }

    public static function orInt($field, $operator='='){
        return function (&$q, $keyword) use ($field, $operator){
            if (is_numeric($keyword)){
                $q->orWhere(self::$alias.'.'.$field, $operator, $keyword);
            }
        };
    }

    public static function orDate($field, $operator='='){
        return function (&$q, $keyword) use ($field, $operator){
            if (isDate($keyword) && !is_numeric($keyword)){
                $q->orWhere(self::$alias.'.'.$field, $operator, $keyword);
            }
        };
    }
}