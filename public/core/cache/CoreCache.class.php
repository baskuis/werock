<?php

/**
 *
 *  In case of apcu support vs apc - here is some support
 *
    apcu_add — Cache a new variable in the data store
    apcu_cache_info — Retrieves cached information from APCu's data store
    apcu_cas — Updates an old value with a new value
    apcu_clear_cache — Clears the APCu cache
    apcu_dec — Decrease a stored number
    apcu_delete — Removes a stored variable from the cache
    apcu_entry — Atomically fetch or generate a cache entry
    apcu_exists — Checks if entry exists
    apcu_fetch — Fetch a stored variable from the cache
    apcu_inc — Increase a stored number
    apcu_sma_info — Retrieves APCu Shared Memory Allocation information
    apcu_store — Cache a variable in the data store
 */

if(!function_exists('apc_add') && function_exists('apcu_add')){
    function apc_add($key, $var, $ttl){
        return apcu_add($key, $var, $ttl);
    }
}
if(!function_exists('apc_store') && function_exists('apcu_store')){
    function apc_store($key, $var, $ttl){
        return apcu_store($key, $var, $ttl);
    }
}
if(!function_exists('apc_fetch') && function_exists('apcu_fetch')){
    function apc_fetch($key){
        return apcu_fetch($key);
    }
}
if(!function_exists('apc_delete') && function_exists('apcu_delete')){
    function apc_delete($key){
        return apcu_delete($key);
    }
}
if(!function_exists('apc_inc') && function_exists('apcu_inc')){
    function apc_inc($key, $step, &$success){
        return apcu_inc($key, $step, $success);
    }
}

