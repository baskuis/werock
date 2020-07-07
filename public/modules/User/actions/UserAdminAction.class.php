<?php

/**
 * Admin People
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
class UserAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';

    public $template = 'maptabledefault';

    public $decorator = 'admindecorator';

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
        $CoreMenuObject->setId('adminpeople');
        $CoreMenuObject->setName(CoreLanguage::get('admin:people:link:name'));
        $CoreMenuObject->setHref('/admin/people');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:people:link:title'));
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

        array_push($routes, CoreControllerObject::buildAction('/admin/people', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));

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
        $access = $UserEntitlementManager->hasEntitlement(UserModule::ENTITLEMENT_MANAGE_USERS);
        if(!$access){
            CoreMenu::hide(AdminModule::ADMIN_NAV_ID, 'adminpeople');
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

        $this->title = CoreLanguage::get('admin:people:link:title');

        /** @var MapTableContextObject $MapTableContextObject */
        $MapTableContextObject = CoreLogic::getObject('MapTableContextObject');
        $MapTableContextObject->setTable('werock_users');
        $MapTableContextObject->setTemplate('maptabledefault'); //optional when using default value
        $MapTableContextObject->setListingTemplate('maptablelisting'); //optional when using default value

        /** @var MapTableRelatedTableDescriptionObject $MapTableRelatedTableDescriptionObject */
        $MapTableRelatedTableDescriptionObject = CoreLogic::getObject('MapTableRelatedTableDescriptionObject');
        $MapTableRelatedTableDescriptionObject->setTable('werock_user_emails');
        $MapTableRelatedTableDescriptionObject->setTitle('User Emails');
        $MapTableRelatedTableDescriptionObject->setDescription('Email addresses belonging to this user');
        $MapTableContextObject->addRelatedTable($MapTableRelatedTableDescriptionObject);

        /** @var MapTableRelatedTableDescriptionObject $MapTableRelatedTableDescriptionObject */
        $MapTableRelatedTableDescriptionObject = CoreLogic::getObject('MapTableRelatedTableDescriptionObject');
        $MapTableRelatedTableDescriptionObject->setTable('werock_group_members');
        $MapTableRelatedTableDescriptionObject->setTitle('Group Members');
        $MapTableRelatedTableDescriptionObject->setDescription('Group members');
        $MapTableContextObject->addRelatedTable($MapTableRelatedTableDescriptionObject);

        /** @var MapTableRelatedTableDescriptionObject $MapTableRelatedTableDescriptionObject */
        $MapTableRelatedTableDescriptionObject = CoreLogic::getObject('MapTableRelatedTableDescriptionObject');
        $MapTableRelatedTableDescriptionObject->setTable('werock_user_data_values');
        $MapTableRelatedTableDescriptionObject->setTitle('User Data');
        $MapTableRelatedTableDescriptionObject->setDescription('Data associated with this user');
        $MapTableContextObject->addRelatedTable($MapTableRelatedTableDescriptionObject);

        /** @var MapTableRelatedTableDescriptionObject $MapTableRelatedTableDescriptionObject */
        $MapTableRelatedTableDescriptionObject = CoreLogic::getObject('MapTableRelatedTableDescriptionObject');
        $MapTableRelatedTableDescriptionObject->setTable('werock_user_invites');
        $MapTableRelatedTableDescriptionObject->setTitle('User Invites');
        $MapTableRelatedTableDescriptionObject->setDescription('Invites created by this user');
        $MapTableContextObject->addRelatedTable($MapTableRelatedTableDescriptionObject);

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