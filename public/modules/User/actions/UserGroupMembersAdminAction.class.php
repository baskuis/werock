<?php

/**
 * Admin User Group Members Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserGroupMembersAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';

    public $template = 'maptabledefault';

    public $decorator = 'admindecorator';

    /** @var MapTableContextObject $MapTableContextObject */
    public $MapTableContextObject;

    /** @var bool $suppressEntitlementPicker Do not entitlement picker */
    public $suppressEntitlementPicker = true;

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus()
    {

        $menus = array();

        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('usergroupmembers');
        $CoreMenuObject->setName(CoreLanguage::get('admin:usergroupmembers:link:name'));
        $CoreMenuObject->setHref('/admin/people/groupmembers');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:usergroupmembers:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(61);
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

        array_push($routes, CoreControllerObject::buildAction('/admin/people/groupmembers', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));

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
        $access = $UserEntitlementManager->userHasEntitlement(CoreUser::getUser(), $UserEntitlementManager->getEntitlement(UserModule::ENTITLEMENT_MANAGE_USERS));
        if(!$access){
            CoreMenu::hide(AdminModule::ADMIN_NAV_ID, 'usergroupmembers');
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

        $this->title = CoreLanguage::get('admin.usergroupmembers.page.title');
        $this->description = CoreLanguage::get('admin.usergroupmembers.page.description');

        /** @var MapTableContextObject $MapTableContextObject */
        $MapTableContextObject = CoreLogic::getObject('MapTableContextObject');
        $MapTableContextObject->setTable('werock_group_members');
        $MapTableContextObject->setTemplate('maptabledefault');
        $MapTableContextObject->setListingTemplate('maptablelisting');

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