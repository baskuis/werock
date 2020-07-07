<?php

class PropertyRentalsAdminBuildingsAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';

    public $template = 'maptabledefault';

    public $decorator = 'admindecorator';

    /** @var MapTableContextObject $MapTableContextObject */
    public $MapTableContextObject;

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus()
    {
        $menus = array();

        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('rentalbuildings');
        $CoreMenuObject->setName(CoreLanguage::get('admin:property.rentals.link.name'));
        $CoreMenuObject->setHref('/admin/data/buildings');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:property.rentals.link.title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(60);
        $CoreMenuObject->setParentId(AdminModule::ADMIN_NAV_ID_DATA);
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

        $route = new CoreControllerObject('/admin/data/buildings', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
        $route->setType(CoreControllerObject::TYPE_ACTION);
        array_push($routes, $route);

        return $routes;
    }

    /**
     * UserRegisterAction the model
     *
     * @return mixed
     */
    public function register(){

    }

    /**
     * Build the model
     *
     * @param $params
     * @return mixed
     */
    public function build($params){

        $this->title = CoreLanguage::get('admin.property.rentals.page.title');
        $this->description = CoreLanguage::get('admin.property.rentals.page.description');

        /** @var MapTableContextObject $MapTableContextObject */
        $MapTableContextObject = CoreLogic::getObject('MapTableContextObject');
        $MapTableContextObject->setTable('werock_buildings');
        $MapTableContextObject->setTemplate('maptabledefault');
        $MapTableContextObject->setListingTemplate('maptablelisting');

        /** @var MapTableAssociatedTableDescriptionObject $MapTableAssociatedTableDescriptionObject */
        $MapTableAssociatedTableDescriptionObject = CoreLogic::getObject('MapTableAssociatedTableDescriptionObject');
        $MapTableAssociatedTableDescriptionObject->setTable('werock_buildings_to_unit_types');
        $MapTableAssociatedTableDescriptionObject->setTitle('Unit Types');
        $MapTableAssociatedTableDescriptionObject->setDescription('Associate unit types');

        /** add associated table */
        $MapTableContextObject->addAssociatedTable($MapTableAssociatedTableDescriptionObject);

        /** @var MapTableRelatedTableDescriptionObject $MapTableRelatedTableDescriptionObject */
        $MapTableRelatedTableDescriptionObject = CoreLogic::getObject('MapTableRelatedTableDescriptionObject');
        $MapTableRelatedTableDescriptionObject->setTable('werock_building_units');
        $MapTableRelatedTableDescriptionObject->setTitle('Building Units');
        $MapTableRelatedTableDescriptionObject->setDescription('Building Units');

        /** add related field description */
        $MapTableContextObject->addRelatedTable($MapTableRelatedTableDescriptionObject);

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