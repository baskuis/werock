<?php

/**
 * Amazon module
 * Allows for integration of the amazon api
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class AmazonModule implements CoreModuleInterface {

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
        ),
        'SearchSuggestions' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        )
    );

    /**
     * Relevant
     * constants
     */
    const AMAZON_SETTINGS_COUNTRY_KEY = 'amazon.settings.country';
    const AMAZON_SETTINGS_ACCESS_KEY_KEY = 'amazon.settings.access.key';
    const AMAZON_SETTINGS_SECRET_KEY_KEY = 'amazon.settings.secret.key';
    const AMAZON_SETTINGS_ASSOCIATE_TAG_KEY = 'amazon.settings.associate.tag';
    const AMAZON_SUGGESTIONS_KEY = 'amazonSearchSuggestions';

    /**
     * @var AmazonService $AmazonService
     */
    private static $AmazonService;

    /**
     * Init script
     *
     */
    public static function __init__()
    {
        self::$AmazonService = CoreLogic::getService('AmazonService');
    }

    /**
     * Get listeners
     *
     * @return mixed
     */
    public static function getListeners()
    {

    }

    /**
     * Get interceptors
     *
     * @return mixed
     */
    public static function getInterceptors()
    {

    }

    /**
     * Get menus
     *
     * @return mixed
     */
    public static function getMenus()
    {

    }

    /**
     * Get routes
     *
     * @return mixed
     */
    public static function getRoutes()
    {
        $routes = array();
        array_push($routes, CoreControllerObject::buildApi('/api/v1/amazon/search', __CLASS__, 'search', CoreControllerObject::MATCH_TYPE_STRING));
        array_push($routes, CoreControllerObject::buildApi('/api/v1/amazon/lookup', __CLASS__, 'lookup', CoreControllerObject::MATCH_TYPE_STRING));
        array_push($routes, CoreControllerObject::buildApi('/api/v1/amazon/indexes', __CLASS__, 'indexes', CoreControllerObject::MATCH_TYPE_STRING));
        return $routes;
    }

    /**
     * Search amazon
     */
    public static function search(){
        $query = isset($_GET['query']) ? $_GET['query'] : false;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'relevance';
        $category = isset($_GET['category']) ? $_GET['category'] : 'All';
        $tag = isset($_GET['tag']) ? $_GET['tag'] : null;
        if($page < 1){ $page = 1; }
        $items = self::$AmazonService->search($query, $page, $sort, $category, $tag);
        CoreApi::setData('items', $items);
    }

    /**
     * Lookup item
     */
    public static function lookup(){
        $asin = isset($_GET['asin']) ? $_GET['asin'] : false;
        $lookup = self::$AmazonService->lookup($asin);
        CoreApi::setData('lookup', $lookup);
    }

    /**
     * Search indexes
     */
    public static function indexes(){
        CoreApi::setData('indexes', self::$AmazonService->getSearchIndexes());
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