<?php

if (! function_exists('classDotNotation')) {
    /**
     * @param object $obj
     * @return string
     */
    function app_path( $obj)
    {
        return strtolower(str_replace('\\', '.', get_class($obj)));
    }
}else{
    trigger_error('unction classDotNotation already exists');
}
