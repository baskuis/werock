<?php

class EmailAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = 'Smtp Settings';

    public $description = 'Configure Smtp settings for outgoing email.';

    public $decorator = 'admindecorator';

    public $template = 'emailsettingsadmin';

    /** @var FormUI $EmailConfigurationForm */
    public $EmailConfigurationForm;

    public function getMenus()
    {
        $menus = array();

        //create menu section in admin nav
        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('adminemailsettings');
        $CoreMenuObject->setName('Email');
        $CoreMenuObject->setHref('/admin/system/email');
        $CoreMenuObject->setTitle('Email Setttings');
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(30);
        $CoreMenuObject->setParentId(AdminModule::ADMIN_NAV_ID_SYSTEM);
        $CoreMenuObject->setTarget(AdminModule::ADMIN_NAV_ID);
        array_push($menus, $CoreMenuObject);

        return $menus;
    }

    public function getRoutes()
    {
        return CoreArrayUtils::asArray(CoreControllerObject::buildAction('/admin/system/email', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));
    }

    public function register()
    {

    }

    public function build($params)
    {

        //set title and description
        $this->title = CoreLanguage::get('admin:smtp:title');
        $this->description = CoreLanguage::get('admin:smtp:description');

        /**
         * Set form data
         * @var FormUI $this->EmailConfigurationForm
         */
        $this->EmailConfigurationForm = CoreForm::register('admingoogletools', array('method' => 'post', 'action' => ''));

        /**
         * Webmaster Tools
         */
        $FormField = new FormField();
        $FormField->setName('emailhost');
        $FormField->setLabel('Hostname');
        $FormField->setHelper('Set the email hostname');
        $FormField->setCondition('');
        $FormField->setValue(CoreModule::getProp('EmailModule', EmailModule::EMAIL_HOST_PROP, CoreStringUtils::EMPTY_STRING));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->EmailConfigurationForm->addField($FormField);

        /**
         * Analytics
         */
        $FormField = new FormField();
        $FormField->setName('emailport');
        $FormField->setLabel('Port');
        $FormField->setHelper('Set the email port');
        $FormField->setCondition('');
        $FormField->setValue(CoreModule::getProp('EmailModule', EmailModule::EMAIL_PORT_PROP, CoreStringUtils::EMPTY_STRING));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->EmailConfigurationForm->addField($FormField);

        /**
         * Google Adwords
         */
        $FormField = new FormField();
        $FormField->setName('emailuser');
        $FormField->setLabel('Username');
        $FormField->setHelper('Set the outgoing email username');
        $FormField->setCondition('');
        $FormField->setValue(CoreModule::getProp('EmailModule', EmailModule::EMAIL_USER_PROP, CoreStringUtils::EMPTY_STRING));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->EmailConfigurationForm->addField($FormField);

        /**
         * Google Adsense
         */
        $FormField = new FormField();
        $FormField->setName('emailpass');
        $FormField->setLabel('Password');
        $FormField->setHelper('Set the outgoing email password');
        $FormField->setCondition('');
        $FormField->setValue(CoreModule::getProp('EmailModule', EmailModule::EMAIL_PASS_PROD, CoreStringUtils::EMPTY_STRING));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputpassword');
        $this->EmailConfigurationForm->addField($FormField);

        /**
         * Create Submit Button
         */
        $FormField = new FormField();
        $FormField->setName('email_submit');
        $FormField->setLabel(null);
        $FormField->setType('formbuttonslarge');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setCondition(null);
        $FormField->setHelper(null);
        $FormField->setPlaceholder('Save Changes');
        $FormField->setValue('Save');
        $this->EmailConfigurationForm->addField($FormField);

        //handle submission
        if($this->EmailConfigurationForm->validFormSubmitted()){
            if($this->EmailConfigurationForm->validateSubmission()){

                /**
                 * Show notification
                 */
                CoreNotification::set('Updated!', CoreNotification::SUCCESS);

                /**
                 * Store properties
                 */
                CoreModule::setProp('EmailModule', EmailModule::EMAIL_HOST_PROP, $this->EmailConfigurationForm->grabFieldValue('emailhost'));
                CoreModule::setProp('EmailModule', EmailModule::EMAIL_PORT_PROP, $this->EmailConfigurationForm->grabFieldValue('emailport'));
                CoreModule::setProp('EmailModule', EmailModule::EMAIL_USER_PROP, $this->EmailConfigurationForm->grabFieldValue('emailuser'));
                CoreModule::setProp('EmailModule', EmailModule::EMAIL_PASS_PROD, $this->EmailConfigurationForm->grabFieldValue('emailpass'));


            }
        }

    }

}