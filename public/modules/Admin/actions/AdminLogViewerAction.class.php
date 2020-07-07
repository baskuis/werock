<?php

class AdminLogViewerAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $template = 'adminlogviewer';

    public $decorator = 'admindecorator';

    public function getMenus(){
        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('adminlogviewer');
        $CoreMenuObject->setName(CoreLanguage::get('admin:adminlogviewer:link:name'));
        $CoreMenuObject->setHref('/admin/tools/adminlogviewer');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:adminlogviewer:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(9999);
        $CoreMenuObject->setParentId(AdminModule::ADMIN_NAV_ID_TOOLS);
        $CoreMenuObject->setTarget(AdminModule::ADMIN_NAV_ID);
        return CoreArrayUtils::asArray($CoreMenuObject);
    }

    public function getRoutes(){
        return CoreArrayUtils::asArray(CoreControllerObject::buildAction('/admin/tools/adminlogviewer', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));
    }

    public function register(){ }

    public function build($params){
        $this->title = CoreLanguage::get('admin.adminlogviewer.page.title');
        $this->description = CoreLanguage::get('admin.adminlogviewer.page.description');
    }

}