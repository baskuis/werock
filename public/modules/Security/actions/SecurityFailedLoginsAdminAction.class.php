<?php

/**
 * Media Failed Logins Action
 * built by maptable
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class SecurityFailedLoginsAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';

    public $template = 'maptabledefault';

    public $decorator = 'admindecorator';

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus()
    {

        $menus = array();

        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('failedlogins');
        $CoreMenuObject->setName(CoreLanguage::get('admin:failedlogins:link:name'));
        $CoreMenuObject->setHref('/admin/failedlogins');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:failedlogins:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(60);
        $CoreMenuObject->setParentId(AdminModule::ADMIN_NAV_ID_PEOPLE);
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

        array_push($routes, CoreControllerObject::buildAction('/admin/failedlogins', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));

        return $routes;

    }

    /**
     * UserRegisterAction the model
     *
     * @return mixed
     */
    public function register()
    {

    }

    /**
     * Build the model
     *
     * @param $params
     * @return mixed
     */
    public function build($params)
    {

        $this->title = CoreLanguage::get('admin.failedlogins.page.title');

        /** @var MapTableContextObject $MapTableContextObject */
        $MapTableContextObject = CoreLogic::getObject('MapTableContextObject');
        $MapTableContextObject->setTable('werock_failed_logins');
        $MapTableContextObject->setTemplate('maptabledefault'); //optional when using default value
        $MapTableContextObject->setListingTemplate('maptablelisting'); //optional when using default value

        $this->MapTableContextObject = $MapTableContextObject;

        /** @var MapTableService $MapTableService */
        $MapTableService = CoreLogic::getService('MapTableService');

        /** @var MapTableActionModifierObject $MapTableActionModifierObject */
        $MapTableActionModifierObject = $MapTableService->fromContext($MapTableContextObject);

        /**
         * Set data on this action accordingly
         */
        $this->template = $MapTableActionModifierObject->getTemplate();

    }

}