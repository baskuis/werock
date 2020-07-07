<?php

/**
 * Redirect Rules Module
 * Allows configuration of redirect rules
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class RedirectRulesModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Redirect Rules Module';
    public static $description = 'Adds redirect rules components';
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

        $interceptors = array();

        /**
         * Run redirect rules
         */
        array_push($interceptors, new CoreInterceptorObject('CoreController', 'routeRequest', __CLASS__, 'runRules', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));

        return $interceptors;

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
     * Init script
     *
     */
    public static function __init__()
    {

    }

    /**
     * Process rules
     *
     * @param null $return
     * @param array $params
     * @return null
     */
    public static function runRules($return = null, $params = array()){

        /**
         * Create helpful pointers
         */
        $CoreControllerObject = isset($params[0]) ? $params[0] : null;
        $urlParams = isset($params[1]) ? $params[1] : null;
        $url = isset($urlParams[0]) ? $urlParams[0] : null;

        /** @var RedirectRulesService $RedirectRulesManager */
        $RedirectRulesManager = CoreLogic::getService('RedirectRulesService');

        /** @var array $rules */
        $rules = $RedirectRulesManager->getRules();

        /** assertion */
        if(empty($rules)){
            return $return;
        }

        /** @var RedirectRulesRuleObject $RedirectRulesRuleObject */
        foreach($rules as $RedirectRulesRuleObject){

            /** rule does not apply */
            if(!$RedirectRulesRuleObject->match($url)) continue;

            /** handle rule */
            $RedirectRulesRuleObject->handle($url);

        }

        return $return;

    }

    /**
     * Run on install
     *
     */
    public static function __install__()
    {
        // TODO: Implement __intall__() method.
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
        // TODO: Implement __update__() method.
    }

    /**
     * Run on enable
     *
     * @return void
     */
    public static function __enable__()
    {
        // TODO: Implement __enable__() method.
    }

    /**
     * Run on disable
     *
     * @return mixed
     */
    public static function __disable__()
    {
        // TODO: Implement __disable__() method.
    }

}