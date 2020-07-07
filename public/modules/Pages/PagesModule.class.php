<?php

/**
 * Class PagesModule
 *
 *
 */
class PagesModule implements CoreModuleInterface {

	/**
	 * Module description
	 */
	public static $name = 'Pages Module';
	public static $description = 'The module allow the creation of pages';
	public static $version = '1.0.2';
	public static $dependencies = array();

	/**
	 * Get listeners
	 *
	 * @return mixed
	 */
	public static function getListeners()
	{
		// TODO: Implement getListeners() method.
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