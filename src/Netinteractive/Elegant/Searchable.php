<?php namespace Netinteractive\Elegant;
/**
 * Created by PhpStorm.
 * User: halfik
 * Date: 10.09.14
 * Time: 15:33
 */


class Searchable  {
    public static $alias;
    public static $like = array(
        'pgsql'=>'iLIKE'
    );

    /**
     * Zwraca operato like w zaleznosci od bazy danych
     * @return string
     */
    public static function getLikeOperator(){
        $driver=\Config::get('database.default');
        if(isset(self::$like[$driver])){
            return self::$like[$driver];
        }
        return 'LIKE';
    }


    /**
     * funckja do oszyczania szukanej frazy ze zbednych elementow
     * @param $keyword
     * @return string
     */
    protected static function clearKeyword($keyword){
        if (is_scalar($keyword)){
            return trim($keyword);
        }
        return $keyword;
    }

    /**
     * @param $field
     * @param string $operator
     * @return callable
     */
    public static function text($field, $operator=null){
        if (!$operator){
            $operator = static::getLikeOperator();
        }

        return function (&$q, $keyword, $logic='or') use ($field, $operator){
            $keyword = self::clearKeyword($keyword);
            if ($operator == static::getLikeOperator()){
                $keyword = '%'.$keyword.'%';
            }

            if ($logic == 'or'){
                $q->orWhere(static::$alias.'.'.$field, $operator, $keyword);
            }else{
                $q->where(static::$alias.'.'.$field, $operator, $keyword);
            }

        };
    }

    /**
     * @param $field
     * @return callable
     */
    public static function textLeft($field){
        return function (&$q, $keyword, $logic='or') use ($field){
            $keyword = self::clearKeyword($keyword);
            if ($logic == 'or'){
                $q->orWhere(static::$alias.'.'.$field, static::getLikeOperator(), '%'.$keyword);
            }else{
                $q->where(static::$alias.'.'.$field, static::getLikeOperator(), '%'.$keyword);
            }
        };
    }

    /**
     * @param $field
     * @return callable
     */
    public static function textRight($field){
        return function (&$q, $keyword, $logic='or') use ($field){
            $keyword = self::clearKeyword($keyword);
            if ($logic == 'or'){
                $q->orWhere(static::$alias.'.'.$field, static::getLikeOperator(), $keyword.'%');
            }else{
                $q->where(static::$alias.'.'.$field, static::getLikeOperator(), $keyword.'%');
            }

        };
    }

    /**
     * @param $field
     * @param string $operator
     * @return callable
     */
    public static function int($field, $operator='='){
        return function (&$q, $keyword, $logic='or') use ($field, $operator){
            $keyword = self::clearKeyword($keyword);
            if (is_numeric($keyword)){
                if ($logic == 'or'){
                    $q->orWhere(static::$alias.'.'.$field, $operator, $keyword);
                }else{
                    $q->where(static::$alias.'.'.$field, $operator, $keyword);
                }
            }
            elseif(is_array($keyword)){
                if ($logic == 'or'){
                    $q-> orWhereIn(static::$alias.'.'.$field, array_values($keyword));
                }else{
                    $q->whereIn(static::$alias.'.'.$field, array_values($keyword));
                }
            }
        };
    }

    /**
     * @param $field
     * @param string $operator
     * @return callable
     */
    public static function date($field, $operator='='){
        return function (&$q, $keyword, $logic='or') use ($field, $operator){
            $keyword = self::clearKeyword($keyword);
            if (isDate($keyword) && !is_numeric($keyword)){
                if ($logic == 'or'){
                    $q->orWhere(static::$alias.'.'.$field, $operator, $keyword);
                }else{
                    $q->where(static::$alias.'.'.$field, $operator, $keyword);
                }
            }
        };
    }
}