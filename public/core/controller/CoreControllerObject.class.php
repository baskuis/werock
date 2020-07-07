<?php

/**
 * Core Controller (entry) Object
 * This object defines a route
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreControllerObject {

    public $match;
    public $object;
    public $method;
    public $type;
    public $matchType;
    public $group;
    public $requestMethod;

    /**
     * Types
     */
    const TYPE_METHOD = 'method';
    const TYPE_ACTION = 'action';

    /**
     * Groups
     */
    const GROUP_PAGE = 'page';
    const GROUP_API = 'api';
    const GROUP_STREAM = 'stream';

    /**
     * Request Methods
     */
    const REQUEST_GET = 'GET';
    const REQUEST_PUT = 'PUT';
    const REQUEST_POST = 'POST';
    const REQUEST_DELETE = 'DELETE';
    const REQUEST_HEAD = 'HEAD';
    const REQUEST_OPTIONS = 'OPTIONS';
    const REQUEST_TRACE = 'TRACE';
    const REQUEST_CONNECT = 'CONNECT';

    /**
     * Match types
     */
    const MATCH_TYPE_STRING = 'string';
    const MATCH_TYPE_REGEX = 'regex';

    /**
     * Constants
     */
    const CONST_STATIC_SEPARATOR = '::';

    /**
     * Build action route
     * defaults to group page
     * returns instance of CoreControllerObject
     *
     * @param $match
     * @param $actionClass
     * @param string $matchType
     * @param string $requestMethod
     * @return CoreControllerObject
     */
    public static function buildAction($match, $actionClass, $matchType = CoreControllerObject::MATCH_TYPE_STRING, $requestMethod = null)
    {
        $instance = new CoreControllerObject($match, $actionClass, null, $matchType, CoreControllerObject::GROUP_PAGE);
        $instance->setType(CoreControllerObject::TYPE_ACTION);
        $instance->setRequestMethod($requestMethod);
        return $instance;
    }

    /**
     * Build method route
     * defaults to group page
     * returns instance of CoreControllerObject
     *
     * @param $match
     * @param $className
     * @param $methodName
     * @param string $matchType
     * @param string $requestMethod
     * @return CoreControllerObject
     */
    public static function buildMethod($match, $className, $methodName, $matchType = CoreControllerObject::MATCH_TYPE_STRING, $requestMethod = null)
    {
        $instance = new CoreControllerObject($match, $className, $methodName, $matchType, CoreControllerObject::GROUP_PAGE);
        $instance->setType(CoreControllerObject::TYPE_METHOD);
        $instance->setRequestMethod($requestMethod);
        return $instance;
    }

    /**
     * Build method route
     * defaults to group api
     * returns instance of CoreControllerObject
     *
     * @param $match
     * @param $className
     * @param $methodName
     * @param string $matchType
     * @param string $requestMethod
     * @return CoreControllerObject
     */
    public static function buildApi($match, $className, $methodName, $matchType = CoreControllerObject::MATCH_TYPE_STRING, $requestMethod = null)
    {
        $instance = new CoreControllerObject($match, $className, $methodName, $matchType, CoreControllerObject::GROUP_API);
        $instance->setType(CoreControllerObject::TYPE_METHOD);
        $instance->setRequestMethod($requestMethod);
        return $instance;
    }

    /**
     * Build C.R.U.D.L. services
     *
     * @param $match
     * @param $className
     * @param $methodBase [[methodBase]]Create, [[methodBase]]Read, [[methodBase]]Update, [[methodBase]]Delete, [[methodBase]]List
     * @return array
     */
    public static function buildCrudl($match, $className, $methodBase){
        $routes = array();
        $pattern = CoreFilesystemUtils::SLASH . preg_quote($match, CoreFilesystemUtils::SLASH) . '\/([0-9]+)\/?/i';
        array_push($routes, CoreControllerObject::buildApi($match, $className, $methodBase . 'Create', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_POST));
        array_push($routes, CoreControllerObject::buildApi($pattern, $className, $methodBase . 'Read', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::REQUEST_GET));
        array_push($routes, CoreControllerObject::buildApi($pattern, $className, $methodBase . 'Update', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::REQUEST_PUT));
        array_push($routes, CoreControllerObject::buildApi($pattern, $className, $methodBase . 'Delete', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::REQUEST_DELETE));
        array_push($routes, CoreControllerObject::buildApi($match, $className, $methodBase . 'List', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));
        return $routes;
    }

    /**
     * Build stream route
     * defaults to group stream
     * returns instance of CoreControllerObject
     *
     * @param $match
     * @param $className
     * @param $methodName
     * @param string $matchType
     * @param string $requestMethod
     * @return CoreControllerObject
     */
    public static function buildStream($match, $className, $methodName, $matchType = CoreControllerObject::MATCH_TYPE_STRING, $requestMethod = null)
    {
        $instance = new CoreControllerObject($match, $className, $methodName, $matchType, CoreControllerObject::GROUP_STREAM);
        $instance->setType(CoreControllerObject::TYPE_METHOD);
        $instance->setRequestMethod($requestMethod);
        return $instance;
    }

    /**
     * Controller route constructor
     *
     * @param null $match
     * @param null $className
     * @param null $method
     * @param string $matchType
     * @param string $group
     */
    function __construct($match = null, $className = null, $method = null, $matchType = self::MATCH_TYPE_STRING, $group = self::GROUP_PAGE)
    {
        if(!empty($method) && !is_callable($className . self::CONST_STATIC_SEPARATOR . $method)){
            CoreLog::error($className . self::CONST_STATIC_SEPARATOR . $method . ' is not callable. Info: ' . serialize($this));
        }
        if(DEV_MODE && $matchType == self::MATCH_TYPE_REGEX && @preg_match($match, null) === false){
            CoreLog::error($match .  ' is an invalid regular expression. Info: ' . serialize($this));
        }
        self::setMatch($match);
        self::setObject($className);
        self::setMethod($method);
        self::setMatchType($matchType);
        self::setGroup($group);
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $match
     */
    public function setMatch($match)
    {
        $this->match = $match;
    }

    /**
     * @return mixed
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
    public function getMatchType()
    {
        return $this->matchType;
    }

    /**
     * @param mixed $matchType
     */
    public function setMatchType($matchType)
    {
        if($matchType != self::MATCH_TYPE_REGEX && $matchType != self::MATCH_TYPE_STRING){
            CoreLog::error('Unknown match type ' . $matchType . '! Route: ' . serialize($this));
        }
        $this->matchType = $matchType;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        if($group != self::GROUP_API && $group != self::GROUP_PAGE && $group != self::GROUP_STREAM){
            CoreLog::error('Unknown group ' . $group . '! Route: ' . serialize($this));
        }
        $this->group = $group;
    }

    /**
     * @return mixed
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * @param mixed $requestMethod
     */
    public function setRequestMethod($requestMethod)
    {
        if(
            !empty($requestMethod) &&
            $requestMethod != self::REQUEST_GET &&
            $requestMethod != self::REQUEST_POST &&
            $requestMethod != self::REQUEST_PUT &&
            $requestMethod != self::REQUEST_DELETE &&
            $requestMethod != self::REQUEST_OPTIONS
        ){
            CoreLog::error('Unknown request type ' . $requestMethod . '! Route: ' . serialize($this));
        }
        $this->requestMethod = $requestMethod;
    }

}