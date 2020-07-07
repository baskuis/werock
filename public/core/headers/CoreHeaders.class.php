<?php

/**
 * Core Headers
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreHeaders {

    const Location = 'Location';

    /**
     * @var array
     */
    private static $headers = array();

    /**
     * Add header
     *
     * @param null $key
     * @param null $value
     */
    public static function add($key = null, $value = null){
        self::$headers[$key] = $value;
    }

    /**
     * Set all headers
     *
     * @param array $headers
     */
    public static function setAll($headers = array()){
        self::$headers = $headers;
    }

    /**
     * Get all headers
     *
     * @return array
     */
    public static function getAll(){
        return self::$headers;
    }

    /**
     * Set redirect
     * will override existing redirect
     *
     * @param string $path
     */
    public static function setRedirect($path = null){
        self::$headers[self::Location] = $path;
    }

    /**
     * Set permanent redirect
     *
     * @param null $path
     */
    public static function setPermanentRedirect($path = null){
        header("HTTP/1.1 301 Moved Permanently");
        self::$headers[self::Location] = $path;
    }

    /**
     * Disable redirect
     *
     */
    public static function disableRedirect(){
        if(isset(self::$headers[self::Location])){
            unset(self::$headers[self::Location]);
            header_remove(self::Location);
        }
    }

    /**
     * Set redirect
     * will override existing redirect
     * @param string $path
     */
    public static function setFallbackRedirect($path){
        if(!isset(self::$headers[self::Location])){
            self::$headers[self::Location] = $path;
        }
    }

    /**
     * Need body
     *
     * @return bool
     */
    public static function needBody(){
        foreach(self::$headers as $key => $value){
            switch($key){
                case self::Location:
                    return false;
                break;
            }
        }
        return true;
    }

}