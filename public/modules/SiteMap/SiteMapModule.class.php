<?php

/**
 * SiteMap Module
 * This module adds a sitemap index and sitemaps
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class SiteMapModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'SiteMap Module';
    public static $description = 'Adds configurable index with sitemaps';
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

    const ROBOTS_TXT_PATH = '/robots.txt';
    const SITEMAP_INDEX_PATH = '/siteindex.xml';

    /**
     * Submitting siteindex to these search engines
     *
     * @var array
     */
    public static $searchEngines = array(
        'http://www.google.com/webmasters/sitemaps/ping?sitemap=',
        'http://www.bing.com/ping?sitemap='
    );

    /** @var SiteMapService $SiteMapService */
    private static $SiteMapService;

    public static function __init__()
    {

        /** Set build sitemaps schedule */
        $CoreScheduleJobObject = new CoreScheduleJobObject();
        $CoreScheduleJobObject->cron('*/5 * * * *');
        $CoreScheduleJobObject->setModule(__CLASS__);
        $CoreScheduleJobObject->setMethod('buildSiteMaps');
        CoreSchedule::add($CoreScheduleJobObject);

        /** Set ping search engines schedule */
        $CoreScheduleJobObject = new CoreScheduleJobObject();
        $CoreScheduleJobObject->cron('0 0 * * *');
        $CoreScheduleJobObject->setModule(__CLASS__);
        $CoreScheduleJobObject->setMethod('pingSearchEngines');
        CoreSchedule::add($CoreScheduleJobObject);

        self::$SiteMapService = CoreLogic::getService('SiteMapService');

    }

    public static function getListeners()
    {
        // TODO: Implement getListeners() method.
    }

    public static function getInterceptors()
    {
        // TODO: Implement getInterceptors() method.
    }

    public static function getMenus()
    {
        // TODO: Implement getMenus() method.
    }

    public static function getRoutes()
    {
        $routes = array();

        /**
         * Build site-maps
         */
        array_push($routes, CoreControllerObject::buildMethod('/do/sitemaps/build', __CLASS__, 'buildSiteMaps', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));

        /**
         * Sitemaps index
         */
        array_push($routes, CoreControllerObject::buildMethod(self::SITEMAP_INDEX_PATH, __CLASS__, 'showIndex', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));

        /**
         * Individual site-maps
         */
        array_push($routes, CoreControllerObject::buildMethod('/^\/sitemaps\/([a-z]+)\.xml$/i', __CLASS__, 'showSiteMap', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::REQUEST_GET));

        /**
         * Robots txt
         */
        array_push($routes, CoreControllerObject::buildMethod(self::ROBOTS_TXT_PATH, __CLASS__, 'robotsTxt', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));

        return $routes;
    }

    public static function showIndex(){
        CoreRender::$output = self::$SiteMapService->renderSiteMapIndex();
    }

    public static function showSiteMap($params = array()){
        $name = isset($params[1]) ? $params[1] : null;
        CoreRender::$output = self::$SiteMapService->renderSiteMap($name);
    }

    public static function buildSiteMaps(){
        self::$SiteMapService->buildSiteMaps();
    }

    /**
     * Ping search engines
     */
    public static function pingSearchEngines(){
        foreach(self::$searchEngines as $searchEngine){
            @file_get_contents($searchEngine . urlencode(HTTP_PROTOCOL . DOMAIN_NAME . self::SITEMAP_INDEX_PATH));
        }
    }

    /**
     * TODO: Dynamically build disallow block
     *
     * @return string
     */
    public static function robotsTxt(){
        CoreRender::$output =
            'User-agent: *' . "\n" .
            'Disallow: /admin' . "\n";
        if(!empty(self::$SiteMapService->sitemaps)){
            /** @var SiteMapObject $SiteMapObject */
            foreach(self::$SiteMapService->sitemaps as $SiteMapObject){
                CoreRender::$output .= 'Sitemap: ' . $SiteMapObject->getUrl() . "\n";
            }
        }
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