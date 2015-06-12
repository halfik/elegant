<?php namespace Netinteractive\Elegant\Search\Db;

use Netinteractive\Elegant\Search\Searchable;
use Netinteractive\Elegant\Search\TranslatorInterface;

/**
 * Class Translator
 * @package Netinteractive\Elegant\Search\Db
 */
class Translator implements TranslatorInterface
{
    public static $alias;
    public static $like = array(
        'pgsql' => 'iLIKE'
    );

    /**
     * @param $field
     * @param $type
     * @return callable
     */
    public function translate($field, $type)
    {
        switch (strtolower($type)) {
            case Searchable::$contains:
                $search = $this->text($field);
                break;
            case Searchable::$begins:
                $search = $this->textLeft($field);
                break;
            case Searchable::$ends:
                $search = $this->textRight($field);
                break;
            default:
                $search = $this->standard($field, $type);
                break;
        }

        return $search;
    }

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
     * @return callable
     */
    public static function text($field)
    {
         return function (&$q, $keyword, $logic = 'or') use ($field) {
            $keyword = self::clearKeyword($keyword);
            $keyword = '%' . $keyword . '%';

            if (strtolower($logic) == 'or') {
                $q->orWhere(static::prepareField($field), static::getLikeOperator(), $keyword);
            } else {
                $q->where(static::prepareField($field), static::getLikeOperator(), $keyword);
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
    public static function standard($field, $operator = '=')
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
}