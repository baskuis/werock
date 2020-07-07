<?php

/**
 * Media Module
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MediaModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Media Module';
    public static $description = 'Enables Media';
    public static $version = '1.0.0.4';
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
        )
    );

    /**
     * Default image extensions
     */
    const DEFAULT_IMAGE_EXTENSIONS = 'image/jpeg,image/gif,image/png,.jpg,.png,.gif';

    /** @var MapTableService $MapTableService */
    private static $MapTableService;

    /** @var MediaService $MediaService */
    private static $MediaService;

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
    public static function getRoutes()
    {

        $routes = array();

        /**
         * Create
         */
        array_push($routes, CoreControllerObject::buildApi('/api/v1/media/create', __CLASS__, 'create', CoreControllerObject::MATCH_TYPE_STRING));

        /**
         * Read
         */
        array_push($routes, CoreControllerObject::buildApi('/^\/api\/v1\/media\/([0-9]+)\/?$/i', __CLASS__, 'get', CoreControllerObject::MATCH_TYPE_REGEX));

        /**
         * Update
         */
        array_push($routes, CoreControllerObject::buildApi('/^\/api\/v1\/media\/([0-9]+)\/update\/?$/i', __CLASS__, 'update', CoreControllerObject::MATCH_TYPE_REGEX));

        /**
         * Delete
         */
        array_push($routes, CoreControllerObject::buildApi('/^\/api\/v1\/media\/([0-9]+)\/delete\/?$/i', __CLASS__, 'delete', CoreControllerObject::MATCH_TYPE_REGEX));

        /**
         * Stream
         */
        array_push($routes, CoreControllerObject::buildStream('/^\/stream\/v1\/media\/([0-9]+)\/?$/i', __CLASS__, 'stream', CoreControllerObject::MATCH_TYPE_REGEX));

        /**
         * Download
         */
        array_push($routes, CoreControllerObject::buildStream('/^\/stream\/v1\/media\/([0-9]+)\/download\/?$/i', __CLASS__, 'download', CoreControllerObject::MATCH_TYPE_REGEX));

        return $routes;

    }

    /**
     * UserRegisterAction listeners, toMethod
     */
    public static function __init__(){

        self::$MapTableService = CoreLogic::getService('MapTableService');
        self::$MediaService = CoreLogic::getService('MediaService');

        //load definition
        CoreLogic::getObject('MediaObject', false);

        //add mappings
        self::addMediaMappings();

    }

    /**
     * Add mapping for blob data
     */
    private static function addMediaMappings(){

        /**
         * Image field
         */
        /** @var MapTableMapColumnObject $MapTableMapColumnObject */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('image');
        $MapTableMapColumnObject->setAppendMatch(array('image'));
        $MapTableMapColumnObject->setDataTypeMatch('/^int/i');
        $MapTableMapColumnObject->setInputTemplate('forminputimage');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        $MapTableMapColumnObject->setFormFieldModifier(function(FormField $FormField, MapTableContextObject $MapTableContextObject){

            /**
             * Get the existing extensions
             * if they are not specified - lets set the default
             * image allowed extensions
             */
            $existingExtensions = $FormField->getExtensions();
            if(empty($existingExtensions)) $FormField->setExtensions(self::DEFAULT_IMAGE_EXTENSIONS);

            /**
             * Since this is a int field
             * we can only allow a single image
             */
            $FormField->setData(array(
                'singular' => true,
                'multiple' => false
            ));

            /**
             * Now we need to return the modified
             * instance of FormField
             */
            return $FormField;

        });
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Images field
         */
        /** @var MapTableMapColumnObject $MapTableMapColumnObject */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('images');
        $MapTableMapColumnObject->setAppendMatch(array('images'));
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar/i');
        $MapTableMapColumnObject->setInputTemplate('forminputimage');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        $MapTableMapColumnObject->setFormFieldModifier(function(FormField $FormField, MapTableContextObject $MapTableContextObject){

            /**
             * Get the existing extensions
             * if they are not specified - lets set the default
             * image allowed extensions
             */
            $existingExtensions = $FormField->getExtensions();
            if(empty($existingExtensions)) $FormField->setExtensions(self::DEFAULT_IMAGE_EXTENSIONS);

            /**
             * Since this is a int field
             * we can only allow a single image
             */
            $FormField->setData(array(
                'singular' => false,
                'multiple' => true
            ));

            /**
             * Now we need to return the modified
             * instance of FormField
             */
            return $FormField;

        });
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Blob data
         */
        /** @var MapTableMapColumnObject $MapTableMapColumnObject */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('data');
        $MapTableMapColumnObject->setAppendMatch('data');
        $MapTableMapColumnObject->setDataTypeMatch('/blob$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputblob');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

    }

    /**
     * Get MediaObject
     *
     * @param null $params
     */
    public static function get($params = null){

        /** @var MediaRequestObject $MediaRequestObject */
        $MediaRequestObject = CoreLogic::getObject('MediaRequestObject');
        $MediaRequestObject->setId((isset($params[1]) ? $params[1] : null));

        /** @var MediaObject $MediaObject */
        $MediaObject = self::$MediaService->get($MediaRequestObject);

        /**
         * Set response data
         */
        CoreApi::setData('media', $MediaObject);

    }

    /**
     * Create MediaObject
     *
     * @param null $params
     */
    public static function create($params = null){

        /** @var MediaCreateObject $MediaCreateObject */
        $MediaCreateObject = CoreLogic::getObject('MediaCreateObject');
        $MediaCreateObject->setData((isset($_POST['data']) ? $_POST['data'] : null));
        $MediaCreateObject->setFilename((isset($_POST['fileName']) ? $_POST['fileName'] : null));
        $MediaCreateObject->setType((isset($_POST['type']) ? $_POST['type'] : null));

        /**
         * Set type to extension if nothing else is available
         */
        if(empty($MediaCreateObject->type)){
            $MediaCreateObject->setType(pathinfo($MediaCreateObject->getFilename(), PATHINFO_EXTENSION));
        }

        /** @var $MediaObject $MediaObject */
        $MediaObject = self::$MediaService->create($MediaCreateObject);

        /**
         * Set response data
         */
        CoreApi::setData('uniqueID', (isset($_POST['uniqueID']) ? $_POST['uniqueID'] : null));
        CoreApi::setData('media', $MediaObject);

    }

    /**
     * Update media item
     *
     * @param null $params
     */
    public static function update($params = null){

    }

    /**
     * Delete media item
     *
     * @param null $params
     */
    public static function delete($params = null){

    }

    /**
     * Download media
     *
     * @param null $params
     */
    public static function download($params = null){

        /** @var MediaRequestObject $MediaRequestObject */
        $MediaRequestObject = CoreLogic::getObject('MediaRequestObject');
        $MediaRequestObject->setId((isset($params[1]) ? $params[1] : null));

        /** @var MediaStreamObject $MediaStreamObject */
        $MediaStreamObject = self::$MediaService->stream($MediaRequestObject);

        /**
         * Set header
         * and response data
         */
        CoreHeaders::add('Content-type', $MediaStreamObject->getType());
        CoreHeaders::add('Content-Length', $MediaStreamObject->getSize());
        CoreHeaders::add('Content-Disposition', 'attachment; filename=' . $MediaStreamObject->getName());
        CoreResponse::setBody($MediaStreamObject->getData());

    }

    /**
     * Stream media
     *
     * @param null $params
     */
    public static function stream($params = null){

        /** @var MediaRequestObject $MediaRequestObject */
        $MediaRequestObject = CoreLogic::getObject('MediaRequestObject');
        $MediaRequestObject->setId((isset($params[1]) ? $params[1] : null));
        $MediaRequestObject->setWidth(isset($_GET['width']) ? (int) $_GET['width'] : false);
        $MediaRequestObject->setHeight(isset($_GET['height']) ? (int) $_GET['height'] : false);

        /** @var MediaStreamObject $MediaStreamObject */
        $MediaStreamObject = self::$MediaService->stream($MediaRequestObject);
        if(!empty($MediaStreamObject)) {

            /** Save cache for modified since look-ups */
            CoreCache::saveCache('streamCache:' . CoreController::$matchUrl, strtotime($MediaStreamObject->getModified()), 30 * 86400, true);

            /**
             * Set headers
             * and response data
             */
            CoreHeaders::add('Pragma', 'public');
            CoreHeaders::add('Content-type', 'image/' . str_ireplace('image/', '', $MediaStreamObject->getType()));
            CoreHeaders::add('Expires', 'Thu, 15 Apr 2020 20:00:00 GMT');
            CoreHeaders::add('Cache-Control', 'max-age=' . (strtotime('Thu, 15 Apr 2020 20:00:00 GMT') - time()) . ', public');
            CoreHeaders::add('Last-Modified', gmdate('D, d M Y H:i:s', strtotime($MediaStreamObject->getModified())) . ' GMT');
            CoreResponse::setBody($MediaStreamObject->getData());

        }

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