<?php

class TemplateUtilsOverviewAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $decorator = 'admindecorator';

    public $template = 'admintemplatesoverview';

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus()
    {

        $menus = array();

        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('admintemplatespreview');
        $CoreMenuObject->setName(CoreLanguage::get('admin:templates:overview:link:name'));
        $CoreMenuObject->setHref('/admin/tools/templates');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:templates:overview:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(60);
        $CoreMenuObject->setParentId(AdminModule::ADMIN_NAV_ID_TOOLS);
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

        array_push($routes, CoreControllerObject::buildAction('/admin/tools/templates', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));

        return $routes;

    }

    public function register(){

    }

    public function build($params = array()){

    }

}