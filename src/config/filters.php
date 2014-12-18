<?php

return array(
    'display' => array(
        'price' => function($value){
            return str_replace('.', ',', $value);
        },
    ),
    'save' => array(
        'firstToUpper' => function($value){
            return ucfirst($value);
        },
     ),
    'fill' => array(
        'emptyToNull' => function ($value){
            if (empty($value)){
                $value = null;
            }
            return $value;
        },
        'trim' => function($value){
            return trim($value);
        }
    )
);