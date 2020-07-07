<?php

/**
 * Core interceptors
 * This objects introduced the interceptor pattern
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreInterceptor {

    const CACHE_INTERCEPTORS_NS = 'interceptors';
    const CACHE_INTERCEPTORS_KEY = 'interceptors';

	/**
	 * Interceptor stack
     * @var array $interceptors
	 */
	private static $interceptors = array();

	/**
	 * Set interceptor object
	 *
	 * @param CoreInterceptorObject $coreInterceptorObject
	 */
	public static function set(CoreInterceptorObject $coreInterceptorObject){
		if(!isset(self::$interceptors[$coreInterceptorObject->getInterceptClass()][$coreInterceptorObject->getInterceptMethod()])){
			self::$interceptors[$coreInterceptorObject->getInterceptClass()][$coreInterceptorObject->getInterceptMethod()] = array();
		}
		array_push(self::$interceptors[$coreInterceptorObject->getInterceptClass()][$coreInterceptorObject->getInterceptMethod()], $coreInterceptorObject);
	}

	/**
	 * Subscribe interceptor
     *
	 * @param String $class_name Intercepting this class 
	 * @param String $class_method Intercepting this method
	 * @param String $interceptor Interceptor class
	 * @param String $method Method name
	 * @param String $type before or after
	 * @return void
	 */
	public static function subscribe($class_name, $class_method, $interceptor, $method, $type = CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER){

		/**
		 * Build the interceptor
		 */
        $CoreInterceptorObject = new CoreInterceptorObject($class_name, $class_method, $interceptor, $method, $type);

		/**
		 * Stack up the interceptor
		 */		
		self::set($CoreInterceptorObject);

	}

	/**
	 * Process interceptors for a class method
	 *
	 * @param null $className
	 * @param null $method
	 * @param null $remoteObject
	 * @param null $remoteMethod
	 * @param array $params
	 * @return mixed
	 */
	public static function process($className = null, $method = null, $remoteObject = null, $remoteMethod = null, $params = array()){

		/**
		 * Find applicable interceptors in stack
		 */
		if(CoreInit::$reflection === false && isset(self::$interceptors[$className][$method]) && !empty(self::$interceptors[$className][$method])) {

			/** @var CoreInterceptorObject $CoreInterceptorObject */
			foreach (self::$interceptors[$className][$method] as $CoreInterceptorObject) {
				if($CoreInterceptorObject->getType() != CoreInterceptorObject::INTERCEPTOR_TYPE_BEFORE) continue;
				if (method_exists($CoreInterceptorObject->getObject(), $CoreInterceptorObject->getMethod())) {

					$o = $CoreInterceptorObject->getObject();
					$m = $CoreInterceptorObject->getMethod();

					/**
					 * Run the subscribed before interceptor
					 */
					$params = $o::$m($params, $remoteObject);

				} else {
					CoreLog::error('Incorrectly registered interceptor ' . $CoreInterceptorObject->getObject() . '::' . $CoreInterceptorObject->getMethod());
				}
			}

			/**
			 * Run method
			 */
			$return = call_user_func_array(array($remoteObject, $remoteMethod), $params);

			/** @var CoreInterceptorObject $CoreInterceptorObject */
			foreach (self::$interceptors[$className][$method] as $CoreInterceptorObject) {
				if($CoreInterceptorObject->getType() != CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER) continue;
				if (method_exists($CoreInterceptorObject->getObject(), $CoreInterceptorObject->getMethod())) {

					$o = $CoreInterceptorObject->getObject();
					$m = $CoreInterceptorObject->getMethod();

					/**
					 * Run the subscribed after interceptor
					 */
					$return = $o::$m($return, $params, $remoteObject);


				} else {
					CoreLog::error('Incorrectly registered interceptor ' . $CoreInterceptorObject->getObject() . '::' . $CoreInterceptorObject->getMethod());
				}
			}

			return $return;

		}

		return call_user_func_array(array($remoteObject, $remoteMethod), $params);
		
	}

    /**
     * Register interceptors
     *
     */
    public static function registerInterceptors(){

        self::$interceptors = CoreCache::getCache(static::CACHE_INTERCEPTORS_KEY, true, array(self::CACHE_INTERCEPTORS_NS), false);

        if(!empty(self::$interceptors)) return;

        /** @var CoreModuleObject $coreModuleObject */
        foreach(CoreModule::$modules as $coreModuleObject){
            $interceptors = $coreModuleObject->getInterceptors();
			/** @var CoreTemplateObject $coreTemplateObject */
            if(!empty($interceptors)){
                /** @var CoreInterceptorObject $coreInterceptorObject */
                foreach($interceptors as $coreInterceptorObject) {
                    self::set($coreInterceptorObject);
                }
            }
        }

        CoreCache::saveCache(static::CACHE_INTERCEPTORS_KEY, self::$interceptors, 86400, true, array(self::CACHE_INTERCEPTORS_NS), false);

    }
	
}