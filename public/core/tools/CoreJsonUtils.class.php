<?php

/**
 * Core JSON utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreJsonUtils {

    /**
     * Prepare object
     *
     * @param $object
     * @param $depth
     * @param int $maxDepth
     * @param array $exclusions
     * @return array
     */
    public static function prepare($object, $depth = 1, $maxDepth = 5, $exclusions = array()){
        $return = array();
        if($depth > $maxDepth) return null;
        $depth++;
        foreach($object as $key => $value){
            if(is_numeric($key)) {
                $return[$key] = is_array($value) || is_object($value) ? self::prepare($value, $depth, $maxDepth, $exclusions) : CoreStringUtils::encodeStringToUTF8($value);
            } else {
                if (is_object($value) && ($value instanceof Closure)) {
                    continue;
                }
                if (is_callable($value)) {
                    continue;
                }
                if(in_array($key, $exclusions)){
                    continue;
                }
                $skip = false;
                foreach($exclusions as $exclusion){
                    if($object instanceof $exclusion){
                        $skip = true;
                    }
                }
                if($skip){
                    continue;
                }
                $return[$key] = is_array($value) || is_object($value) ? self::prepare($value, $depth, $maxDepth, $exclusions) : CoreStringUtils::encodeStringToUTF8($value);
                $return[CoreFeTemplate::FE_HAVE_PREFIX . $key] = !empty($value);
            }
        }
        return $return;
    }

    /**
     * Prepare object for export
     *
     * @param $object
     * @param int $maxDepth
     * @param array $exclusions
     * @return array
     */
    public static function prepareObject($object, $maxDepth = 5, $exclusions = array()){
        return self::prepare($object, 1, $maxDepth, $exclusions);
    }

    /**
     * JSON encode
     *
     * @param $object
     * @param int $maxDepth
     * @param array $exclusions
     * @return string
     */
    public static function jsonEncode($object, $maxDepth = 5, $exclusions = array()){
        $prepared = self::prepare($object, 1, $maxDepth, $exclusions);
        return json_encode($prepared);
    }

}