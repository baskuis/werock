<?php

/**
 * Email Module
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
Class EmailModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Email Module';
    public static $description = 'Send email module';
    public static $version = '1.0.2';
    public static $dependencies = array();

    const EMAIL_HOST_PROP = 'email.host';
    const EMAIL_PORT_PROP = 'email.port';
    const EMAIL_USER_PROP = 'email.user';
    const EMAIL_PASS_PROD = 'email.pass';

    /** @var EmailService $EmailService */
    private static $EmailService;

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

        self::$EmailService = CoreLogic::getService('EmailService');

        /** @var EmailSmtpAuthenticationObject $SmtpAuthenticationObject */
        $SmtpAuthenticationObject = CoreLogic::getObject('EmailSmtpAuthenticationObject');

        //set SMTP Authentcation details
        $SmtpAuthenticationObject->setServer(CoreModule::getProp(__CLASS__, self::EMAIL_HOST_PROP, 'ssl://smtp.gmail.com'));
        $SmtpAuthenticationObject->setPort(CoreModule::getProp(__CLASS__, self::EMAIL_PORT_PROP, '465'));
        $SmtpAuthenticationObject->setUsername(CoreModule::getProp(__CLASS__, self::EMAIL_USER_PROP, 'b@ukora.com'));
        $SmtpAuthenticationObject->setPassword(CoreModule::getProp(__CLASS__, self::EMAIL_PASS_PROD, 'mp5d2FkQm0n'));

        self::$EmailService->setSmtpAuthenticationObject($SmtpAuthenticationObject);

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