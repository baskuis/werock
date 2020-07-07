<?php

/**
 * Core api
 *
 * PHP version 5
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreApi {

    use CoreInterceptorTrait;
    use ClassReflectionTrait;

    //api have prefix
    const API_HAVE_PREFIX = 'have__';

    const TARGET_ENCODING = 'UTF-8';

    /** keys and constants */
    const NOTIFICATIONS = 'notifications';
    const PAGINATION = 'pagination';

    /**
     * @var array
     */
    public static $_PUT = array();

    /**
     * @var array
     */
    public static $_DELETE = array();

    /**
     * HTTP Status
     *
     * @var string $status
     */
    public static $status = null;

    /**
     * String return
     */
    public static $output = null;

    /**
     * Data
     */
    public static $data = array();

    /**
     * Get payload
     *
     * @return mixed
     */
    public static function getPayload(){
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Set data on response
     * Allow interceptors
     *
     * @param null $key
     * @param null $value
     * @return bool
     */
    public static function _setData($key = null, $value = null){

        //check for key
        if(empty($key)){
            CoreLog::error('Cannot set data with null key');
            return false;
        }

        //set data
        self::$data[$key] = $value;

        //return
        return true;

    }

    /**
     * Set object on response
     *
     * @param mixed $object
     * @return bool
     */
    public static function set($object){
        if(is_object($object)) {
            $className = get_class($object);
            if ($className) {
                return self::setData($className, $object);
            }
        }
    }

    /**
     * Create $_PUT and $_DELETE
     */
    public static function populatePutDelete(){
        switch($_SERVER['REQUEST_METHOD']){
            case !strcasecmp($_SERVER['REQUEST_METHOD'], 'DELETE'):
                parse_str(file_get_contents('php://input'), self::$_DELETE );
                break;

            case !strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT'):
                parse_str(file_get_contents('php://input'), self::$_PUT);
                break;
        }
    }

    /**
     * Page notifications
     */
    public static $notifications = array();

    /**
     * Builds Notifications on rendering object
     */
    private static function buildNotifications(){

        //set notifications
        $notificationTypes = CoreNotification::getTypes();

        //get all types
        foreach($notificationTypes as $type){
            self::$data[self::NOTIFICATIONS][$type]	= CoreNotification::getNotifications($type);
        }

    }

    /**
     * Build pagination response
     */
    private static function buildPagination(){

        /**
         * Load in pagination when relevant
         *
         */
        if(CorePagination::isRanQuery()) {
            self::$data[self::PAGINATION] = CorePagination::getPaginationObject();
        }

    }

    /**
     * Render template and output to browser
     */
    public static function execute(){
        $response = array();
        CoreApi::buildNotifications();
        CoreApi::buildPagination();
        if(!empty(CoreApi::$data)){
            $response = CoreJsonUtils::prepareObject(CoreApi::$data, 6, array('SimplePie', 'members')); //TODO: Find elegant solution to issues with SimplePie and members
        }
        if(isset($response['status'])) CoreLog::error('Please avoid using protected \'status\' in response');
        $response['status'] = self::$status;
        self::$output = json_encode($response);
    }

    /**
     * Get output
     * @return null
     */
    public static function _getOutput(){
        if(CoreRender::$noBody) return null;
        return self::$output;
    }

}