<?php

class GoogleToolsAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';

    public $description = '';

    public $decorator = 'admindecorator';

    public $template = 'googletoolsadmin';

    /**
     * Form
     * @var FormUI $GoogleToolsConfigurationForm
     */
    public $GoogleToolsConfigurationForm;

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus()
    {
        $menus = array();

        //create menu section in admin nav
        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('admingoogletools');
        $CoreMenuObject->setName(CoreLanguage::get('admin:system:gtools:link:name'));
        $CoreMenuObject->setHref('/admin/system/gtools');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:system:gtools:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(30);
        $CoreMenuObject->setParentId(AdminModule::ADMIN_NAV_ID_SYSTEM);
        $CoreMenuObject->setTarget(AdminModule::ADMIN_NAV_ID);
        array_push($menus, $CoreMenuObject);

        return $menus;
    }

    /**
     * Get routes for this action
     *
     * @return array
     */
    public function getRoutes()
    {
        $routes = array();
        array_push($routes, CoreControllerObject::buildAction('/admin/system/gtools', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));
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

        //set title and description
        $this->title = CoreLanguage::get('admin:gtools:title');
        $this->description = CoreLanguage::get('admin:gtools:description');

        /**
         * Set form data
         * @var FormUI $this->LoginForm
         */
        $this->GoogleToolsConfigurationForm = CoreForm::register('admingoogletools', array('method' => 'post', 'action' => ''));

        /**
         * Webmaster Tools
         */
        $FormField = new FormField();
        $FormField->setName('webmastertools');
        $FormField->setLabel('Webmaster Tools Key');
        $FormField->setHelper('Set the Webmaster Tools key');
        $FormField->setCondition('');
        $FormField->setValue(CoreModule::getProp('GoogleToolsModule', GoogleToolsModule::GOOGLE_WEBMASTER_VERIFICATION_KEY, CoreStringUtils::EMPTY_STRING));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->GoogleToolsConfigurationForm->addField($FormField);

        /**
         * Analytics
         */
        $FormField = new FormField();
        $FormField->setName('analytics');
        $FormField->setLabel('Analytics Key');
        $FormField->setHelper('Enter your Google Analytics key');
        $FormField->setCondition('');
        $FormField->setValue(CoreModule::getProp('GoogleToolsModule', GoogleToolsModule::GOOGLE_ANALYTICS_KEY, CoreStringUtils::EMPTY_STRING));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->GoogleToolsConfigurationForm->addField($FormField);

        /**
         * Google Adwords
         */
        $FormField = new FormField();
        $FormField->setName('googleadwords');
        $FormField->setLabel('Adwords Key');
        $FormField->setHelper('Enter your Google Adwords key');
        $FormField->setCondition('');
        $FormField->setValue(CoreModule::getProp('GoogleToolsModule', GoogleToolsModule::GOOGLE_ADWORDS_KEY, CoreStringUtils::EMPTY_STRING));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->GoogleToolsConfigurationForm->addField($FormField);

        /**
         * Google Adsense
         */
        $FormField = new FormField();
        $FormField->setName('googleadsense');
        $FormField->setLabel('Adsense Key');
        $FormField->setHelper('Enter your Google Adsense key');
        $FormField->setCondition('');
        $FormField->setValue(CoreModule::getProp('GoogleToolsModule', GoogleToolsModule::GOOGLE_ADSENSE_KEY, CoreStringUtils::EMPTY_STRING));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->GoogleToolsConfigurationForm->addField($FormField);

        /**
         * Create Submit Button
         */
        $FormField = new FormField();
        $FormField->setName('modules_submit');
        $FormField->setLabel(null);
        $FormField->setType('formbuttonslarge');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setCondition(null);
        $FormField->setHelper(null);
        $FormField->setPlaceholder('Save Changes');
        $FormField->setValue('Save');
        $this->GoogleToolsConfigurationForm->addField($FormField);

        //handle submission
        if($this->GoogleToolsConfigurationForm->validFormSubmitted()){
            if($this->GoogleToolsConfigurationForm->validateSubmission()){

                /**
                 * Show notification
                 */
                CoreNotification::set('Updated!', CoreNotification::SUCCESS);

                /**
                 * Store properties
                 */
                CoreModule::setProp('GoogleToolsModule', GoogleToolsModule::GOOGLE_WEBMASTER_VERIFICATION_KEY, $this->GoogleToolsConfigurationForm->grabFieldValue('webmastertools'));
                CoreModule::setProp('GoogleToolsModule', GoogleToolsModule::GOOGLE_ANALYTICS_KEY, $this->GoogleToolsConfigurationForm->grabFieldValue('analytics'));
                CoreModule::setProp('GoogleToolsModule', GoogleToolsModule::GOOGLE_ADSENSE_KEY, $this->GoogleToolsConfigurationForm->grabFieldValue('googleadwords'));
                CoreModule::setProp('GoogleToolsModule', GoogleToolsModule::GOOGLE_ADWORDS_KEY, $this->GoogleToolsConfigurationForm->grabFieldValue('googleadsense'));


            }
        }

    }

}