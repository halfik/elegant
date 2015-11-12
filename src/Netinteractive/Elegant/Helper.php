<?php namespace Netinteractive\Elegant;

/**
 * Class Helper
 * @package Netinteractive\Elegant
 */
class Helper
{
    /**
     * @param object $obj
     * @return string
     */
    public static function classDotNotation( $obj)
    {
        return strtolower(str_replace('\\', '.', get_class($obj)));
    }
}