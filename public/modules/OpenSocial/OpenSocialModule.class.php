<?php

/**
 * OpenSocialModule
 * This module adds open social authentication and integration abilities
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class OpenSocialModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'OpenSocial Module';
    public static $description = 'Adds open social authentication and integration abilities';
    public static $version = '1.0.0';
    public static $dependencies = array(
        'User' => array('min' => '1.0.0', 'max' => '1.9.9'),
        'Intelligence' => array('min' => '1.0.0', 'max' => '1.9.9')
    );

    const FACEBOOK_LOGIN_URL = '/do/opensocial/facebook/login';
    const FACEBOOK_REGISTER_URL = '/do/opensocial/facebook/register';
    const FACEBOOK_LOGIN_CALLBACK_URL = '/do/opensocial/facebook/login/callback';
	const FACEBOOK_REGISTER_CALLBACK_URL = '/do/opensocial/facebook/register/callback';

    /** @var OpenSocialFacebookService $OpenSocialFacebookService */
    private static $OpenSocialFacebookService;

    /**
     * Get listeners
     *
     * @return mixed
     */
    public static function getListeners()
    {
        $listeners = array();
        array_push($listeners, CoreObserverObject::build(CoreController::CONTROLLER_EVENT_RENDER_BEFORE, __CLASS__, 'appendFacebookSDK'));
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
    public static function getRoutes(){
        $routes = array();
        array_push($routes, CoreControllerObject::buildMethod(self::FACEBOOK_LOGIN_URL, __CLASS__, 'facebookLogin', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));
        array_push($routes, CoreControllerObject::buildMethod(self::FACEBOOK_REGISTER_URL, __CLASS__, 'facebookRegister', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));
        array_push($routes, CoreControllerObject::buildMethod(self::FACEBOOK_LOGIN_CALLBACK_URL, __CLASS__, 'facebookLoginCallback', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));
        array_push($routes, CoreControllerObject::buildMethod(self::FACEBOOK_REGISTER_CALLBACK_URL, __CLASS__, 'facebookRegisterCallback', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));
        return $routes;
    }

    /**
     * UserRegisterAction listeners, toMethod
     */
    public static function __init__(){
        self::$OpenSocialFacebookService = CoreLogic::getService('OpenSocialFacebookService');
    }

    /**
     * Facebook login
     */
    public static function facebookLogin(){
        $loginUrl = self::$OpenSocialFacebookService->getConnectUrl(HTTP_PROTOCOL . DOMAIN_NAME . self::FACEBOOK_LOGIN_CALLBACK_URL);
        CoreHeaders::setRedirect($loginUrl);
    }

    /**
     * Facebook callback
     */
    public static function facebookLoginCallback(){
        self::$OpenSocialFacebookService->connectCallback(false);
    }

	/**
     * Facebook register
     */
	public static function facebookRegister(){
		$loginUrl = self::$OpenSocialFacebookService->getConnectUrl(HTTP_PROTOCOL . DOMAIN_NAME . self::FACEBOOK_REGISTER_CALLBACK_URL);
        CoreHeaders::setRedirect($loginUrl);
	}
	
	/**
     * Facebook callback
     */
	public static function facebookRegisterCallback(){
		self::$OpenSocialFacebookService->connectCallback(true);
	}

    /**
     * Append facebook SDK
     * if facebook is enabled
     *
     */
    public static function appendFacebookSDK(){
        if(false === self::$OpenSocialFacebookService->isEnabled()){
            return;
        }

        /**
         *
         * Turned this off - was blocking other scripts
         * TODO: Create toggle to enable/disable per domain
         *
         * CoreScript::$requestScopedScript .= '
          window.fbAsyncInit = function() {
            FB.init({
              appId      : \'' . CoreModule::getProp('OpenSocialModule', 'facebook.application.id', '') . '\',
              xfbml      : true,
              version    : \'v2.5\'
            });
          };
          (function(d, s, id){
             var js, fjs = d.getElementsByTagName(s)[0];
             if (d.getElementById(id)) {return;}
             js = d.createElement(s); js.id = id;
             js.src = "//connect.facebook.net/en_US/sdk.js";
             fjs.parentNode.insertBefore(js, fjs);
           }(document, \'script\', \'facebook-jssdk\'));
        ';
         */

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