<?php

/**
 * WeRock System Performance Module
 * This module adds performance improvements
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class PerformanceModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Performance Module';
    public static $description = 'Adds system performance utilities';
    public static $version = '1.0.0';
    public static $dependencies = array(
        'Intelligence' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        )
    );

    /** @var IntelligenceService $IntelligenceService */
    private static $IntelligenceService;

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

    }

    /**
     * Init
     */
    public static function __init__(){
        self::$IntelligenceService = CoreLogic::getService('IntelligenceService');
    }

    /**
     * Sort routes by popularity
     */
    public static function orderRoutesByPopularity(){

        if(false === ($controllers = CoreCache::getCache(CoreController::CACHE_ROUTES_KEY, true, CoreController::CACHE_ROUTES_NS, false))) {

            /** @var array $controllers */
            $controllers = CoreController::getControllers();

        }

        /** @var IntelligenceDataRequestObject $IntelligenceDataRequestObject */
        $IntelligenceDataRequestObject = CoreLogic::getObject('IntelligenceDataRequestObject');
        $IntelligenceDataRequestObject->setKey(IntelligenceService::REQUEST_URI);
        $IntelligenceDataRequestObject->setFrom(strtotime('-3 days'));
        $IntelligenceDataRequestObject->setTo(time());
        $IntelligenceDataRequestObject->setLimit(100);

        /** @var array $records */
        $records = self::$IntelligenceService->getData($IntelligenceDataRequestObject);

        if (!isset($records[0])) {
            CoreLog::error('No intelligence data available to optimize routes');
        }

        /** @var IntelligenceDataResponseObject $entry */
        foreach ($records[0]->getValues() as $entry) {
            /** @var CoreControllerObject $CoreControllerObject */
            foreach ($controllers as &$CoreControllerObject) {
                if (CoreController::match($entry['text'], $CoreControllerObject)) {
                    if (!isset($CoreControllerObject->performanceModulePopularity)) {
                        $CoreControllerObject->performanceModulePopularity = (int) $entry['count'];
                    } else {
                        $CoreControllerObject->performanceModulePopularity += (int) $entry['count'];
                    }
                }
            }
        }

        /** Sort them by popularity */
        usort($controllers, function ($a, $b) {
            if (!isset($a->performanceModulePopularity)) $a->performanceModulePopularity = 0;
            if (!isset($b->performanceModulePopularity)) $b->performanceModulePopularity = 0;
            if ($a->performanceModulePopularity < $b->performanceModulePopularity) return 1;
            if ($a->performanceModulePopularity > $b->performanceModulePopularity) return -1;
            return 0;
        });

        /**
         * Save a reference
         */
        CoreCache::saveCache(CoreController::CACHE_ROUTES_KEY, $controllers, 86400, true, CoreController::CACHE_ROUTES_NS, false);

        /** Set the sorted list */
        CoreController::setControllers($controllers);

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