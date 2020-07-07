<?php

/**
 * Caching Module
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CacheModule implements CoreModuleInterface {
	
	/**
	 * Module description
	 */
	public static $name = 'Cache Module';
	public static $description = 'Allows caching';
	public static $version = '1.0.0';
	public static $dependencies = array();

	/**
	 * Constants and defaults
	 */
	const CACHING_ENABLED = 'caching:enabled';
	const CACHING_ENABLED_VALUE = false;
	const CACHING_DURATION = 'caching:duration';
	const CACHING_DURATION_VALUE = 600;

	/**
	 * Get listeners
	 *
	 * @return mixed
	 */
	public static function getListeners()
	{

        $listeners = array();

        array_push($listeners, new CoreObserverObject(CoreController::CONTROLLER_EVENT_RENDER_BEFORE, __CLASS__, 'pageCacheLookup'));
        array_push($listeners, new CoreObserverObject(CoreController::CONTROLLER_EVENT_RENDER_BEFORE, __CLASS__, 'pageCacheLookup'));
        array_push($listeners, new CoreObserverObject(CoreController::CONTROLLER_EVENT_RENDER_AFTER, __CLASS__, 'pageCacheStore'));

        return $listeners;

	}

	/**
	 * Get interceptors
	 *
	 * @return mixed
	 */
	public static function getInterceptors()
	{
		// TODO: Implement getInterceptors() method.
	}

	/**
	 * Get menus
	 *
	 * @return mixed
	 */
	public static function getMenus()
	{
		// TODO: Implement getMenus() method.
	}

	/**
	 * Get routes
	 *
	 * @return mixed
	 */
	public static function getRoutes()
	{
		// TODO: Implement getRoutes() method.
	}

	/**
	 * UserRegisterAction listeners, toMethod
	 */	
	public static function __init__(){

	}

	/**
	 * Create page hash
	 */
	private static function createPageHash(){
		
		//creating unique string 
		return md5($_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING']);
	
	}
	
	/**
	 * Page cache lookup
	 */
	public static function pageCacheLookup(){
		
		//see if caching has been enabled
		if(!CoreModule::getProp(__CLASS__, self::CACHING_ENABLED, self::CACHING_ENABLED_VALUE)){
			return;
		}
		
		//get page string and exit
		if(false !== ($pageString = CoreCache::getCache(self::createPageHash(), false))){
			echo $pageString;
			exit(0);
		}

	}
	
	/**
	 * Store page cache
	 */
	public static function pageCacheStore(){

		//see if caching has been enabled
		if(!CoreModule::getProp(__CLASS__, self::CACHING_ENABLED, self::CACHING_ENABLED_VALUE)){
			return;
		}

		//save page cache
		CoreCache::saveCache(
			self::createPageHash(), 
			CoreRender::getOutput(), 
			CoreModule::getProp(__CLASS__, self::CACHING_DURATION, self::CACHING_DURATION_VALUE), 
			false);
		
	}

	/**
	 * Run on update
	 *
	 * @param $previousVersion
	 * @param $newVersion
	 *
	 * @return void
	 */
	public static function __update__($previousVersion, $newVersion)
	{

	}

	/**
	 * Run on enable
	 *
	 * @return void
	 */
	public static function __enable__()
	{

	}

	/**
	 * Run on disable
	 *
	 * @return mixed
	 */
	public static function __disable__()
	{

	}

	/**
	 * Run on install
	 *
	 */
	public static function __install__()
	{

	}

}