<?php

/**
 * User Request Password Action
 *
 * PHP version 7
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserRequestPasswordAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = 'Request Password Reset';
    public $description = 'Request a password reset, we will send you a reset password link';

    public $decorator = '';
    public $template = 'userpasswordrequest';

    /** @var UserService $UserService */
    private $UserService;

    /** @var FormUi $PasswordRequestForm */
    public $PasswordRequestForm;

    public $linkRequested = false;

    public function getMenus(){
        //not in any menu
    }

    public function getRoutes(){
        return CoreArrayUtils::asArray(CoreControllerObject::buildAction('/user/password/request', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));
    }

    public function register(){
        //not needed
    }

    public function build($params){

        $this->UserService = CoreLogic::getService('UserService');

        /** if the user is logged in lets redirect them to the landing page */
        if($this->UserService->activeUser()){
            CoreHeaders::setRedirect('/');
        }

        $this->PasswordRequestForm = CoreForm::register('resetpassword', array('method' => 'post', 'action' => ''));

        $FormField = new FormField();
        $FormField->setName('email');
        $FormField->setType('forminputemail');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setPlaceholder('ie: my@email.com');
        $FormField->setLabel('Your Email');
        $FormField->setHelper('Please enter the email you signed up with');
        $this->PasswordRequestForm->addField($FormField);

        $FormField = new FormField();
        $FormField->setName('altcaptcha');
        $FormField->setType('forminputaltcaptcha');
        $FormField->setTemplate('formfieldnaked');
        $this->PasswordRequestForm->addField($FormField);

        $FormField = new FormField();
        $FormField->setName('update_submit');
        $FormField->setType('forminputsubmit');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setValue('Reset Password');
        $this->PasswordRequestForm->addField($FormField);

        if($this->PasswordRequestForm->validFormSubmitted()){
            if($this->PasswordRequestForm->validateSubmission()){

                /**
                 * Collect values
                 */
                $email = $this->PasswordRequestForm->grabFieldValue('email');

                /**
                 * Update user password
                 */
                if(false !== $this->UserService->requestPasswordReset($email)){
                    $this->linkRequested = true;
                }

            }
        }

    }

}