<?php

/**
 * Search suggestions module
 * Allows for embeddable smart search input field
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class SearchSuggestionsModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Search Suggestions Module';
    public static $description = 'Adds ability to embed search suggestions';
    public static $version = '1.0.6';
    public static $dependencies = array(
        'User' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'Admin' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        )
    );

    /** @var SearchSuggestionsService $SearchSuggestionsManager */
    private static $SearchSuggestionsService;

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
    public static function getRoutes(){

        $routes = array();

        $route = new CoreControllerObject('/^\/api\/v1\/searchSuggestions\/([^\/]+)\/([^\/]+)\/?$/i', __CLASS__, 'suggest', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::GROUP_API);
        $route->setType(CoreControllerObject::TYPE_METHOD);
        array_push($routes, $route);

        $route = new CoreControllerObject('/^\/api\/v1\/searchSuggestions\/([^\/]+)\/?$/i', __CLASS__, 'suggest', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::GROUP_API);
        $route->setType(CoreControllerObject::TYPE_METHOD);
        array_push($routes, $route);

        array_push($routes, CoreControllerObject::buildApi('/api/v1/popularSuggestions', __CLASS__, 'popular', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));

        array_push($routes, CoreControllerObject::buildApi('/api/v1/recentSuggestions', __CLASS__, 'recent', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));

        return $routes;

    }

    /**
     * Init
     */
    public static function __init__(){
        self::$SearchSuggestionsService = CoreLogic::getService('SearchSuggestionsService');
    }

    /**
     * Suggest
     *
     * @param array $params
     */
    public static function suggest($params = array()){

        /** @var string $urn */
        $urn = isset($params[1]) ? urldecode($params[1]) : null;

        /** @var string $search */
        $search = isset($params[2]) ? urldecode($params[2]) : null;

        /** get suggestions */
        $suggestions = self::$SearchSuggestionsService->suggest($search, $urn);

        /** suggestions */
        CoreApi::setData('suggestions', $suggestions);

    }

    /**
     * Popular
     *
     * @param array $params
     */
    public static function popular($params = array()){

        /** @var string $urn */
        $urn = isset($_GET['urn']) ? urldecode($_GET['urn']) : null;

        /** @var int $limit */
        $limit = isset($_GET['limit']) && (int) $_GET['limit'] < 20 ? (int) $_GET['limit'] : 20;

        /** get popular */
        $popular = self::$SearchSuggestionsService->popular($urn, $limit);

        /** suggestions */
        CoreApi::setData('popular', $popular);

    }

    /**
     * Recent
     *
     * @param array $params
     */
    public static function recent($params = array()){

        /** @var string $urn */
        $urn = isset($_GET['urn']) ? urldecode($_GET['urn']) : null;

        /** @var int $limit */
        $limit = isset($_GET['limit']) && (int) $_GET['limit'] < 20 ? (int) $_GET['limit'] : 20;

        /** get popular */
        $recent = self::$SearchSuggestionsService->recent($urn, $limit);

        /** suggestions */
        CoreApi::setData('recent', $recent);

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