/**
 * Caching operations
 * Provided methods to set, get, update and delete cached object
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreCache {

    /**
     * Cache constants
     */
    CONST CACHE_GLOBAL_NAMESPACE_KEY = 'we:namespace';

	/**
	 * Page render cache stack
	 * allows us to store objects
	 * in memory while the page is
	 * generated
	 */
	private static $cacheStack = array();

    /**
     * Quick reference for namespace values
     * to prevent excessive cache lookups
     *
     * @var array
     */
    private static $namespaces = array();

    /**
     * Constants
     */
    const CACHE_KEY_SEPARATOR = ':';
    const CACHE_KEY_CACHE_SEPARATOR = ':cache:';
    const CACHE_APC_DELETE_FUNCTION = 'apc_delete';
    const CACHE_APC_INC_FUNCTION = 'apc_inc';
    const CACHE_APC_STORE_FUNCTION = 'apc_store';
    const CACHE_MEMCACHE_CLASS_NAME = 'Memcache';
    const CACHE_APC_ITERATOR_CLASS = 'APCIterator';
    const CACHE_NAMESPACE_SEPARATOR = ',';
    const CACHE_APC_CLEAR_CACHE_FUNCTION = 'apc_clear_cache';
    const CACHE_APC_FETCH_FUNCTION = 'apc_fetch';
    const HTTP_MODIFIED_SINCE_HEADER = 'HTTP_IF_MODIFIED_SINCE';
    const HTTP_NOT_MODIFIED_HEADER = 'HTTP/1.1 304 Not Modified';

    /**
     * Type references
     */
    const TYPE_APC = 'apc';
    const TYPE_MEMCACHED = 'memc';

    /**
     * Memcache holder
     *
     * @var Memcache $Memcache
     */
	private static $Memcache = null;

    /**
     * Block cached responses
     *
     *
     */
    public static function blockCacheRequests(){
        if(isset($_SERVER[self::HTTP_MODIFIED_SINCE_HEADER])) {

            /**
             * Handle cached streamed items
             */
            $lastModified = CoreCache::getCache('streamCache:' . str_replace(CoreController::PATH_Q . $_SERVER[CoreController::SERVER_QUERY_STRING], null, $_SERVER[CoreController::SERVER_REQUEST_URI]), true);
            if (!empty($lastModified) && strtotime($_SERVER[self::HTTP_MODIFIED_SINCE_HEADER]) <= $lastModified + 60) {
                header(self::HTTP_NOT_MODIFIED_HEADER);
                exit;
            }
            
        }
    }

    /**
     * Expire Cache
     *
     * @param null $key
     * @param bool $apc Local cache or network wide cache
     * @param string $namespace
     * @return bool|string[]
     */
    public static function deleteCache($key = null, $apc = false, $namespace = null){

        /**
         * Add namespace
         */
        $key = self::generateKey($key, $namespace);

        //no cache system lookup needed
        if(isset(self::$cacheStack[$key])){
            unset(self::$cacheStack[$key]);
        }

        //cache local .. if we can
        if($apc && function_exists(self::CACHE_APC_DELETE_FUNCTION)){

            /**
             * Delete cache by key
             */
            return apc_delete(DOMAIN_NAME . self::CACHE_KEY_CACHE_SEPARATOR . $key);

        }else{

            //check for Memcache
            if(!class_exists(self::CACHE_MEMCACHE_CLASS_NAME)){
                return false;
            }

            //assure memcached
            if(self::assureMemcache()){

                /**
                 * Delete cache key
                 */
                return self::$Memcache->delete(DOMAIN_NAME . self::CACHE_KEY_CACHE_SEPARATOR . $key);

            }

        }

        //something went wrong
        return false;

    }

    /**
     * Create namespace
     *
     * @param mixed $namespace
     * @param bool $apc
     * @return bool
     */
    public static function createNamespace($namespace = null, $apc = true){

        /**
         * To keep types straight
         */
        $typeKey = ($apc) ? self::TYPE_APC : self::TYPE_MEMCACHED;

        /**
         * If is array
         */
        if(is_array($namespace)){

            /**
             * namespace values
             */
            $ns_values = array();

            /**
             * Step through namespaces
             */
            foreach($namespace as $ns){

                /**
                 * Lookup cached value only if needed
                 */
                if(!isset(self::$namespaces[$typeKey][$ns])) {

                    /**
                     * Create namespace key
                     */
                    $key = self::CACHE_GLOBAL_NAMESPACE_KEY . self::CACHE_KEY_SEPARATOR . DOMAIN_NAME . self::CACHE_KEY_SEPARATOR . $ns;

                    $ns_value = 0;

                    /**
                     * If APC
                     */
                    if ($apc && false === ($ns_value = CoreCache::getCache($key, true, null, false))) {
                        CoreCache::saveCache($key, 0, 0, true, null, false);
                        $ns_value = 0;
                    }

                    /**
                     * If Memcache
                     */
                    if (!$apc && false === ($ns_value = CoreCache::getCache($key, false, null, false))) {
                        CoreCache::saveCache($key, 0, 0, false, null, false);
                        $ns_value = 0;
                    }

                    //load value
                    $ns_values[$ns] = $ns_value;

                    //set lookup
                    self::$namespaces[$typeKey][$ns] = $ns_value;

                } else {

                    //restore from existing reference
                    $ns_values[$ns] = self::$namespaces[$typeKey][$ns];

                }

            }

            /**
             * Return string of values
             */
            return implode(self::CACHE_NAMESPACE_SEPARATOR, $ns_values);

        }

        /**
         * Lookup cached value only if needed
         */
        if(!isset(self::$namespaces[$typeKey][$namespace])) {

            /**
             * Create namespace key
             */
            $key = self::CACHE_GLOBAL_NAMESPACE_KEY . self::CACHE_KEY_SEPARATOR . DOMAIN_NAME . self::CACHE_KEY_SEPARATOR . $namespace;

            $ns_value = 0;

            /**
             * If APC
             */
            if ($apc && false === ($ns_value = CoreCache::getCache($key, true))) {
                CoreCache::saveCache($key, 0, 0, true);
                $ns_value = 0;
            }

            /**
             * If Memcache
             */
            if (!$apc && false === ($ns_value = CoreCache::getCache($key, false))) {
                CoreCache::saveCache($key, 0, 0, false);
                $ns_value = 0;
            }

        } else {

            $ns_value = self::$namespaces[$typeKey][$namespace];

        }

        /**
         * All done
         */
        return $ns_value;

    }

    /**
     * Invalidate a namespace
     *
     * @param null $namespace
     * @return bool
     */
    public static function invalidateNamespace($namespace = null){

        /**
         * Kill references
         */
        if(isset(self::$namespaces[self::TYPE_MEMCACHED][$namespace])) unset(self::$namespaces[self::TYPE_MEMCACHED][$namespace]);
        if(isset(self::$namespaces[self::TYPE_APC][$namespace])) unset(self::$namespaces[self::TYPE_APC][$namespace]);

        /**
         * If an array of namespaces are passed
         */
        if(is_array($namespace)){

            /**
             * Step through namespaces
             */
            foreach($namespace as $ns){

                /**
                 * Create namespace key
                 */
                $key = self::CACHE_GLOBAL_NAMESPACE_KEY . self::CACHE_KEY_SEPARATOR . DOMAIN_NAME . self::CACHE_KEY_SEPARATOR . $ns;

                /**
                 * Increment APC
                 */
                CoreCache::increment($key, true);

                /**
                 * Increment Memcache
                 */
                CoreCache::increment($key, false);

            }

            return true;

        }

        /**
         * Create namespace key
         */
        $key = self::CACHE_GLOBAL_NAMESPACE_KEY . self::CACHE_KEY_SEPARATOR . DOMAIN_NAME . self::CACHE_KEY_SEPARATOR . $namespace;

        /**
         * Increment APC
         */
        CoreCache::increment($key, true);

        /**
         * Increment Memcache
         */
        CoreCache::increment($key, false);

        return true;

    }

    /**
     * Increment cached value
     *
     * @param null $key
     * @param bool $apc
     * @return bool
     */
    public static function increment($key = null, $apc = false){

        //if incrementing local cache
        if($apc && function_exists(self::CACHE_APC_INC_FUNCTION)){

            /**
             * Attempt to increment
             */
            $success = false;
            apc_inc(DOMAIN_NAME . self::CACHE_KEY_CACHE_SEPARATOR . $key, 1, $success);

            /**
             * Return success
             */
            return $success;

        }else{

            //check for Memcache
            if(!class_exists(self::CACHE_MEMCACHE_CLASS_NAME)){
                return false;
            }

            //assure memcached
            if(self::assureMemcache()){

                /**
                 * Increment
                 */
                return self::$Memcache->increment(DOMAIN_NAME . self::CACHE_KEY_CACHE_SEPARATOR . $key, 1);

            }

        }

        return false;

    }

    /**
     * Delete all with prepend
     * AVOID USING THIS
     *
     * @param $regex
     * @param bool $apc
     * @return bool
     */
    public static function deleteAllCache($regex = null, $apc = false){

        //cache local .. if we can
        if($apc && function_exists(self::CACHE_APC_DELETE_FUNCTION)){

            /**
             * Check for iterator APCIterator
             */
            if(class_exists(self::CACHE_APC_ITERATOR_CLASS)){

                /**
                 * Iterate over cached objects and
                 * remove
                 */
                foreach(new APCIterator('user', $regex) as $entry){
                    CoreCache::deleteCache($entry['key'], true);
                }

                return true;

            }else{

                CoreLog::error('APCIterator does not exist');

            }

        }

        return false;

    }

	/**
	 * Save cache
     *
	 * @param string $key Cache key (host name inclusion not required)
	 * @param mixed $value Variable to be cached
	 * @param int $duration Cache expiration in seconds
	 * @param bool $apc Local cache or network wide cache
     * @param string $namespace Cache namespace
     * @param boolean $serialize Serialize object?
	 * @return bool True on success and false on failure
	 */
	public static function saveCache($key = null, $value = null, $duration = 600, $apc = false, $namespace = null, $serialize = true){

		//quick sanity check.. we need a key
		if(empty($key)){ return false; }

        /**
         * Add namespace
         */
        $key = self::generateKey($key, $namespace);

		//store in stack
		if($value !== false && $value !=  null) self::$cacheStack[$key] = $value;
			
		//cache local .. if we can
		if($apc && function_exists(self::CACHE_APC_STORE_FUNCTION)){

			//store cache
			return apc_store(DOMAIN_NAME . self::CACHE_KEY_CACHE_SEPARATOR . $key, ($serialize ? serialize($value) : $value), $duration);
			
		//if network wide
		}else{
			
			//check for Memcache
			if(!class_exists(self::CACHE_MEMCACHE_CLASS_NAME)){ 
				return false;
			}
			
			//assure memcached
			if(self::assureMemcache()){

				if(false === self::$Memcache->replace(DOMAIN_NAME . self::CACHE_KEY_CACHE_SEPARATOR . $key, ($serialize ? serialize($value) : $value), false, $duration)){
					self::$Memcache->set(DOMAIN_NAME . self::CACHE_KEY_CACHE_SEPARATOR . $key, ($serialize ? serialize($value) : $value), false, $duration);
				}

				//success
				return true;
		
			}
	
		}

        //notify
        CoreLog::error('Unable to cache data');
		
		//something went wrong
		return false;
		
	}

    /**
     * Generate key
     *
     * @param $key
     * @param $namespace
     * @return bool|string
     */
    private static function generateKey($key, $namespace, $apc = true){
        if(empty($namespace)) return $key;
        if(false !== ($ns_value = self::createNamespace($namespace, $apc))){
            return $key . self::CACHE_KEY_SEPARATOR . $ns_value;
        }
        CoreLog::error('Unable to generate cache key');
        return false;
    }

	/**
	 * Get cache
     *
	 * @param string $key Cache key (host name inclusion not required)
	 * @param bool $apc Local cache or network wide cache
     * @param string $namespace Cache namespace
     * @param boolean $serialize Serialize object?
	 * @return mixed Cached variable or false
	 */
	public static function getCache($key = null, $apc = false, $namespace = null, $serialize = true){

		//quick sanity check.. we need a key
		if(empty($key) || !CACHING_ENABLED){ return false; }

        /**
         * Add namespace
         */
        $key = self::generateKey($key, $namespace);

		//no cache system lookup needed
		if(isset(self::$cacheStack[$key])){
			return self::$cacheStack[$key];
		}

		//get cache local .. if we can
		if($apc && function_exists(self::CACHE_APC_FETCH_FUNCTION)){

			//get cached value
			return $serialize ? unserialize(apc_fetch(DOMAIN_NAME . self::CACHE_KEY_CACHE_SEPARATOR . $key)) : apc_fetch(DOMAIN_NAME . self::CACHE_KEY_CACHE_SEPARATOR . $key);

        //try memcache
		}else{
			
			//quick sanity check
			if(!class_exists(self::CACHE_MEMCACHE_CLASS_NAME)){
				return false;
			}
			
			//assure memcached
			if(self::assureMemcache()){
			
				//lookup by key and return 
				return $serialize ? unserialize(self::$Memcache->get(DOMAIN_NAME . self::CACHE_KEY_CACHE_SEPARATOR . $key)) : self::$Memcache->get(DOMAIN_NAME . self::CACHE_KEY_CACHE_SEPARATOR . $key);
			
			}
		
		}

		//something went wrong
		return false;
		
	}

    /**
     * Flush cache
     */
    public static function flushCache(){

        /**
         * Flush APC cache
         */
        if (function_exists(self::CACHE_APC_CLEAR_CACHE_FUNCTION)) {
            apc_clear_cache();
        }

        /**
         * Assure memcache
         */
        if (self::assureMemcache()) {
            self::$Memcache->flush();
        }

    }

	/**
	 * Assure memcache connection
	 */
	private static function assureMemcache(){
		
		//already connected
		if(is_object(self::$Memcache)){  
			return true;
		}
		
		//connect now
		if(method_exists(self::CACHE_MEMCACHE_CLASS_NAME, 'connect')){
			
			//initiate and connect
			self::$Memcache = new Memcache; 
			return self::$Memcache->connect(MEMCACHED_HOST, MEMCACHED_PORT);
			
		}
		
		//was unable to connect
		return false;
		
	}	
	
}