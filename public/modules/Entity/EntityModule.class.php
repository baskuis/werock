<?php

class EntityModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Entity Module';
    public static $description = 'Allows for use of entities';
    public static $version = '1.0.0';
    public static $dependencies = array(
        'MapTable' => array(
            'min' => '1.0.0',
            'max' => '1.0.0'
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