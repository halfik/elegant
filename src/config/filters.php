<?php

$serializer = new SuperClosure\Serializer;

return array(
    'display' => array(
        'bool' => $serializer->serialize(function ($value, $params = array()) {
                if ($value && (int)$value == 1) {
                    return _('Yes');
                }
                return _('No');
            }),
        'per100' => $serializer->serialize(function($value, $field, $params=array(100)){
                return $value.' / '.$params[0];
            }),
        'date' => $serializer->serialize(function ($value, $params = array('Y-m-d')) {
                if ($value instanceof \Carbon\Carbon) {
                    $value->setToStringFormat($params[0]);
                    return $value->__toString();
                } else {
                    if (is_numeric($value)) {
                        return date($params[0], $value);
                    }

                    $dateObj = new DateTime($value);
                    return $dateObj->format($params[0]);
                }
            }),
        'hour' => $serializer->serialize(function ($value, $params = array()) {
                return substr($value, 0, 5);
            }),
        'is_active' => $serializer->serialize(function ($value, $params = array()) {
                if ((int)$value == 1) {
                    return _('active');
                }
                return _('inactive');
            }),
        'price' => $serializer->serialize(function ($value, $params = array()) {
                $formatter = new \NumberFormatter("pl-PL", \NumberFormatter::CURRENCY);
                $value = $formatter->format($value);
                if (empty($value)) {
                    $value = 0;
                }
                return $value;
            }),
        'pl_decimal' => $serializer->serialize(function($value, $field, $params=array()){
                return str_replace('.', ',', $value);
            }),
        'precision' => $serializer->serialize(function($value, $field, $params=array(2)){
                return number_format($value , $params[0]);
            }),
        'rounded' => $serializer->serialize(function($value, $field, $params=array()){
                return round($value);
            }),
        'translate' => $serializer->serialize(function ($value, $params = array()) {
                if (!empty($value)) {
                    return _($value);
                }
                return $value;
            }),
        'truncate' => $serializer->serialize(function ($value, $params = array()) {
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
                        $value = substr($value, 0, $breakpoint) . $pad;
                    }
                }

                return $value;
            }),
        'upper' => $serializer->serialize(function ($value, $params = array()) {
                return strtoupper($value);
            }),
    ),
    'save' => array(
        'emptyToNull' => $serializer->serialize(function ($value, $params = array()) {
                $value = trim($value);
                if (empty($value)) {
                    $value = null;
                }
                return $value;
            }),
        'firstToUpper' => $serializer->serialize(function ($value, $params = array()) {
                return ucfirst($value);
            }),
        'phone' => $serializer->serialize(function ($value, $params = array()) {
                return str_replace(array(')', '(', ' ', '-'), '', $value);
            }),
        'price' =>$serializer->serialize( function ($value, $params = array()) {
                return str_replace(',', '.', $value);
            }),
        'trim' => $serializer->serialize(function ($value, $params = array()) {
                return trim($value);
            }),
    ),
    'fill' => array(
        'emptyToNull' => $serializer->serialize(function ($value, $params = array()) {
                $value = trim($value);
                if (empty($value)) {
                    $value = null;
                }
                return $value;
            }),
        'trim' => $serializer->serialize(function ($value, $params = array()) {
                return trim($value);
            }),
        'price' => $serializer->serialize(function ($value, $params = array()) {
                return str_replace(',', '.', $value);
            }),
        'phone' => $serializer->serialize(function ($value, $params = array()) {
                return str_replace(array(')', '(', ' ', '-'), '', $value);
            }),
        'stripTags' => $serializer->serialize(function ($value, $params = array()) {
                $allowed = isSet($params['allowed']) ? $params['allowed'] : implode('', $params);
                return strip_tags($value, $allowed);
            }),
        'hour' =>$serializer->serialize( function ($value, $params = array()) {
                if (is_int($value)) {
                    return date('H:i:s', ($value - 1) * 60 * 60);
                }
                return $value;
            }),
    )
);