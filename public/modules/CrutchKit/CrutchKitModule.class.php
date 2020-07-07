<?php

/**
 * CrutchKit
 */
class CrutchKitModule implements CoreModuleInterface {

	/**
	 * Module description
	 */
	public static $name = 'CrutchKit Module';
	public static $description = 'Adds a variety of crutches support';
	public static $version = '1.0.0.1';
	public static $dependencies = array();
		
	/**
	 * UserRegisterAction listeners, toMethod
	 */	
	public static function __init__(){
	
	}

	/**
	 * Get listeners
	 *
	 * @return mixed
	 */
	public static function getListeners()
	{

	}

	/**
	 * Get interceptors
	 *
	 * @return mixed
	 */
	public static function getInterceptors()
	{

	}

	/**
	 * Get menus
	 *
	 * @return mixed
	 */
	public static function getMenus()
	{

	}

	/**
	 * Get routes
	 *
	 * @return mixed
	 */
	public static function getRoutes()
	{

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