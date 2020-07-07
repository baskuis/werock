<?php

/**
 * Message Module
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MessageModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Message Module';
    public static $description = 'Enables Messages';
    public static $version = '1.0.0';
    public static $dependencies = array(
        'User' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'UtilityMenu' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'Admin' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'Editor' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        )
    );

    /**
     * Referable constants
     */
    const UTILITY_NAV_MESSAGES_ID = 'UtilityMessages';
    const ADMIN_NAV_MESSAGES_ID = 'AdminMessagesSettings';

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
         * Action class routes
         * TODO: Migrate to action classes
         */
        array_push($routes, CoreControllerObject::buildMethod('/messages', __CLASS__, 'messages', CoreControllerObject::MATCH_TYPE_STRING));
        array_push($routes, CoreControllerObject::buildMethod('/messages/write', __CLASS__, 'writeMessage', CoreControllerObject::MATCH_TYPE_STRING));

        /**
         * Read
         */
        array_push($routes, CoreControllerObject::buildApi('/^\/api\/v1\/message\/([0-9]+)\/?$/i', __CLASS__, 'getMessage', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::REQUEST_GET));

        /**
         * List
         */
        array_push($routes, CoreControllerObject::buildApi('/^\/api\/v1\/messages\/?$/i', __CLASS__, 'getMessages', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::REQUEST_GET));

        return $routes;

    }

    /**
     * UserRegisterAction listeners, toMethod
     */
    public static function __init__(){

    }

    /**
     * Render write message
     * @param null $params
     */
    public static function writeMessage($params = null){

        /**
         * @var MessageWriteAction $WriteMessageAction
         */
        $WriteMessageAction = CoreLogic::getAction('MessageWriteAction', $params);
        $WriteMessageAction->setTitle(CoreLanguage::get('writemessage:page:title'));
        $WriteMessageAction->setDescription(CoreLanguage::get('writemessage:page:description'));

        //build
        $WriteMessageAction->build(array());

        //execute the action
        $WriteMessageAction->execute();

    }

    /**
     * Render messages
     * @param null $params
     */
    public static function messages($params = null){

        /**
         * @var MessagesAction $MessagesAction
         */
        $MessagesAction = CoreLogic::getAction('MessagesAction', $params);
        $MessagesAction->setTitle(CoreLanguage::get('messages:page:title'));
        $MessagesAction->setDescription(CoreLanguage::get('messages:page:description'));

        //build
        $MessagesAction->build(array());

        //execute the action
        $MessagesAction->execute();

    }

    /**
     * Get messages
     *
     * @param null $params
     */
    public static function getMessages($params = null){

        /**
         * @var UserService $UserManager
         */
        $UserManager = CoreLogic::getService('UserService');
        $UserObject = $UserManager->getCurrentUser();

        /**
         * @var MessageService $MessageManager
         */
        $MessageManager = CoreLogic::getService('MessageService');

        /**
         * Get messages
         */
        $messages = $MessageManager->getMessages($UserObject);

        //set data
        CoreApi::setData('messages', $messages);

    }

    /**
     * Get Message
     * @param null $params
     */
    public static function getMessage($params = null){

         /**
         * @var MessageService $MessageManager
         */
        $MessageManager = CoreLogic::getService('MessageService');
        $MessageObject = $MessageManager->getMessage(isset($params[1]) ? (int)$params[1] : null);

        //set data
        CoreApi::setData('message', $MessageObject);

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