<?php

return array(
    'display' => array(
        'price' => function($value, $params=array()){
            return str_replace('.', ',', $value);
        },
    ),
    'save' => array(
        'emptyToNull' => function ($value, $params=array()){
            if (empty($value)){
                $value = null;
            }
            return $value;
        },
        'firstToUpper' => function($value, $params=array()){
            return ucfirst($value);
        },
        'price' => function($value, $params=array()){
            return str_replace(',', '.', $value);
        },
     ),
    'fill' => array(
        'emptyToNull' => function ($value, $params=array()){
            if (empty($value)){
                $value = null;
            }
            return $value;
        },
        'trim' => function($value, $params=array()){
            return trim($value);
        },
        'price' => function($value, $params=array()){
            return str_replace(',', '.', $value);
        },
    )
);