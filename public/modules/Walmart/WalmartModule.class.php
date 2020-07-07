<?php

/**
 * Wallmart Module
 *
 * PHP version 7
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class WalmartModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Wallmart Module';
    public static $description = 'Exposes Wallmart A.P.I. Adds admin configuration.';
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
        'SearchSuggestions' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        )
    );

    const WALMART_SUGGESTIONS_KEY = 'walmartSearchSuggestions';

    /** @var WalmartService $WalmartService */
    private static $WalmartService;

    public static function __init__()
    {
        self::$WalmartService = CoreLogic::getService('WalmartService');
    }

    public static function getListeners()
    {

    }

    public static function getInterceptors()
    {

    }

    public static function getMenus()
    {

    }

    public static function getRoutes()
    {
        $routes = array();
        array_push($routes, CoreControllerObject::buildApi('/api/v1/walmart/search', __CLASS__, 'search', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));
        array_push($routes, CoreControllerObject::buildApi('/api/v1/walmart/lookup', __CLASS__, 'lookup', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));
        return $routes;

    }

    public static function search(){
        $query = isset($_GET['query']) ? $_GET['query'] : false;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'relevance';
        $category = isset($_GET['category']) ? $_GET['category'] : 'All';
        $tag = isset($_GET['tag']) ? $_GET['tag'] : null;
        if($page < 1 || !is_numeric($page)){ $page = 1; }
        $items = self::$WalmartService->search($query, $page, $sort, $category, $tag);
        CoreApi::setData('items', $items);
    }

    public static function lookup(){
        $upc = isset($_GET['upc']) ? $_GET['upc'] : null;
        CoreApi::setData('lookup', self::$WalmartService->upc($upc));
    }

    public static function __install__()
    {

    }

    public static function __update__($previousVersion, $newVersion)
    {

    }

    public static function __enable__()
    {

    }

    public static function __disable__()
    {

    }

}