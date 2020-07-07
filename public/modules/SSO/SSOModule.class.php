<?php

/**
 * SSO Module
 * This adds the ability to adds identity provider - and service provider abilities
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class SSOModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'SSO Module';
    public static $description = 'Adds ability to configure instance as service provider or identity provider';
    public static $version = '1.0.0';
    public static $dependencies = array(
        'User' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'Admin' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'MapTable' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'Language' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        )
    );

    /** @var SSOService $SSOService */
    private static $SSOService;

    /**
     * Init script
     *
     */
    public static function __init__()
    {
        self::$SSOService = CoreLogic::getService('SSOService');
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
        $routes = array();
        array_push($routes, CoreControllerObject::buildMethod('/sso/saml', __CLASS__, 'ssoSaml', CoreControllerObject::MATCH_TYPE_STRING));
        return $routes;
    }

    public static function ssoSaml(){

        self::$SSOService->loasSimpleSaml();

    }

    /**
     * Run on install
     *
     */
    public static function __install__()
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

}