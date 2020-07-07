<?php

/**
 * Commerce module
 * This module adds commerce ability
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CommerceModule implements CoreModuleInterface {
	
	/**
	 * Module description
	 */
	public static $name = 'Commerce Module';
	public static $description = 'Adds E-Commerce functionality';
	public static $version = '1.0.0';
	public static $dependencies = array(
		'Payment' => array(
			'min' => '1.0.0',
			'max' => '1.9.9'
		),
		'User' => array(
			'min' => '1.0.0',
			'max' => '1.9.9'
		),
		'Admin' => array(
			'min' => '1.0.0',
			'max' => '1.9.9'
		)
	);

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