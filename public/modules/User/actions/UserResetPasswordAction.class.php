<?php

/**
 * User Reset Password Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserResetPasswordAction extends CoreRenderTemplate implements CoreRenderTemplateInterface{

    public $decorator = '';
    public $template = 'userpasswordreset';

    public $title = 'Reset Password';
    public $description = 'Reset your password';

    /** @var FormUi $PasswordResetForm */
    public $PasswordResetForm;

    /** @var UserService $UserService */
    public $UserService;

    public $passwordReset = false;

    public function getMenus(){
        //not in menu
    }

    public function getRoutes(){
        return CoreArrayUtils::asArray(CoreControllerObject::buildAction('/user/password/reset', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));
    }

    public function register(){
        //not needed
    }

    public function build($params){

        $this->UserService = CoreLogic::getService('UserService');

        /** make sure user is logged in */
        if($this->UserService->activeUser()){
            CoreHeaders::setRedirect('/');
        }

        /**
         * Get key
         */
        $key = isset($_GET['key']) ? $_GET['key'] : false;

        /**
         * Check the key
         */
        $this->UserService->resetKeyValid($key);

        $this->PasswordResetForm = CoreForm::register('resetpassword', array('method' => 'post', 'action' => ''));

        $FormField = new FormField();
        $FormField->setName('password');
        $FormField->setType('forminputpassword');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setLabel('New Password');
        $FormField->setHelper('Enter your new password');
        $this->PasswordResetForm->addField($FormField);

        $FormField = new FormField();
        $FormField->setName('password_repeat');
        $FormField->setType('forminputpassword');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setLabel('Repeat New Password');
        $FormField->setHelper('Repeat your new password');
        $this->PasswordResetForm->addField($FormField);

        $FormField = new FormField();
        $FormField->setName('altcaptcha');
        $FormField->setType('forminputaltcaptcha');
        $FormField->setTemplate('formfieldnaked');
        $this->PasswordResetForm->addField($FormField);

        $FormField = new FormField();
        $FormField->setName('update_submit');
        $FormField->setType('forminputsubmit');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setValue('Reset Password');
        $this->PasswordResetForm->addField($FormField);

        if($this->PasswordResetForm->validFormSubmitted()){
            if($this->PasswordResetForm->validateSubmission()){

                /**
                 * Collect values
                 */
                $newPassword = $this->PasswordResetForm->grabFieldValue('password');

                /**
                 * Reset user password
                 */
                if(false !== $this->UserService->resetPassword($key, $newPassword)){
                    $this->passwordReset = true;
                }

            }
        }

    }

}