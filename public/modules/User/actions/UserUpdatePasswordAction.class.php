<?php

/**
 * User Update Password Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserUpdatePasswordAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $decorator = '';
    public $template = 'userpasswordupdate';

    public $title = 'Change Password';
    public $description = 'Change your password';

    /** @var FormUi $PasswordUpdateForm */
    public $PasswordUpdateForm;

    /** @var UserService $UserService */
    public $UserService;

    public function getMenus() {

    }

    public function getRoutes() {
        return CoreArrayUtils::asArray(CoreControllerObject::buildAction('/user/password/update', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));
    }

    public function register() {

    }

    public function build($params) {

        $this->UserService = CoreLogic::getService('UserService');

        /** make sure user is logged in */
        if(!$this->UserService->activeUser()){
            throw new UserUnauthorizedException('You need to be logged in!');
        }

        $this->PasswordUpdateForm = CoreForm::register('updatepassword', array('method' => 'post', 'action' => ''));

        $FormField = new FormField();
        $FormField->setName('current');
        $FormField->setType('forminputpassword');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setLabel('Current Password');
        $FormField->setHelper('Enter your current password');
        $this->PasswordUpdateForm->addField($FormField);

        $FormField = new FormField();
        $FormField->setName('password');
        $FormField->setType('forminputpassword');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setLabel('New Password');
        $FormField->setHelper('Enter your new password');
        $this->PasswordUpdateForm->addField($FormField);

        $FormField = new FormField();
        $FormField->setName('password_repeat');
        $FormField->setType('forminputpassword');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setLabel('Repeat New Password');
        $FormField->setHelper('Repeat your new password');
        $this->PasswordUpdateForm->addField($FormField);

        $FormField = new FormField();
        $FormField->setName('update_submit');
        $FormField->setType('forminputsubmit');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setValue('Update Password');
        $this->PasswordUpdateForm->addField($FormField);

        if($this->PasswordUpdateForm->validFormSubmitted()){
            if($this->PasswordUpdateForm->validateSubmission()){

                /**
                 * Collect values
                 */
                $currentPassword = $this->PasswordUpdateForm->grabFieldValue('current');
                $newPassword = $this->PasswordUpdateForm->grabFieldValue('password');

                /**
                 * Update user password
                 */
                $this->UserService->updatePassword($currentPassword, $newPassword);

            }
        }
    }


}