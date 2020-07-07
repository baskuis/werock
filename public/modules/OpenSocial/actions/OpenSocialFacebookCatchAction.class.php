<?php

class OpenSocialFacebookCatchAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $template = 'opensocialfacebookcatch';

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

        $route = new CoreControllerObject('/opensocial/connect/facebook/catch', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
        $route->setType(CoreControllerObject::TYPE_ACTION);
        array_push($routes, $route);

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

    }

}