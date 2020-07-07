<?php

/**
 * Core URL toMethod
 * routes request to module or plugin defined routes
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreController {

    /**
     * Allow interceptors
     */
    use CoreInterceptorTrait;

	/**
	 * Controller stack
	 */
	private static $controllers = array();

    /**
     * Caching
     */
    const CACHE_ROUTES_NS = 'controller';
    const CACHE_ROUTES_KEY = 'controller';

    /**
     * Misc constants
     */
    const PATH_Q = '?';
    const SERVER_QUERY_STRING = 'QUERY_STRING';
    const SERVER_REQUEST_URI = 'REQUEST_URI';

    /**
     * Exception handling
     */
    const EXCEPTION_HANDLE_METHOD = 'handle';
    const EXCEPTION_HANDLE_METHOD_ALIAS = '_handle';

    /**
     * Match URL
     * this will be used to pick a route
     *
     * @var mixed
     */
    public static $matchUrl = null;
    public static $requestMethod = null;

    /**
     * The pattern with was found to match
     *
     * @var CoreControllerObject $currentRoute
     */
    public static $currentRoute = '';

	/**
	 * Public toMethod keys
	 */
	const REGEX = 'regex';
	const OBJECT = 'object';
	const METHOD = 'method';

    /**
     * Action class load method
     */
    const ACTION_LOAD_METHOD = 'build';

	/**
	 * Document stack
	 * page elements will stack here
	 */
	public static $document = array();

    /**
     * Reference to current action
     *
     * @var CoreRenderTemplateInterface $currentAction
     */
    public static $currentAction = null;
			
	/**
	 * Events
	 */
	const CONTROLLER_EVENT_HANDLE_BEFORE = 'request:handle:before';
	const CONTROLLER_EVENT_RENDER_BEFORE = 'document:render:before';
	const CONTROLLER_EVENT_RENDER_AFTER = 'document:render:after';
	const CONTROLLER_EVENT_RENDER_NOT_FOUND = 'document:render:not_found';
	const CONTROLLER_EVENT_ACTION_BEFORE_EXECUTE = 'action:execute:before';
    const CONTROLLER_EVENT_ACTION_BEFORE_BUILD = 'action:build:before';

    /**
     * Set core controller object
     *
     * @param CoreControllerObject $coreControllerObject
     */
    public static function set(CoreControllerObject $coreControllerObject){
        array_push(self::$controllers, $coreControllerObject);
    }

	/**
	 * Registers route/path and points to method in class
     *
	 * @param String $match Url match or regex
	 * @param String $className Object which contains method
	 * @param String $method Method name of action method
     * @param string $matchType
     * @param string $group
	 * @return boolean True of false
	 */
	public static function toMethod($match = null, $className = null, $method = null, $matchType = CoreControllerObject::MATCH_TYPE_REGEX, $group = CoreControllerObject::GROUP_API){
	
		/**
         * Core controller object
         */
        $CoreControllerObject = new CoreControllerObject($match, $className, $method, $matchType, $group);
        $CoreControllerObject->setType(CoreControllerObject::TYPE_METHOD);

		/**
		 * Stack the toMethod
		 */
		self::set($CoreControllerObject);

        return true;
	
	}

    /**
     * Registers route or path and points to action class
     *
     * @param string $match Url match or regex
     * @param string $actionClass
     * @param string $matchType
     * @return bool
     */
    public static function toAction($match = null, $actionClass = null, $matchType = CoreControllerObject::MATCH_TYPE_REGEX){

        /**
         * Core controller object
         */
        $CoreControllerObject = new CoreControllerObject($match, $actionClass, CoreController::ACTION_LOAD_METHOD, $matchType, CoreControllerObject::GROUP_PAGE);
        $CoreControllerObject->setType(CoreControllerObject::TYPE_ACTION);

        /**
         * Stack the toAction
         */
        self::set($CoreControllerObject);

        return true;

    }

    /**
     * Route request
     * Note: the _ prepend allows this method to be intercepted
     *
     * @param CoreControllerObject $CoreControllerObject
     * @param array $params
     *
     * @return bool
     */
    public static function _routeRequest(CoreControllerObject $CoreControllerObject, $params = array()){

        /**
         * Pick property routing by type
         */
        switch($CoreControllerObject->getType()){

            /**
             * To static method method in class
             */
            case CoreControllerObject::TYPE_METHOD:

                /**
                 * Prevent unauthorized access
                 */
                if($CoreControllerObject->getGroup() == CoreControllerObject::GROUP_API) {
                    CoreSecurity::checkAccessToken();
                }

                $className = $CoreControllerObject->getObject();
                $methodName = $CoreControllerObject->getMethod();

                /**
                 * Sanity check
                 */
                if(!method_exists($className, $methodName)){
                    CoreLog::error($className . '::' . $methodName . ' does not exist');
                }

                /**
                 * Route to method
                 */
                $className::$methodName($params);

                break;

            /**
             * To action
             */
            case CoreControllerObject::TYPE_ACTION:

                /**
                 * Generate token
                 * this will refresh the access token
                 * for api and stream access
                 */
                CoreSecurity::generateAccessToken();

                /**
                 * Lookup action
                 */
                $className = $CoreControllerObject->getObject();

                /**
                 * @var CoreRenderTemplateInterface self::$currentAction
                 */
                self::$currentAction = CoreLogic::getAction($className, $params);

                /**
                 * Allow interception
                 */
                CoreObserver::dispatch(self::CONTROLLER_EVENT_ACTION_BEFORE_BUILD, self::$currentAction);

                /**
                 * Build action
                 */
                self::$currentAction->build($params);

                /**
                 * Allow interception
                 */
                CoreObserver::dispatch(self::CONTROLLER_EVENT_ACTION_BEFORE_EXECUTE, self::$currentAction);

                /**
                 * Execute action
                 */
                self::$currentAction->execute();

                break;

            /**
             * Unsupported type!
             */
            default:

                CoreLog::error('Unsupported ControllerObject type: ' . $CoreControllerObject->getType());

                break;

        }

        /**
         * Fire listeners after request handling
         */
        CoreObserver::dispatch(self::CONTROLLER_EVENT_RENDER_AFTER, null);

        return true;

    }

    /**
     * Handle exception
     *
     * @param Exception $Exception
     * @return bool
     * @throws Exception
     */
    public static function _handleException(Exception $Exception){

        /**
         * Rethrow exception if we don't have a handle method
         */
        if(!method_exists($Exception, self::EXCEPTION_HANDLE_METHOD_ALIAS) && !method_exists($Exception, self::EXCEPTION_HANDLE_METHOD)){
            throw $Exception;
        }

        /**
         * Handle Exception
         *
         */
        $Exception->handle();

        return true;

    }

    /**
     * Match request uri to controller entry
     *
     * @param $request_uri
     * @param CoreControllerObject $CoreControllerObject
     * @return bool
     */
    public static function match($request_uri, CoreControllerObject $CoreControllerObject){
        $rm = $CoreControllerObject->getRequestMethod();
        if(!empty($rm) && $rm != self::$requestMethod){
            return false;
        }
        return  ($CoreControllerObject->getMatchType() == CoreControllerObject::MATCH_TYPE_REGEX) ? (preg_match($CoreControllerObject->getMatch(), $request_uri) ? true : false) : ($request_uri == $CoreControllerObject->getMatch() ? true : false);
    }

    /**
	 * Handle request
	 * route this request to a matching url
     *
     * Note: the _ prepend allows this method to be intercepted
	 *
     * @param $group
     * @return bool|null
     */
	public static function _handleRequest($group){

        try {

            /**
             * Fire events prior to routing this request
             */
            CoreObserver::dispatch(self::CONTROLLER_EVENT_HANDLE_BEFORE, null);

            /**
             * Set request reference
             */
            self::$matchUrl = str_replace(self::PATH_Q . $_SERVER[self::SERVER_QUERY_STRING], null, $_SERVER[self::SERVER_REQUEST_URI]);
            self::$requestMethod = $_SERVER['REQUEST_METHOD'];

            /**
             * Check request method
             */
            if(empty(self::$requestMethod)){
                CoreLog::error('No request method set!');
            }
            if(
                self::$requestMethod == CoreControllerObject::REQUEST_CONNECT ||
                self::$requestMethod == CoreControllerObject::REQUEST_TRACE ||
                self::$requestMethod == CoreControllerObject::REQUEST_OPTIONS ||
                self::$requestMethod == CoreControllerObject::REQUEST_HEAD
            ){
                CoreRender::$noBody = true;
            }

            /**
             * Check the controllers
             * @var CoreControllerObject $CoreControllerObject
             */
            foreach(self::$controllers as $CoreControllerObject){

                /** Speed things up */
                if($CoreControllerObject->getGroup() != $group) continue;

                /**
                 * Does not match request method
                 */
                $rm = $CoreControllerObject->getRequestMethod();
                if(!empty($rm) && $rm != self::$requestMethod) continue;

                /**
                 * Seed params
                 * reference object
                 */
                $params = array(self::$matchUrl);

                /**
                 * Handle controller
                 */
                switch(($CoreControllerObject->getMatchType() == CoreControllerObject::MATCH_TYPE_REGEX) ? preg_match($CoreControllerObject->getMatch(), self::$matchUrl, $params) : (self::$matchUrl == $CoreControllerObject->getMatch() ? 1 : 0)){

                    /**
                     * Match found
                     * route request to this toMethod
                     */
                    case (1):

                        //dispatch listeners before rendering
                        CoreObserver::dispatch(self::CONTROLLER_EVENT_RENDER_BEFORE, null);

                        //set found pattern
                        self::$currentRoute = $CoreControllerObject;

                        /**
                         * Route request
                         *
                         */
                        return self::routeRequest($CoreControllerObject, $params);

                    break;

                    /**
                     * No match found
                     * normal behavior
                     */
                    case (0):
                        //no match found
                    break;

                    /**
                     * Invalid regex passed
                     * report this error
                     */
                    case (false):

                        CoreLog::error('Invalid regex pattern. Route: ' . serialize($CoreControllerObject));

                        return false;

                    break;

                }
            }

            /** Set page not found */
            if($group == $CoreControllerObject::GROUP_API){
                CoreApi::$status = 404;
                CoreNotification::set(self::$matchUrl . ' not found', CoreNotification::ERROR);
            }

            //dispatch listeners before adding error
            CoreObserver::dispatch(self::CONTROLLER_EVENT_RENDER_NOT_FOUND, __CLASS__);

        } catch (Exception $e){

            /**
             * Handle exception
             * allow for interception
             */
            self::handleException($e);

        }
	    return null;
	}

    /**
     * @return array
     */
    public static function getControllers()
    {
        return self::$controllers;
    }

    /**
     * @param mixed $controllers
     */
    public static function setControllers($controllers)
    {
        self::$controllers = $controllers;
    }

    /**
     * Register routes
     *
     */
    public static function registerRoutes(){
        self::$controllers = CoreCache::getCache(static::CACHE_ROUTES_KEY, true, array(self::CACHE_ROUTES_NS), false);
        if(!empty(self::$controllers)) return;
        self::$controllers = array();
        /** @var CoreModuleObject $coreModuleObject */
        foreach(CoreModule::$modules as $coreModuleObject){
            $routes = $coreModuleObject->getRoutes();
            if(!empty($routes)){
                /** @var CoreControllerObject $coreControllerObject */
                foreach($routes as $coreControllerObject) {
                    self::set($coreControllerObject);
                }
            }
        }
        CoreCache::saveCache(static::CACHE_ROUTES_KEY, self::$controllers, 86400, true, array(self::CACHE_ROUTES_NS), false);
    }

}