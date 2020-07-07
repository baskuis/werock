<?php

/**
 * Core Module Interface
 * this defines required methods on Module definition classes
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
interface CoreModuleInterface {

    /**
     * Init script
     *
     */
    public static function __init__();

    /**
     * Get listeners
     *
     * @return mixed
     */
    public static function getListeners();

    /**
     * Get interceptors
     *
     * @return mixed
     */
    public static function getInterceptors();

    /**
     * Get menus
     *
     * @return mixed
     */
    public static function getMenus();

    /**
     * Get routes
     *
     * @return mixed
     */
    public static function getRoutes();

    /**
     * Run on install
     *
     */
    public static function __install__();

    /**
     * Run on update
     *
     * @param $previousVersion
     * @param $newVersion
     *
     * @return void
     */
    public static function __update__($previousVersion, $newVersion);

    /**
     * Run on enable
     *
     * @return void
     */
    public static function __enable__();

    /**
     * Run on disable
     *
     * @return mixed
     */
    public static function __disable__();

}