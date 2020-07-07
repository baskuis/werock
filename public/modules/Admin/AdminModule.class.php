<?php

/**
 * Admin Module
 * adds an extendable admin section
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class AdminModule implements CoreModuleInterface {
	
	/**
	 * Module description
	 */
	public static $name = 'Admin Module';
	public static $description = 'Admin panel';
	public static $version = '1.0.0';
	public static $dependencies = array(
        'Form' => array('min' => '1.0.0', 'max' => '1.9.9'),
		'User' => array('min' => '1.0.0', 'max' => '1.9.9'),
        'UtilityMenu' => array('min' => '1.0.0', 'max' => '1.9.9'),
        'CrutchKit' => array('min' => '1.0.0', 'max' => '1.9.9'),
        'Intelligence' => array('min' => '1.0.0', 'max' => '1.9.9')
	);

    /**
     * Constants
     */
    CONST ADMIN_NAV_ID = 'AdministratorSectionNav';

    /**
     * Section nav references
     */
    const ADMIN_NAV_ID_SYSTEM = 'System';
    const ADMIN_NAV_ID_TOOLS = 'Tools';
    const ADMIN_NAV_ID_PEOPLE = 'People';
    const ADMIN_NAV_ID_CONTENT = 'Content';
    const ADMIN_NAV_ID_DATA = 'Data';

    /** @var UserService $UserService */
    private static $UserService;

    /** @var UserEntitlementService $UserEntitlementService */
    private static $UserEntitlementService;

    /**
     * Get listeners
     *
     * @return mixed
     */
    public static function getListeners()
    {
        return array();
    }

    /**
     * Get interceptors
     *
     * @return mixed
     */
    public static function getInterceptors()
    {

        $return = array();

        /**
         * Assure authorized access to
         * admin components
         */
        array_push($return, new CoreInterceptorObject('CoreController', 'routeRequest', __CLASS__, 'assureAuthorized', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));

        /**
         * Handle admin unauthorized
         *
         */
        array_push($return, new CoreInterceptorObject('AdminUnauthorizedException', 'handle', __CLASS__, 'handleUnauthorized', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));

        return $return;

    }

    /**
     * Get menus
     *
     * @return mixed
     */
    public static function getMenus()
    {

        /**
         * Define top level
         * menu systems
         */

        $return = array();

        //create menu section in admin nav (System)
        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId(self::ADMIN_NAV_ID_SYSTEM);
        $CoreMenuObject->setName(CoreLanguage::get('admin:system:link:name'));
        $CoreMenuObject->setHref('/admin/system');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:system:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(0);
        $CoreMenuObject->setTarget(self::ADMIN_NAV_ID);
        array_push($return, $CoreMenuObject);

        //create menu section in admin nav (System)
        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId(self::ADMIN_NAV_ID_TOOLS);
        $CoreMenuObject->setName(CoreLanguage::get('admin:tools:link:name'));
        $CoreMenuObject->setHref('/admin/tools');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:tools:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(10);
        $CoreMenuObject->setTarget(self::ADMIN_NAV_ID);
        array_push($return, $CoreMenuObject);

        //create menu section in admin nav (Users)
        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId(self::ADMIN_NAV_ID_CONTENT);
        $CoreMenuObject->setName(CoreLanguage::get('admin:content:link:name'));
        $CoreMenuObject->setHref('/admin/content');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:content:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(20);
        $CoreMenuObject->setTarget(self::ADMIN_NAV_ID);
        array_push($return, $CoreMenuObject);

        //create menu section in admin nav (Users)
        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId(self::ADMIN_NAV_ID_PEOPLE);
        $CoreMenuObject->setName(CoreLanguage::get('admin:people:link:name'));
        $CoreMenuObject->setHref('/admin/people');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:people:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(30);
        $CoreMenuObject->setTarget(self::ADMIN_NAV_ID);
        array_push($return, $CoreMenuObject);

        //create menu section in admin nav (Users)
        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId(self::ADMIN_NAV_ID_DATA);
        $CoreMenuObject->setName(CoreLanguage::get('admin:data:link:name'));
        $CoreMenuObject->setHref('/admin/data');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:data:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(40);
        $CoreMenuObject->setTarget(self::ADMIN_NAV_ID);
        array_push($return, $CoreMenuObject);

        return $return;

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
        self::$UserService = CoreLogic::getService('UserService');
        self::$UserEntitlementService = CoreLogic::getService('UserEntitlementService');
	}

    /**
     * Handle Unauthorized
     */
    public static function handleUnauthorized(){

        /**
         * Save requested location
         */
        $_SESSION['REQUESTED_URI'] = $_SERVER['REQUEST_URI'];

        /**
         * Set the redirect header
         */
        CoreHeaders::setRedirect('/admin/login');

    }

    /**
     * Assure authorized
     *
     * @throws UserUnauthorizedException
     */
    public static function assureAuthorized($return, $args){
        
        if(!$return) return false;

        /**
         * Check if this path is part of admin
         */
        if(

            /**
             * Prevent access to any /admin/** page
             */
            (preg_match('/^\/admin\/?/i', CoreController::$matchUrl) && !preg_match('/^\/admin\/login/i', CoreController::$matchUrl)) ||

            /**
             * Protect /api/v1/intelligence endpoints
             * Check if this an api intelligence request
             */
            preg_match('/^\/api\/v1\/intelligence\//i', CoreController::$matchUrl) ||

            /**
             * Protect /api/v1/entitlements endpoints
             * Check if this an api intelligence request
             */
            preg_match('/^\/api\/v1\/entitlements\//i', CoreController::$matchUrl)

        ){
            if(!self::$UserService->activeUser() || !self::$UserEntitlementService->userHasEntitlement(self::$UserService->getCurrentUser(), self::$UserEntitlementService->getEntitlement(UserModule::ENTITLEMENT_SYSTEM_ADMIN))){
                throw new AdminUnauthorizedException();
            }
        }

        return $return;

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