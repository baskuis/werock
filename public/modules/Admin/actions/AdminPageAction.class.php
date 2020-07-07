<?php

class AdminPageAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    /**
     * Admin home template
     * @var string
     */
    public $template = 'adminhome';

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

    }

    /**
     * Get routes for this action
     *
     * @return array
     */
    public function getRoutes()
    {
        $routes = array();
        array_push($routes, CoreControllerObject::buildAction('/admin', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));
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