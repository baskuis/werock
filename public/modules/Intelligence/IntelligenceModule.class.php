<?php

/**
 * Intelligence Module
 * Allows for tracking of data for analytical purposes
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class IntelligenceModule implements CoreModuleInterface {
	
	/**
	 * Module description
	 */
	public static $name = 'Intelligence Module';
	public static $description = 'Allow intelligence gathering';
	public static $version = '1.0.0.6';
	public static $dependencies = array(
        'Form' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'MapTable' => array(
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

        $listeners = array();

        /**
         * Persist the intelligence stack
         */
        array_push($listeners, new CoreObserverObject(CoreInit::EVENT_ON_UNLOAD, __CLASS__, 'storeIntelligenceStack'));

        /**
         * Store visitor
         * information
         */
        array_push($listeners, new CoreObserverObject(CoreVisitor::EVENT_VISITOR_CREATED, __CLASS__, 'createdVisitor'));

        /**
         * Store map table
         * events
         */
        array_push($listeners, new CoreObserverObject('MAPTABLE:EVENTS:RECORD:DELETED', __CLASS__, 'maptableRecordDeleted'));
        array_push($listeners, new CoreObserverObject('MAPTABLE:EVENTS:RECORD:INSERTED', __CLASS__, 'maptableRecordInserted'));
        array_push($listeners, new CoreObserverObject('MAPTABLE:EVENTS:RECORD:UPDATED', __CLASS__, 'maptableRecordUpdated'));

        /**
         * Store action information
         */
        array_push($listeners, new CoreObserverObject(CoreController::CONTROLLER_EVENT_ACTION_BEFORE_EXECUTE, __CLASS__, 'viewedAction'));

        /**
         * 404 listener
         */
        array_push($listeners, new CoreObserverObject(CoreController::CONTROLLER_EVENT_RENDER_NOT_FOUND, __CLASS__, 'unableToRouteRequest'));

        return $listeners;

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
         * Add visitor data
         */
        array_push($interceptors, new CoreInterceptorObject(CoreVisitor::getClassName(), 'getVisitor', __CLASS__, 'getVisitor', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));

        /**
         * Inject widgets into maptable view
         */
        array_push($interceptors, new CoreInterceptorObject('MapTableService', 'fromContext', __CLASS__, 'generateWidgets', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));

        return $interceptors;

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

        /**
         * Intelligence data
         */
        array_push($routes, CoreControllerObject::buildApi('/^\/api\/v1\/intelligence\/([^\/]+)\/([0-9]+)\/([0-9]+)\/([0-9]+)\/?$/i', __CLASS__, 'getIntelligenceData', CoreControllerObject::MATCH_TYPE_REGEX));

        /**
         * Map-Table records
         */
        array_push($routes, CoreControllerObject::buildApi('/^\/api\/v1\/intelligence\/records\/([^\/]+)\/([0-9]+)\/([0-9]+)\/([0-9]+)\/?$/i', __CLASS__, 'getRecordsAdded', CoreControllerObject::MATCH_TYPE_REGEX));

        return $routes;
    }

    /**
	 * UserRegisterAction listeners, toMethod
	 */	
	public static function __init__(){

        self::$IntelligenceService = CoreLogic::getService('IntelligenceService');

	}

    /**
     * Generate widgets
     *
     * @param MapTableActionModifierObject $MapTableActionModifierObject
     * @param $params
     * @return MapTableActionModifierObject
     * @throws Exception
     */
    public static function generateWidgets(MapTableActionModifierObject $MapTableActionModifierObject, $params) {

        $widgets = array();

        /**
         * Build widgets and make available
         */
        if(get_class($params[0]) == MapTableContextObject::class) {

            /** @var MapTableContextObject $MapTableContextObject */
            $MapTableContextObject = $params[0];

            if(empty($MapTableContextObject->action)){

                /** @var IntelligenceWidgetObject $monthlyWidget */
                $monthlyWidget = CoreLogic::getObject('IntelligenceWidgetObject');
                $monthlyWidget->setLabel('Activity');
                $monthlyWidget->setKey('maptable:' . $MapTableContextObject->getTable());
                $monthlyWidget->setStart(strtotime('-30 days'));
                $monthlyWidget->setEnd(time());
                $monthlyWidget->setInterval(24 * 3600);
                $monthlyWidget->setCanEdit(true);
                $monthlyWidget->setUniqueID('maptablemonthly');
                $monthlyWidget->setIsLineChart(true);
                $monthlyWidget->setTemplate('googlechartswidget');
                array_push($widgets, $monthlyWidget);

                /** @var IntelligenceWidgetObject $monthlyCountWidget */
                $monthlyCountWidget = CoreLogic::getObject('IntelligenceWidgetObject');
                $monthlyCountWidget->setLabel('Records');
                $monthlyCountWidget->setKey($MapTableContextObject->getTable());
                $monthlyCountWidget->setStart(strtotime('-30 days'));
                $monthlyCountWidget->setEnd(time());
                $monthlyCountWidget->setInterval(24 * 3600);
                $monthlyCountWidget->setCanEdit(true);
                $monthlyCountWidget->setUniqueID('maptablecountmonthly');
                $monthlyCountWidget->setIsMapTable(true);
                $monthlyCountWidget->setTemplate('googlechartswidget');
                array_push($widgets, $monthlyCountWidget);

            }

        }

        CoreTemplate::setData('maptablelisting', 'widgets', $widgets);

        return $MapTableActionModifierObject;

    }

	/**
	 * Store intelligence stack
	 */
	public static function storeIntelligenceStack(){

        //ignore client
        if(CoreSecUtils::isCli()) return;

		//store visitor machine, nav and location details
        self::$IntelligenceService->stackVisitorMachineDetails();
        self::$IntelligenceService->stackNavigationDetails();
        self::$IntelligenceService->stackVisitorLocation();

		//store intelligence
        self::$IntelligenceService->insertIntelligenceStack();
		
	}

    /**
     * When a visitor is created - lets store the visitor data
     */
    public static function createdVisitor(){

        /** @var IntelligenceBrowserDetailsObject $IntelligenceBrowserDetailsObject */
        $IntelligenceBrowserDetailsObject = self::$IntelligenceService->getBrowserDetails();

        /**
         * Store visitor data
         * this allows for later reference
         */
        CoreVisitor::setData('platform', $IntelligenceBrowserDetailsObject->getPlatform());
        CoreVisitor::setData('browser', $IntelligenceBrowserDetailsObject->getBrowser());
        CoreVisitor::setData('version', $IntelligenceBrowserDetailsObject->getVersion());
        CoreVisitor::setData('useragent', $IntelligenceBrowserDetailsObject->getUserAgent());
        CoreVisitor::setData('mobile', $IntelligenceBrowserDetailsObject->getIsMobile());

    }

    /**
     * Inject extra information about the visitor
     *
     * @param CoreVisitorObject $CoreVisitorObject
     * @return CoreVisitorObject
     */
    public static function getVisitor($CoreVisitorObject){

        /**
         * If a visitor is found
         * lets inject the data into the CoreVisitorObject
         * for consumption in associated templates
         */
        if($CoreVisitorObject){
            $CoreVisitorObject->addData('platform', CoreVisitor::getData('platform', $CoreVisitorObject->getId()));
            $CoreVisitorObject->addData('browser', CoreVisitor::getData('browser', $CoreVisitorObject->getId()));
            $CoreVisitorObject->addData('version', CoreVisitor::getData('version', $CoreVisitorObject->getId()));
            $CoreVisitorObject->addData('useragent', CoreVisitor::getData('useragent', $CoreVisitorObject->getId()));
            $CoreVisitorObject->addData('mobile', CoreVisitor::getData('mobile', $CoreVisitorObject->getId()));
        }

        /**
         * Need to pass back the requested object
         */
        return $CoreVisitorObject;

    }

    /**
     * Handle recording maptable delete record
     *
     * @param null $payload
     */
    public static function maptableRecordDeleted($payload = null){

        /** @var MapTableContextObject $payload */
        if(get_class($payload) == MapTableContextObject::class){

            /** Store Intelligence */
            self::$IntelligenceService->addToIntelligenceStack('maptable:' . $payload->getTable(), 'record deleted');

        }
    }

    /**
     * Handle recording maptable insert record
     *
     * @param null $payload
     */
    public static function maptableRecordInserted($payload = null){

        /** @var MapTableContextObject $payload */
        if(get_class($payload) == MapTableContextObject::class){

            /** Store Intelligence */
            self::$IntelligenceService->addToIntelligenceStack('maptable:' . $payload->getTable(), 'record inserted');

        }

    }

    /**
     * Handle recording maptable update record
     *
     * @param null $payload
     */
    public static function maptableRecordUpdated($payload = null){

        /** @var MapTableContextObject $payload */
        if(get_class($payload) == MapTableContextObject::class){

            /** Store Intelligence */
            self::$IntelligenceService->addToIntelligenceStack('maptable:' . $payload->getTable(), 'record updated');

        }

    }

    /**
     * Track action
     *
     * @param null $payload
     */
    public static function viewedAction($payload = null){

        /** @var CoreRenderTemplate $payload */
        if(get_parent_class($payload) == 'CoreRenderTemplate'){
            self::$IntelligenceService->addToIntelligenceStack('action view', 'action view');
            self::$IntelligenceService->addToIntelligenceStack('action', get_class($payload));
        }

    }

    /**
     * Return records added in date range
     *
     * @param array $params
     * @throws Exception
     */
    public static function getRecordsAdded($params = array()){

        /** Assertion */
        if(sizeof($params) != 5) throw new Exception('Invalid request');

        /** @var IntelligenceTableRangeRequestObject $IntelligenceTableRangeRequestObject */
        $IntelligenceTableRangeRequestObject = CoreLogic::getObject('IntelligenceTableRangeRequestObject');
        $IntelligenceTableRangeRequestObject->setTable(urldecode($params[1]));
        $IntelligenceTableRangeRequestObject->setFrom($params[2]);
        $IntelligenceTableRangeRequestObject->setTo($params[3]);
        $IntelligenceTableRangeRequestObject->setInterval($params[4]);

        /** @var array $data */
        $data = self::$IntelligenceService->getRecordsAdded($IntelligenceTableRangeRequestObject);

        /** Set data on response */
        CoreApi::setData('intelligence', $data);

    }

    /**
     * Return data for rendering of widget
     *
     * @param array $params
     * @throws Exception
     */
    public static function getIntelligenceData($params = array()){

        /** Assertion */
        if(sizeof($params) != 5) throw new Exception('Invalid request');

        /** @var IntelligenceDataRequestObject $IntelligenceDataRequestObject */
        $IntelligenceDataRequestObject = CoreLogic::getObject('IntelligenceDataRequestObject');
        $IntelligenceDataRequestObject->setKey(urldecode($params[1]));
        $IntelligenceDataRequestObject->setFrom($params[2]);
        $IntelligenceDataRequestObject->setTo($params[3]);
        $IntelligenceDataRequestObject->setInterval($params[4]);
        $IntelligenceDataRequestObject->setLimit(20);
        $IntelligenceDataRequestObject->setCrawler(false);

        /** @var array $data */
        $data = self::$IntelligenceService->getData($IntelligenceDataRequestObject);

        /** Set data on response */
        CoreApi::setData('intelligence', $data);

    }

    /**
     * Unable to route request
     *
     */
    public static function unableToRouteRequest(){

        /** Store Intelligence */
        self::$IntelligenceService->addToIntelligenceStack('page not found', CoreController::$matchUrl);

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