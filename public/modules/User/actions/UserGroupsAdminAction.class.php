<?php

/**
 * Admin User Group Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserGroupsAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

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
        $CoreMenuObject->setId('usergroups');
        $CoreMenuObject->setName(CoreLanguage::get('admin:usergroups:link:name'));
        $CoreMenuObject->setHref('/admin/people/groups');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:usergroups:link:title'));
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

        array_push($routes, CoreControllerObject::buildAction('/admin/people/groups', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));

        return $routes;

    }

    /**
     * UserRegisterAction the model
     *
     * @return mixed
     */
    public function register() {

        /** @var UserEntitlementService $UserEntitlementManager */
        $UserEntitlementManager = CoreLogic::getService('UserEntitlementService');
        $access = $UserEntitlementManager->userHasEntitlement(CoreUser::getUser(), $UserEntitlementManager->getEntitlement(UserModule::ENTITLEMENT_MANAGE_USERS));
        if(!$access){
            CoreMenu::hide(AdminModule::ADMIN_NAV_ID, 'usergroups');
        }

    }

    /**
     * Build the model
     *
     * @param $params
     * @return mixed
     */
    public function build($params) {

        $this->title = CoreLanguage::get('admin.usergroups.page.title');
        $this->description = CoreLanguage::get('admin.usergroups.page.description');

        /** @var MapTableContextObject $MapTableContextObject */
        $MapTableContextObject = CoreLogic::getObject('MapTableContextObject');
        $MapTableContextObject->setTable('werock_groups');
        $MapTableContextObject->setTemplate('maptabledefault');
        $MapTableContextObject->setListingTemplate('maptablelisting');
        $MapTableContextObject->setListingHeaderTemplate('maptablelistingheader');

        /** @var MapTableRelatedTableDescriptionObject $MapTableRelatedTableDescriptionObject */
        $MapTableRelatedTableDescriptionObject = CoreLogic::getObject('MapTableRelatedTableDescriptionObject');
        $MapTableRelatedTableDescriptionObject->setTable('werock_group_members');
        $MapTableRelatedTableDescriptionObject->setTitle('Group Members');
        $MapTableRelatedTableDescriptionObject->setDescription('Group members');
        $MapTableRelatedTableDescriptionObject->setInputTemplate('maptablerelatedtable');
        $MapTableRelatedTableDescriptionObject->setFieldTemplate('formfieldnaked');

        /** add related field description */
        $MapTableContextObject->addRelatedTable($MapTableRelatedTableDescriptionObject);

        /** @var MapTableAssociatedTableDescriptionObject $MapTableAssociatedTableDescriptionObject */
        $MapTableAssociatedTableDescriptionObject = CoreLogic::getObject('MapTableAssociatedTableDescriptionObject');
        $MapTableAssociatedTableDescriptionObject->setTable('werock_group_to_media');
        $MapTableAssociatedTableDescriptionObject->setTitle('Associated Media');
        $MapTableAssociatedTableDescriptionObject->setDescription('Pick associated media');
        $MapTableAssociatedTableDescriptionObject->setInputTemplate('maptableassociatedtable');
        $MapTableAssociatedTableDescriptionObject->setFieldTemplate('formfieldnaked');

        /** add associated table description */
        $MapTableContextObject->addAssociatedTable($MapTableAssociatedTableDescriptionObject);

        /** @var set action reference MapTableContextObject */
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