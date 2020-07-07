<?php

/**
 * Utility Menu Module
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UtilityMenuModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Utility Menu Module';
    public static $description = 'Utility menu module. Adds utility menu features.';
    public static $version = '1.0.0';
    public static $dependencies = array(
        'User' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        )
    );

    /**
     * Utility nav
     */
    const UTILITY_NAV_ID = 'utilitynav';

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