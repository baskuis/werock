<?php

/**
 * Core interceptor wrapper
 * This object can wrap a class to allow interceptors to be easily applied by naming convention
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
trait CoreInterceptorTrait {

    /**
     * Calling a method
     *
     * @param $name
     * @param $args
     * @return mixed|null
     */
    public function __call($name, $args){
        if(method_exists($this, INTERCEPTOR_METHOD_PREPEND . $name)){
            return CoreInterceptor::process(get_class($this), $name, $this, INTERCEPTOR_METHOD_PREPEND . $name, $args);
        }
        if(method_exists($this, $name)){
            return call_user_func_array(array($this, $name), $args);
        }
        CoreLog::error('Method ' . $name . ' not found on ' . get_class($this));
        return false;
    }

    /**
     * Calling a static method
     *
     * @param $name
     * @param $args
     * @return mixed|null
     */
    public static function __callStatic($name, $args){
        if(method_exists(get_class(), INTERCEPTOR_METHOD_PREPEND . $name)){
            return CoreInterceptor::process(get_class(), $name, get_class(), INTERCEPTOR_METHOD_PREPEND . $name, $args);
        }
        if(method_exists(get_class(), $name)){
            return call_user_func_array(array(get_class(), $name), $args);
        }
        CoreLog::error('Static method ' . $name . ' not found on ' . get_class());
        return false;
    }

}