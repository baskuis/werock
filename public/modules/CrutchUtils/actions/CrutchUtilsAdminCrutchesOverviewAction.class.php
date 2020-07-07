<?php

/**
 * Admin CrutchUtils Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CrutchUtilsAdminCrutchesOverviewAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';

    public $description = '';

    public $template = 'admincrutchesoverview';

    public $decorator = 'admindecorator';

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus()
    {

        $menus = array();

        //add admin link
        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('admincrutchesoverview');
        $CoreMenuObject->setName(CoreLanguage::get('admin:system:crutchesoverview:link:name'));
        $CoreMenuObject->setHref('/admin/system/crutchesoverview');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:system:crutchesoverview:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(20);
        $CoreMenuObject->setParentId(AdminModule::ADMIN_NAV_ID_SYSTEM);
        $CoreMenuObject->setTarget(AdminModule::ADMIN_NAV_ID);
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

        $route = new CoreControllerObject('/admin/system/crutchesoverview', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
        $route->setType(CoreControllerObject::TYPE_ACTION);
        array_push($routes, $route);

        return $routes;

    }

    public function register(){

    }

    public function build($params = array()){

        $this->title = CoreLanguage::get('admin:system:crutches:overview:page:title');
        $this->description = CoreLanguage::get('admin:system:crutches:overview:page:description');

    }

}