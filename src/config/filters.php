<?php

use Opis\Closure\SerializableClosure;

return array(
    'display' => array(
        'bool' => serialize(new SerializableClosure(
                function ($value, $field, $params = array(), $record=null) {
                    if ($value && (int)$value == 1) {
                        return _('Yes');
                    }
                    return _('No');
                }
            )
        ),
        'date' => serialize(new SerializableClosure(
                function ($value, $field, $params = array('Y-m-d'), $record=null) {
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
                }
            )
        ),
        'hour' => serialize(new SerializableClosure(
                function ($value, $field, $params = array(), $record=null) {
                    return substr($value, 0, 5);
                }
            )
        ),
        'isActive' => serialize(new SerializableClosure(
                function ($value, $field, $params = array(), $record=null) {
                    if ((int)$value == 1) {
                        return _('active');
                    }
                    return _('inactive');
                }
            )
        ),
        'price' => serialize(new SerializableClosure(
                function ($value, $field, $params = array('locale'=>'pl-PL'), $record=null) {
                    $formatter = new \NumberFormatter($params['locale'], \NumberFormatter::CURRENCY);
                    $value = $formatter->format($value);
                    if (empty($value)) {
                        $value = 0;
                    }
                    return $value;
                }
            )
        ),
        'dot2comma' => serialize(new SerializableClosure(
                function($value, $field, $params=array(), $record=null){
                    return str_replace('.', ',', $value);
                }
            )
        ),
        'precision' => serialize(new SerializableClosure(
                function($value, $field, $params=array(2), $record=null){
                    return number_format($value , $params[0]);
                }
            )
        ),
        'rounded' => serialize(new SerializableClosure(
                function($value, $field, $params=array(), $record=null){
                    return round($value);
                }
            )
        ),
        'translate' => serialize(new SerializableClosure(
                function($value, $field, $params=array(), $record=null){
                    if (!empty($value)) {
                        return _($value);
                    }
                    return $value;
                }
            )
        ),
        'truncate' => serialize(new SerializableClosure(
                function ($value, $field, $params = array(), $record=null) {
                    $limit = isSet($params[0]) ? $params[0] : 100;
                    $start =  isSet($params[1]) ? $params[1] : 0;
                    $pad = isSet($params[2]) ? $params[2] : "...";

                    #return with no change if string is shorter than $limit
                    if(strlen($value) <= $limit){
                        return $value;
                    }

                    return substr($value, $start, $limit) . $pad;
                }
            )
        ),
        'upper' => serialize(new SerializableClosure(
                function($value, $field, $params=array(), $record=null){
                    return strtoupper($value);
                }
            )
        ),
    ),
    'save' => array(
        /*
         * file upload for base64 encoded files data
         * example (annonymous function returns file name - with out extension, just name)
         *  'logo' => array(
                'title' => _('Logo'),
                'type'=> static::TYPE_IMAGE,
                'filters' => array(
                    'save' => array(
                        'base64File:'.call_user_func(function(){
                            return '777';
                        })
                    ),
                    'fill' => array(
                        //'jsonDecode'
                    )
                )
            ),
         * or (file name will be random based on timestamp)
         * 'logo' => array(
                'title' => _('Logo'),
                'type'=> static::TYPE_IMAGE,
                'filters' => array(
                    'save' => array(
                        'base64File'
                    ),
                    'fill' => array(
                        //'jsonDecode'
                    )
                )
            ),
         */
        'base64File' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    if (is_array($value) && array_key_exists('name', $value) && array_key_exists('content', $value)){
                        $encodedValue = base64_decode(preg_replace('#^data:[^;]+;base64,#', '', $value['content']));

                        #detecting is we have bse64 encoded data
                        if ($encodedValue){
                            #file infos
                            $ext = pathinfo($value['name'], PATHINFO_EXTENSION);

                            if(count($params) == 0){
                                $params[] = time();
                            }

                            #put file into storage
                            $fileName = $params[0].'.'.$ext;
                            \Storage::put($fileName,$encodedValue);
                            return $fileName;
                        }

                        return $value['content'];
                    }

                    return $value;
                }
            )
        ),
        'dot2comma' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    return str_replace(',', '.', $value);
                }
            )
        ),
        'emptyToNull' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    $value = trim($value);
                    if (empty($value)) {
                        $value = null;
                    }
                    return $value;
                }
            )
        ),
        'firstToUpper' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    return ucfirst($value);
                }
            )
        ),
        'jsonEncode' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    return json_encode($value);
                }
            )
        ),
        'phone' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    return str_replace(array(')', '(', ' ', '-'), '', $value);
                }
            )
        ),
        'strReplace' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    if (!isset($params[0]) || !$params[1]){
                        return $value;
                    }
                    return str_replace($params[0],$params[1], $value);
                }
            )
        ),
        'trim' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    return trim($value);
                }
            )
        ),
    ),
    'fill' => array(
        'dot2comma' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    return str_replace(',', '.', $value);
                }
            )
        ),
        'emptyToNull' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    $value = trim($value);
                    if (empty($value)) {
                        $value = null;
                    }
                    return $value;
                }
            )
        ),
        'emptyToFalse' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    if (empty($value)) {
                        $value = false;
                    }
                    return $value;
                }
            )
        ),
        'emptyToZero' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    if (empty($value)) {
                        $value = 0;
                    }
                    return $value;
                }
            )
        ),
        'hour' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    if (is_int($value)) {
                        return date('H:i:s', ($value - 1) * 60 * 60);
                    }
                    return $value;
                }
            )
        ),
        'jsonDecode' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    return json_decode($value, true);
                }
            )
        ),
        'trim' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    return trim($value);
                }
            )
        ),
        'phone' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    return str_replace(array(')', '(', ' ', '-'), '', $value);
                }
            )
        ),
        'stripTags' => serialize(new SerializableClosure(
                function ($value, $params = array()) {
                    if (is_object($value)){
                        return $value;
                    }

                    $allowed = isSet($params[0]) ? $params[0] : implode('', $params);

                    return strip_tags($value, $allowed);
                }
            )
        ),

    )
);