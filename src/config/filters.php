<?php

return array(
    'display' => array(
        'bool' => function ($value, $params = array()) {
                if ($value && (int)$value == 1) {
                    return _('Yes');
                }
                return _('No');
            },
        'per100'=>function($value, $field, $params=array(100)){
                return $value.' / '.$params[0];
            },
        'day' => function ($value, $params = array()) {
                return $value . ' ' . _('dniach');
            },
        'date' => function ($value, $params = array('Y-m-d')) {
                if ($value instanceof Carbon\Carbon) {
                    $value->setToStringFormat($params[0]);
                    return $value->__toString();
                } else {
                    if (is_numeric($value)) {
                        return date($params[0], $value);
                    }

                    $dateObj = new DateTime($value);
                    return $dateObj->format($params[0]);
                }
            },
        'hour' => function ($value, $params = array()) {
                return substr($value, 0, 5);
            },
        'is_active' => function ($value, $params = array()) {
                if ((int)$value == 1) {
                    return _('active');
                }
                return _('inactive');
            },
        'price' => function ($value, $params = array()) {
                $formatter = new \NumberFormatter("pl-PL", \NumberFormatter::CURRENCY);
                $value = $formatter->format($value);
                if (empty($value)) {
                    $value = 0;
                }
                return $value;
            },
        'pl_decimal' => function($value, $field, $params=array()){
                return str_replace('.', ',', $value);
            },
        'precision' => function($value, $field, $params=array(2)){
                return number_format($value , $params[0]);
            },
        'rounded' => function($value, $field, $params=array()){
                return round($value);
            },
        'translate' => function ($value, $params = array()) {
                if (!empty($value)) {
                    return _($value);
                }
                return $value;
            },
        'truncate' => function ($value, $params = array()) {
                $limit = isSet($params[0]) ? $params[0] : 100;
                $break = isSet($params[1]) ? $params[1] : ".";
                $pad = isSet($params[2]) ? $params[2] : "...";

                #return with no change if string is shorter than $limit
                if(strlen($value) <= $limit){
                    return $value;
                }

                # is $break present between $limit and the end of the string?
                if(false !== ($breakpoint = strpos($value, $break, $limit))) {
                    if($breakpoint < strlen($value) - 1) {
                        $string = substr($value, 0, $breakpoint) . $pad;
                    }
                }

                return $string;
            },
        'upper' => function ($value, $params = array()) {
                return strtoupper($value);
            },
    ),
    'save' => array(
        'emptyToNull' => function ($value, $params = array()) {
                $value = trim($value);
                if (empty($value)) {
                    $value = null;
                }
                return $value;
            },
        'firstToUpper' => function ($value, $params = array()) {
                return ucfirst($value);
            },
        'price' => function ($value, $params = array()) {
                return str_replace(',', '.', $value);
            },
        'trim' => function ($value, $params = array()) {
                return trim($value);
            },
    ),
    'fill' => array(
        'emptyToNull' => function ($value, $params = array()) {
                $value = trim($value);
                if (empty($value)) {
                    $value = null;
                }
                return $value;
            },
        'trim' => function ($value, $params = array()) {
                return trim($value);
            },
        'price' => function ($value, $params = array()) {
                return str_replace(',', '.', $value);
            },
        'phone' => function ($value, $params = array()) {
                return str_replace(array(')', '(', ' ', '-'), '', $value);
            },
        'stripTags' => function ($value, $params = array()) {
                $allowed = isSet($params['allowed']) ? $params['allowed'] : implode('', $params);
                return strip_tags($value, $allowed);
            },
        'bank_account' => function ($value, $params = array()) {
                return str_replace(array('PL', ' '), '', $value);
            },
        'hour' => function ($value, $params = array()) {
                if (is_int($value)) {
                    return date('H:i:s', ($value - 1) * 60 * 60);
                }
                return $value;
            },

    )
);