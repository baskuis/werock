<?php

class EmailResubscribeAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $template = 'emailresubscribe';
    public $title = 'Re-Subscribe';
    public $description = 'Re-Subscribe to future emails';

    /** @var EmailService $EmailService */
    private $EmailService;

    public function getMenus(){

    }

    public function getRoutes(){
        return CoreArrayUtils::asArray(CoreControllerObject::buildAction('/email/resubscribe', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));
    }

    public function register(){

    }

    public function build($params){

        $this->EmailService = CoreLogic::getService('EmailService');

        $email = isset($_GET['email']) ? $_GET['email'] : false;
        $token = isset($_GET['token']) ? $_GET['token'] : false;

        if($this->EmailService->resubscribe($email, $token)){
            CoreNotification::set('Email Re-subscribed', CoreNotification::SUCCESS);
        }

    }

}