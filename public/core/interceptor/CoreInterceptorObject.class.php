<?php

/**
 * Core interceptors object
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreInterceptorObject {

    private $object;
    private $method;
    private $type;
    private $interceptClass;
    private $interceptMethod;

    const INTERCEPTOR_TYPE_AFTER = 'after';
    const INTERCEPTOR_TYPE_BEFORE = 'before';

    /**
     * Construct a interceptor object
     *
     * @param string $interceptClass to intercept
     * @param string $interceptMethod Class method to intercept
     * @param string $object Object which will do interception
     * @param string $method Object method which will do interception
     * @param string $type Type of interception
     */
    function __construct($interceptClass, $interceptMethod, $object, $method, $type){
        $this->interceptClass = $interceptClass;
        $this->interceptMethod = $interceptMethod;
        $this->object = $object;
        $this->method = $method;
        if($type != self::INTERCEPTOR_TYPE_AFTER && $type != self::INTERCEPTOR_TYPE_BEFORE){
            CoreLog::error('Unable to build interceptor with invalid type: ' . $type);
        }
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getInterceptClass()
    {
        return $this->interceptClass;
    }

    /**
     * @return mixed
     */
    public function getInterceptMethod()
    {
        return $this->interceptMethod;
    }

}