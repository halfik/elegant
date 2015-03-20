<?php namespace Netinteractive\Elegant\Search;


/**
 * Class Searchable
 * @package Netinteractive\Elegant\Search
 */
class Searchable
{
    public static $alias;
    public static $like = array(
        'pgsql' => 'iLIKE'
    );

    /**
     * Returns like operator
     * @return string
     */
    public static function getLikeOperator()
    {
        $driver = \Config::get('database.default');
        if (isset(self::$like[$driver])) {
            return self::$like[$driver];
        }
        return 'LIKE';
    }


    /**
     * Method clears keywrod
     * @param $keyword
     * @return string
     */
    protected static function clearKeyword($keyword)
    {
        if (is_scalar($keyword)) {
            return trim($keyword);
        }
        return $keyword;
    }

    /**
     * @param $field
     * @return string
     */
    protected static function prepareField($field)
    {
        if (empty(static::$alias )){
           return $field;
        }

        return  $field = static::$alias . '.' .$field;
    }

    /**
     * @param $field
     * @param string $operator
     * @return callable
     */
    public static function text($field, $operator = null)
    {
        if (!$operator) {
            $operator = static::getLikeOperator();
        }

        return function (&$q, $keyword, $logic = 'or') use ($field, $operator) {
            $keyword = self::clearKeyword($keyword);
            if ($operator == static::getLikeOperator()) {
                $keyword = '%' . $keyword . '%';
            }

            if (strtolower($logic) == 'or') {
                $q->orWhere(static::prepareField($field), $operator, $keyword);
            } else {
                $q->where(static::prepareField($field), $operator, $keyword);
            }

        };
    }

    /**
     * @param $field
     * @return callable
     */
    public static function textLeft($field)
    {
        return function (&$q, $keyword, $logic = 'or') use ($field) {
            $keyword = self::clearKeyword($keyword);
            if (strtolower($logic) == 'or') {
                $q->orWhere(static::prepareField($field), static::getLikeOperator(), '%' . $keyword);
            } else {
                $q->where(static::prepareField($field), static::getLikeOperator(), '%' . $keyword);
            }
        };
    }

    /**
     * @param $field
     * @return callable
     */
    public static function textRight($field)
    {
        return function (&$q, $keyword, $logic = 'or') use ($field) {
            $keyword = self::clearKeyword($keyword);
            if (strtolower($logic) == 'or') {
                $q->orWhere(static::prepareField($field), static::getLikeOperator(), $keyword . '%');
            } else {
                $q->where(static::prepareField($field), static::getLikeOperator(), $keyword . '%');
            }

        };
    }

    /**
     * @param $field
     * @param string $operator
     * @return callable
     */
    public static function int($field, $operator = '=')
    {
        return function (&$q, $keyword, $logic = 'or') use ($field, $operator) {
            $keyword = static::clearKeyword($keyword);
            if (is_numeric($keyword)) {
                if (strtolower($logic) == 'or') {
                    $q->orWhere(static::prepareField($field), $operator, $keyword);
                } else {
                    $q->where(static::prepareField($field), $operator, $keyword);
                }
            } elseif (is_array($keyword)) {
                if (strtolower($logic) == 'or') {
                    $q->orWhereIn(static::prepareField($field), array_values($keyword));
                } else {
                    $q->whereIn(static::prepareField($field), array_values($keyword));
                }
            }
        };
    }

    /**
     * @param $field
     * @param string $operator
     * @return callable
     */
    public static function date($field, $operator = '=')
    {
        return function (&$q, $keyword, $logic = 'or') use ($field, $operator) {
            $keyword = self::clearKeyword($keyword);
            if (isDate($keyword) && !is_numeric($keyword)) {
                if (strtolower($logic) == 'or') {
                    $q->orWhere(self::prepareField($field), $operator, $keyword);
                } else {
                    $q->where(self::prepareField($field), $operator, $keyword);
                }
            }
        };
    }
}