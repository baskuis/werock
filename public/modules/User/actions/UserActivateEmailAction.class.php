<?php

class UserActivateEmailAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $template = 'useractivateemail';

    public $title;

    public $description;

    /** @var bool $activated */
    public $activated = false;

    /** @var UserService $UserService */
    private $UserService;

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus(){

    }

    /**
     * Get routes for this action
     *
     * @return array
     */
    public function getRoutes(){
        return CoreArrayUtils::asArray(CoreControllerObject::buildAction('/^\/email\/activate\/([^\/]+)\/?$/i', __CLASS__, CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::REQUEST_GET));
    }

    /**
     * Register the model
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

        $this->UserService = CoreLogic::getService('UserService');

        /**
         * Activate Email
         */
        $this->activated = $this->UserService->activateEmail(isset($params[1]) ? $params[1] : null);

    }

}