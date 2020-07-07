<?php


/**
 * Shows phpinfo()
 *
 * PHP version 7
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class AdminPhpInfoAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    /**
     * Admin home template
     * @var string
     */
    public $template = 'adminphpinfo';

    /**
     * Admin home decorator
     */
    public $decorator = 'admindecorator';

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
        $CoreMenuObject->setId('adminphpinfo');
        $CoreMenuObject->setName('PHP Info');
        $CoreMenuObject->setHref('/admin/system/info');
        $CoreMenuObject->setTitle('PHP Info');
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
        array_push($routes, CoreControllerObject::buildAction('/admin/system/info', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));
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
     * Catch params
     */
    public function build($params = array()){

    }
}