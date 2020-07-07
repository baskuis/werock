<?php

/**
 * Security Module
 * This module adds security
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class SecurityModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Security Module';
    public static $description = 'Adds additional security';
    public static $version = '1.0.7';
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

    /** @var SecurityService $SecurityService */
    private static $SecurityService;

    /**
     * Get listeners
     *
     * @return mixed
     */
    public static function getListeners(){
        $listeners = array();
        array_push($listeners, new CoreObserverObject('user:login:failed', __CLASS__, 'failedLogin'));
        array_push($listeners, CoreObserverObject::build(CoreLog::WRITE_LOG_MESSAGE_AFTER_EVENT, __CLASS__, 'logEvent'));
        return $listeners;
    }

    /**
     * Get interceptors
     *
     * @return mixed
     */
    public static function getInterceptors(){ }

    /**
     * Get menus
     *
     * @return mixed
     */
    public static function getMenus(){ }

    /**
     * Get routes
     *
     * @return mixed
     */
    public static function getRoutes(){ }

    /**
     * Init
     */
    public static function __init__(){
        self::$SecurityService = CoreLogic::getService('SecurityService');
    }

    /**
     * Handle failed login
     *
     * @param UserAuthenticationObject $UserAuthenticationObject
     */
    public static function failedLogin(UserAuthenticationObject $UserAuthenticationObject){
        self::$SecurityService->captureFailedLogin($UserAuthenticationObject);
    }

    /**
     * Handle log entries
     *
     * @param CoreLogObject $coreLogObject
     */
    public static function logEvent(CoreLogObject $coreLogObject){
        if(empty($coreLogObject) || $coreLogObject->getType() == CoreLog::CLI) return;
        self::$SecurityService->updateRemoteReputation($coreLogObject);
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