<?php

/**
 * Experimentation Tools
 * Adds experimentation tools like AB Testing and Multi Armed Bandit
 * Uses murmurhash for randomization
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class ExperimentationModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Experimentation Module';
    public static $description = 'Multiple experimentation tools';
    public static $version = '1.0.19';
    public static $dependencies = array(
        'Admin' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'User' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'MapTable' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'Intelligence' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        )
    );

    /** @var ExperimentationService $ExperimentationService */
    private static $ExperimentationService;

    public static function __init__()
    {
        self::$ExperimentationService = CoreLogic::getService('ExperimentationService');
    }

    public static function getListeners()
    {
        $listeners = array();
        array_push($listeners, CoreObserverObject::build(CoreController::CONTROLLER_EVENT_HANDLE_BEFORE, __CLASS__, 'buildExperiments'));
        array_push($listeners, CoreObserverObject::build(CoreRender::EVENT_RENDER_PAGE_END_HEAD, __CLASS__, 'renderExperimentalJavascriptBlock'));
        return $listeners;
    }

    public static function getInterceptors()
    {

        /**
         * @var array $interceptors
         */
        $interceptors = array();

        /**
         * Capturing intelligence events
         */
        array_push($interceptors, new CoreInterceptorObject('IntelligenceService', 'addToIntelligenceStack', __CLASS__, 'captureEvent', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));

        /**
         * Wrap view/render
         */
        array_push($interceptors, new CoreInterceptorObject(CoreTemplate::getClassName(), 'render', __CLASS__, 'preTemplateRender', CoreInterceptorObject::INTERCEPTOR_TYPE_BEFORE));
        array_push($interceptors, new CoreInterceptorObject(CoreTemplate::getClassName(), 'getView', __CLASS__, 'preTemplateGetView', CoreInterceptorObject::INTERCEPTOR_TYPE_BEFORE));
        array_push($interceptors, new CoreInterceptorObject(CoreFeTemplate::getClassName(), 'getView', __CLASS__, 'preFeTemplateGetView', CoreInterceptorObject::INTERCEPTOR_TYPE_BEFORE));

        return $interceptors;

    }

    public static function getMenus(){

    }

    public static function getRoutes(){
        $routes = array();
        array_push($routes, CoreControllerObject::buildMethod('/experimental/js', __CLASS__, 'renderExperimentalJavascript', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));
        return $routes;
    }

    /**
     * Build experiments
     */
    public static function buildExperiments(){
        self::$ExperimentationService->buildExperiments();
    }

    /**
     * Render script block with experimental javascript
     *
     */
    public static function renderExperimentalJavascript(){
        CoreRender::$output .= self::$ExperimentationService->renderJavascript();
    }

    /**
     * Render script block with experimental javascript
     *
     */
    public static function renderExperimentalJavascriptBlock(){
        CoreRender::$output .= '<script type="text/javascript">' . self::$ExperimentationService->renderJavascript() . '</script>';
    }

    /**
     * Intercept render
     *
     * @param array $params
     * @return array
     */
    public static function preTemplateRender($params = array()){
        if(isset($params[0])){
            $params[0] = self::$ExperimentationService->handleTemplate($params[0]);
        }
        return $params;
    }

    /**
     * Intercept get view
     *
     * @param array $params
     * @return array
     */
    public static function preTemplateGetView($params = array()){
        if(isset($params[0])){
            $params[0] = self::$ExperimentationService->handleTemplate($params[0]);
        }
        return $params;
    }

    /**
     * Intercept fe get view
     *
     * @param array $params
     * @return array
     */
    public static function preFeTemplateGetView($params = array()){
        if(isset($params[0])){
            $params[0] = self::$ExperimentationService->handleTemplate($params[0]);
        }
        return $params;
    }

    /**
     * Capture an event
     *
     * @param $return
     * @param $params
     * @param IntelligenceService $intelligenceService
     */
    public static function captureEvent($return, $params, IntelligenceService $intelligenceService){
        if(true === $return) {

            /** @var string $key */
            $key = isset($params[0]) ? $params[0] : false;
            /** @var string $value */
            $value = isset($params[1]) ? $params[1] : false;

            /** evaluate the event */
            self::$ExperimentationService->handleEvent($key, $value);

        }
        return $return;
    }

    public static function __install__(){

    }

    public static function __update__($previousVersion, $newVersion){

        /** Cleanup in 1.0.18 of group_id on experiments */
        if($newVersion == '1.0.18'){
            if(CoreSqlUtils::columnExists('werock_experiments', 'werock_group_id')){
                CoreSqlUtils::query('ALTER TABLE werock_experiments DROP COLUMN werock_group_id;');
            }
        }

    }

    public static function __enable__(){

    }

    public static function __disable__(){

    }

}