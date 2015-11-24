<?php

if (!function_exists('classDotNotation')) {
    /**
     * Creates from object class name readable doted separated single string
     * (it is used to declare record related events)
     * @param object $obj
     * @return string
     */
    function classDotNotation( $obj)
    {
        return strtolower(str_replace('\\', '.', get_class($obj)));
    }
}



if (!function_exists('isJson')) {
    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}


