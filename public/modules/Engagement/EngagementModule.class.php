<?php

/**
 * Engagement Module
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class EngagementModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Engagement Module';
    public static $description = 'Engagement tools';
    public static $version = '1.0.2';
    public static $dependencies = array(
        'CrutchKit' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        )
    );

    /** @var EngagementService $EngagementService */
    private static $EngagementService;

    private static $collectEmailPromptTemplate;

    public static function __init__(){
        self::$EngagementService = CoreLogic::getService('EngagementService');
    }

    public static function getListeners(){
        $listeners = array();
        array_push($listeners, CoreObserverObject::build(CoreRender::EVENT_RENDER_PAGE_END_HEAD, __CLASS__, 'queueEmailUpdatesTemplate'));
        array_push($listeners, CoreObserverObject::build(CoreRender::EVENT_RENDER_PAGE_END_BODY, __CLASS__, 'insertEmailUpdatesHtml'));
        return $listeners;
    }

    public static function getInterceptors(){

    }

    public static function getMenus(){

    }

    public static function getRoutes(){
        $routes = array();
        array_push($routes, CoreControllerObject::buildApi('/api/v1/engagement/email/capture', __CLASS__, 'captureEmail', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_POST));
        return $routes;
    }

    public static function queueEmailUpdatesTemplate(){
        if(self::$EngagementService->hasEngagementCollectEmailObject()){
            self::$collectEmailPromptTemplate = CoreTemplate::render('engagementcollectemailprompt', CoreArrayUtils::asArray(self::$EngagementService->getEngagementCollectEmailObject()));
            CoreRender::addBodyClass('engagementcollectemail');
        }
    }

    public static function insertEmailUpdatesHtml(){
        if(!empty(self::$collectEmailPromptTemplate)) {
            CoreRender::$output .= self::$collectEmailPromptTemplate;
        }
    }

    public static function captureEmail(){
        /** @var EngagementEmailCaptureObject $EngagementEmailCaptureObject */
        $EngagementEmailCaptureObject = CoreObjectUtils::applyRow('EngagementEmailCaptureObject', $_POST);
        CoreApi::setData('captured', self::$EngagementService->captureEmail($EngagementEmailCaptureObject));
    }

    public static function __install__(){

    }

    public static function __update__($previousVersion, $newVersion){

    }

    public static function __enable__(){

    }

    public static function __disable__(){

    }

}