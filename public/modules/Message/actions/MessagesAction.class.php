<?php

/**
 * User Messages Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MessagesAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';

    public $description = '';

    /**
     * Set main template
     */
    public $template = "usermessages";

    /**
     * Define register fields
     */
    public $fields = array();

    /**
     * Messsages
     * @var array
     */
    public $messages = array();

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus()
    {

        $menus = array();

        //create menu item in utility nav
        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('UtilityMessages');
        $CoreMenuObject->setName(CoreLanguage::get('messages:link:name'));
        $CoreMenuObject->setHref('/messages');
        $CoreMenuObject->setTitle(CoreLanguage::get('messages:link:title'));
        $CoreMenuObject->setTemplate('utilitynavmessageslink');
        $CoreMenuObject->setTarget(UtilityMenuModule::UTILITY_NAV_ID);
        array_push($menus, $CoreMenuObject);

        return $menus;

    }

    /**
     * Get routes for this action
     *
     * @return array
     */
    public function getRoutes()
    {

        $routes = array();

        $route = new CoreControllerObject('/messages', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
        $route->setType(CoreControllerObject::TYPE_ACTION);
        array_push($routes, $route);

        return $routes;

    }

    /**
     * UserRegisterAction this action
     *
     * @return mixed|void
     */
    public function register(){


    }

    /**
     * Catch params
     */
    public function build($params = array()){

    }

}