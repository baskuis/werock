<?php

/**
 * Core Class Loader
 * Loads classes and wraps method accordingly to support various design patterns
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreClassLoader {

    CONST INTERCEPTOR_WRAPPER_CLASS_APPEND = 'Proxy';

    /**
     * Load a class
     * TODO: Haven't determined final approach atm
     *
     * @param $path
     * @param null $name
     */
    public final static function load($path = nul, $name = null){

        /**
         * Assertions
         */
        if(!$path || !$name) CoreLog::error('Need path and name to load class');
        if(!is_file($path)) CoreLog::error('Could not find class ' . $name . ' at ' . $path);

        /**
         * Load class
         */
        require $path;

        /**
         * Create instance of wrapper
         */
        $InterceptorWrapper = new CoreInterceptorWrapper(new $name());

        /**
         * Create the alias
         */
        $var = class_alias('CoreInterceptorWrapper', $name . self::INTERCEPTOR_WRAPPER_CLASS_APPEND);

    }

}