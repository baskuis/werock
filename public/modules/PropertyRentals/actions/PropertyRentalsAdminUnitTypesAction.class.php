<?php

class PropertyRentalsAdminUnitTypesAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

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
        $CoreMenuObject->setId('rentalunittypes');
        $CoreMenuObject->setName(CoreLanguage::get('admin:property.rentals.unittypes.link.name'));
        $CoreMenuObject->setHref('/admin/data/unittypes');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:property.rentals.unittypes.link.title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(70);
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

        $route = new CoreControllerObject('/admin/data/unittypes', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
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

        $this->title = CoreLanguage::get('admin.property.rentals.unittypes.page.title');
        $this->description = CoreLanguage::get('admin.property.rentals.unittypes.page.description');

        /** @var MapTableContextObject $MapTableContextObject */
        $MapTableContextObject = CoreLogic::getObject('MapTableContextObject');
        $MapTableContextObject->setTable('werock_building_unit_types');
        $MapTableContextObject->setTemplate('maptabledefault');
        $MapTableContextObject->setListingTemplate('maptablelisting');

        /** @var MapTableAssociatedTableDescriptionObject $MapTableAssociatedTableDescriptionObject */
        $MapTableAssociatedTableDescriptionObject = CoreLogic::getObject('MapTableAssociatedTableDescriptionObject');
        $MapTableAssociatedTableDescriptionObject->setTable('werock_buildings_to_unit_types');
        $MapTableAssociatedTableDescriptionObject->setTitle('Unit Types');
        $MapTableAssociatedTableDescriptionObject->setDescription('Associate unit types');

        /** add associated table */
        $MapTableContextObject->addAssociatedTable($MapTableAssociatedTableDescriptionObject);

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