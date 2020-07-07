<?php

/**
 * User Closing Page Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserClosingPageAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';

    public $template = 'userclosingpage';

    /** @var UserService $UserService */
    private $UserService;

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus(){ }

    /**
     * Get routes for this action
     *
     * @return array
     */
    public function getRoutes(){
        $routes = array();
        array_push($routes, CoreControllerObject::buildAction('/closeme', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));
        return $routes;
    }

    /**
     * UserRegisterAction the model
     *
     * @return mixed
     */
    public function register(){ }

    /**
     * Build the model
     *
     * @param $params
     * @return mixed
     */
    public function build($params){
        $action = isset($_GET['action']) ? $_GET['action'] : null;
        switch($action){
            case 'logout':
                $this->UserService = CoreLogic::getService('UserService');
                $this->UserService->logout();
                break;
            default:
                //no action
                break;
        }
    }

}