<?php

/**
 * Core Object Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreObjectUtils {

    const OBJECT_APPEND = 'Object';
    const OBJECT_SETTER_PREPEND = 'set';
    const OBJECT_SETTER_PREPEND_BOOLEAN = 'is';

    /**
     * Merge objects
     *
     * @param null $a
     * @param null $b
     * @return object
     */
    public static function mergeObjects($a = null, $b = null){
        if(is_string($a)) $a = CoreLogic::getObject($a);
        if(is_string($b)) $b = CoreLogic::getObject($b);
        return (object) array_merge((array) $a, (array) $b);
    }

    /**
     * Apply row to object
     *
     * @param mixed $object
     * @param array $row
     * @return object
     */
    public static function applyRow($object = null, $row = array()){
        if(is_string($object)) $object = CoreLogic::getObject($object);
        if(substr(get_class($object), -6) != self::OBJECT_APPEND) CoreLog::error('First argument needs to be an object reference ending in Object');
        if(is_object($row)) $row = (array) $row;
        if(!is_array($row)) return $object;
        uksort($row, function($a, $b){
            if (strlen($a) == strlen($b)) return 0;
            if (strlen($a) > strlen($b)) return -1;
            return 1;
        });
        $objectKeys = array_keys((array) $object);
        usort($objectKeys, function($a, $b){
            if (strlen($a) == strlen($b)) return 0;
            if (strlen($a) > strlen($b)) return -1;
            return 1;
        });
        $mapped = array();
        foreach ($row as $row_key => $row_value) {
            if(strpos($row_key, CoreApi::API_HAVE_PREFIX) === 0) continue; //read only
            foreach($objectKeys as $key) {
                if(in_array($key, $mapped)) continue;
                if (strtolower(substr(CoreStringUtils::underscoresToCamelCase($row_key), -strlen($key))) == strtolower($key)) {
                    if(method_exists($object, self::OBJECT_SETTER_PREPEND . ucfirst($key))){
                        if(method_exists($object, self::OBJECT_SETTER_PREPEND_BOOLEAN . ucfirst($key))){
                            $row_value = CoreStringUtils::evaluateBoolean($row_value);
                        }
                        call_user_func(array($object, self::OBJECT_SETTER_PREPEND . ucfirst($key)), $row_value);
                    } else {
                        $object->$key = $row_value;
                    }
                    array_push($mapped, $key);
                    break;
                }
            }
        }
        return $object;
    }

}