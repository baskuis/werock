<?php

/**
 * Admin Language Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class LanguageAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';

    public $template = 'maptabledefault';

    public $decorator = 'admindecorator';
    
    public $mapTableObject = 'werock_language';
    
    /** @var MapTableContextObject $MapTableContextObject */
    public $MapTableContextObject = null;

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus()
    {

        $menus = array();

        //create menu section in admin nav
        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('adminlanguage');
        $CoreMenuObject->setName(CoreLanguage::get('admin:language:link:name'));
        $CoreMenuObject->setHref('/admin/system/language');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:language:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(60);
        $CoreMenuObject->setParentId('System');
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

        $route = new CoreControllerObject('/admin/system/language', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
        $route->setType(CoreControllerObject::TYPE_ACTION);
        array_push($routes, $route);

        return $routes;

    }

    /**
     * UserRegisterAction the model
     *
     * @return mixed
     */
    public function register()
    {

        /** @var UserEntitlementService $UserEntitlementManager */
        $UserEntitlementManager = CoreLogic::getService('UserEntitlementService');
        $access = $UserEntitlementManager->hasObjectEntitlement('maptable.object.' . $this->mapTableObject, 'maptable:view');
        if(!$access) {
            CoreMenu::hide(AdminModule::ADMIN_NAV_ID, 'adminlanguage');
        }

    }

    /**
     * Build the model
     *
     * @param $params
     * @return mixed
     */
    public function build($params)
    {

        $this->title = CoreLanguage::get('admin.language.page.title');

        /** @var MapTableContextObject $MapTableContextObject */
        $MapTableContextObject = CoreLogic::getObject('MapTableContextObject');
        $MapTableContextObject->setTable($this->mapTableObject);
        $MapTableContextObject->setTemplate('maptabledefault'); //optional when using default value
        $MapTableContextObject->setListingTemplate('maptablelisting'); //optional when using default value

        $this->MapTableContextObject = $MapTableContextObject;

        /** @var MapTableService $MapTableManager */
        $MapTableManager = CoreLogic::getService('MapTableService');

        /** @var MapTableActionModifierObject $MapTableActionModifierObject */
        $MapTableActionModifierObject = $MapTableManager->fromContext($MapTableContextObject);

        /**
         * Set data on this action accordingly
         */
        $this->template = $MapTableActionModifierObject->getTemplate();

    }

}