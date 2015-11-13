<?php

if (! function_exists('classDotNotation')) {
    /**
     * @param object $obj
     * @return string
     */
    function classDotNotation( $obj)
    {
        return strtolower(str_replace('\\', '.', get_class($obj)));
    }
}
