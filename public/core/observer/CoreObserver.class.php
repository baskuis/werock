<?php

/**
 * This static object will keep track of event subscriptions
 * and will fire off events/listeners when the event is dispatched
 * 
 * @author WeRock (bkuis)
 */
class CoreObserver {

	/**
	 * Cache
	 */
    const CACHE_OBSERVER_NS = 'listeners';
    const CACHE_OBSERVER_KEY = 'listeners';
	const CACHE_OBSERVER_DURATION = 86400;

	/**
	 * Events stack
	 */
	private static $events = array();

	/**
	 * Set observer
	 *
	 * @param CoreObserverObject $coreObserverObject
	 */
	public static function set(CoreObserverObject $coreObserverObject){
		if(!isset(self::$events[$coreObserverObject->getEvent()])){
			self::$events[$coreObserverObject->getEvent()] = array();
		}
		array_push(self::$events[$coreObserverObject->getEvent()], $coreObserverObject);
	}

	/**
	 * Subscribe to event
	 *
	 * @param String $event The event to subscribe to
	 * @param Object $object The object which is subscribing to the event
	 * @param String $method The method name that will be called upon this event
	 * @return null
	 */
	public static function subscribe($event = null, $object = null, $method = null){
		$coreObserverObject = new CoreObserverObject($event, $object, $method);
		self::set($coreObserverObject);
	}
	
	/**
	 * Dispatch events subscribed
	 *
	 * @param String $event event name
	 * @param Object $object object where the event was encountered ie: User
	 * @return null
	 */
	public static function dispatch($event = null, $object = null){

		/**
		 * Cut short if no events are found
		 */
		if(CoreInit::$reflection !== false || !isset(self::$events[$event]) || empty(self::$events[$event])){
			return;
		}
				
		/** @var CoreObserverObject $coreObserverObject */
		foreach(self::$events[$event] as $coreObserverObject){
			
			//could not find method on subscribed event
			if(method_exists($coreObserverObject->getObject(), $coreObserverObject->getMethod())){
				
				/**
				 * Fire registered method within 
				 * the subscribed object upon this event
				 */
				$o = $coreObserverObject->getObject();
				$m = $coreObserverObject->getMethod();
				$o::$m($object);
				
			}
			
		}
		
	}

	/**
	 * Register listeners
	 *
	 */
    public static function registerListeners(){
        self::$events = CoreCache::getCache(static::CACHE_OBSERVER_KEY, true, array(self::CACHE_OBSERVER_NS), false);
        if(!empty(self::$events)) return;
        self::$events = array();
        /** @var CoreModuleObject $coreModuleObject */
        foreach(CoreModule::$modules as $coreModuleObject){
            $interceptors = $coreModuleObject->getObservers();
            /** @var CoreTemplateObject $coreTemplateObject */
            if(!empty($interceptors)){
                /** @var CoreObserverObject $coreObserverObject */
                foreach($interceptors as $coreObserverObject) {
                    self::set($coreObserverObject);
                }
            }
        }
        CoreCache::saveCache(static::CACHE_OBSERVER_KEY, self::$events, self::CACHE_OBSERVER_DURATION, true, array(self::CACHE_OBSERVER_NS), false);
    }
	
}