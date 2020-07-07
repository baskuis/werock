<?php

/**
 * User Write Messages Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MessageWriteAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    /**
     * Set main template
     */
    public $template = "writemessage";

    /**
     * Define register fields
     */
    public $fields = array();

    /**
     * Form reference
     *
     * @var string
     */
    private $form_reference = 'writeMessage';

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

        $route = new CoreControllerObject('/messages/write', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
        $route->setType(CoreControllerObject::TYPE_ACTION);
        array_push($routes, $route);

        return $routes;

    }

    /**
     * UserRegisterAction Action
     */
    public function register(){

    }

    /**
     * Catch params
     */
    public function build($params = array()){

        /** @var UserService $UserManager */
        $UserManager = CoreLogic::getService('UserService');

        /** @var UserObject $CurrentUser */
        $CurrentUser = $UserManager->getCurrentUser();

        /**
         * Throw exception when not authenticated
         */
        if(!$CurrentUser){
            throw new UserUnauthorizedException();
        }

        /**
         * Define register form fields
         */
        $this->fields[] = array(
            'name' => 'addressees',
            'label' => 'Addressee',
            'type' => 'formfieldpeoplepicker',
            'template' => 'formfieldflexible',
            'condition' => '*',
            'placeholder' => 'ie: john.parker',
            'helper' => 'Please enter a valid username',
            'value' => null
        );
        $this->fields[] = array(
            'name' => 'message',
            'label' => 'Message',
            'type' => 'forminputhtml',
            'template' => 'formfieldflexible',
            'condition' => '',
            'helper' => 'Please enter your message',
            'placeholder' => '',
            'value' => null
        );
        $this->fields[] = array(
            'name' => 'message_submit',
            'label' => '',
            'type' => 'forminputsubmit',
            'template' => 'formfieldflexible',
            'placeholder' => '',
            'value' => 'Send Message'
        );

        /**
         * Set form data
         */
        CoreForm::register($this->form_reference, array('method' => 'post', 'action' => ''), $this->fields);

        //handle submission
        if(CoreForm::getForm($this->form_reference)->validFormSubmitted()){

            //validate submission
            if(CoreForm::getForm($this->form_reference)->validateSubmission()){

                /**
                 * @var UserService $UserManager
                 */
                $UserManager = CoreLogic::getService('UserService');

                /**
                 * @var Array $userList
                 */
                $userList = explode(',', CoreForm::getForm($this->form_reference)->grabFieldValue('addressees'));

                //receiving users
                $ReceivingUsers = array();

                /**
                 * Build users
                 */
                foreach($userList as $userid){

                    //suppress issue with null userid
                    if(empty($userid)){ continue; }

                    //get user
                    $UserObject = $UserManager->getUser($userid);

                    //test user
                    if(!$UserObject){
                        throw new Exception();
                    }

                    /**
                     * Stack to receiving users
                     */
                    array_push($ReceivingUsers, $UserObject);

                }

                /**
                 * Create message object
                 */
                $MessageObject = new MessageObject();
                $MessageObject->setBody(CoreForm::getForm($this->form_reference)->grabFieldValue('message'));

                /**
                 * @var MessageService $MessageManager
                 */
                $MessageManager = CoreLogic::getService('MessageService');
                $sent = $MessageManager->send($MessageObject, $ReceivingUsers);

                //set appropriate messaging
                if($sent){
                    CoreNotification::set('Message sent!', CoreNotification::SUCCESS);
                }else{
                    CoreNotification::set('Message not sent!', CoreNotification::ERROR);
                }

            }

        }

    }

}