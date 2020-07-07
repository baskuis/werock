<?php

/**
 * Core Array Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreArrayUtils {

    /**
     * Resolve parents
     * adds element [_level] which indicates the depth
     *
     * @param array $array
     * @param null $primary_key
     * @param null $parent_key
     * @return array
     */
    public static function resolveParents($array = array(), $primary_key = null, $parent_key = null){
        if(empty($parent_key)){ CoreLog::error('Please pass the parent key'); }
        return self::stepThroughParentArray($array, 0, 0, $primary_key, $parent_key);
    }

    /**
     * Step through parent array helper
     *
     * @param array $array
     * @param int $parent_id
     * @param int $level
     * @param $parent_key
     * @return array
     */
    private static function stepThroughParentArray($array = array(), $parent_id = 0, $level = 0, $primary_key = null, $parent_key = null){
        $level++;
        $return = array();
        if(!empty($array)){
            foreach($array as $array_key => $array_value){
                if($array_value[$parent_key] == $parent_id){
                    $return[$array_key] = $array_value;
                    $return[$array_key]['_level'] = $level;
                    $inner_array = self::stepThroughParentArray($array, $array_value[$primary_key], $level, $primary_key, $parent_key);
                    $return = $return + $inner_array;
                }
            }
        }
        return $return;
    }

    /**
     * Reconcile arrays
     *
     * @param array $base
     * @param array $add
     * @param array $remove
     * @return array
     */
    private static function reconcileArrays($base = array(), $add = array(), $remove = array()){
        if(!is_array($add)){ CoreLog::error('$add needs to be an array'); }
        if(!is_array($remove)){ CoreLog::error('$remove needs to be an array'); }
        $new = array_merge($base, $add);
        foreach($remove as $rkey => $rvalue){
            if(isset($new[$rkey]) && (empty($rvalue) || $new[$rkey] == $rvalue)){
                unset($new[$rkey]);
            }
        }
        return $new;
    }

    /**
     * Hidden fields
     *
     * @param array $add
     * @param array $remove
     * @return string
     */
    public static function hiddenFields($add = array(), $remove = array()){
        $new = self::reconcileArrays($_GET, $add, $remove);
        $inputString = '';
        foreach($new as $nkey => $nvalue){
            $inputString .= '<input type="hidden" name="' . $nkey . '" value="' . $nvalue . '" />' . "\n";
        }
        return $inputString;
    }

    /**
     * Get String
     *
     * @param array $add
     * @param array $remove
     * @return string _GET string
     */
    public static function getString($add = array(), $remove = array()){
        $new = self::reconcileArrays($_GET, $add, $remove);
        return '?' . http_build_query($new);
    }

    /**
     * Object to array
     *
     * @param $object
     * @return array
     */
    public static function objectToArray($object){
        if(!is_object($object) && !is_array($object)) return $object;
        return array_map('CoreArrayUtils::objectToArray', (array) $object);
    }

    /**
     * Merge arrays
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function mergeArrays($array1 = array(), $array2 = array()){
        if(!$array1 || !is_array($array1)) $array1 = array();
        if(!$array2 || !is_array($array2)) $array2 = array();
        return array_merge($array1, $array2);
    }

    /**
     * Return value as array
     *
     * @param null $value
     * @return array
     */
    public static function asArray($value = null){
        $a = array();
        array_push($a, $value);
        return $a;
    }

}