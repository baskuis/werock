<?php

/**
 * Core Response
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreResponse {

    public static $body;

    /** @var boolean $noBody */
    public static $noBody;

    /**
     * @param mixed $body
     */
    public static function setBody($body)
    {
        CoreResponse::$body = $body;
    }

    /**
     * @return mixed
     */
    public static function getBody()
    {
        if(self::$noBody) return null;
        return CoreResponse::$body;
    }

    /**
     * Output body
     */
    public static function outputData(){
        if(self::$noBody) return;
        echo CoreResponse::$body;
    }

}