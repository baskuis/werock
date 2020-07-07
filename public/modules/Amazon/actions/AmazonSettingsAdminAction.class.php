<?php

class AmazonSettingsAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';

    public $description = '';

    public $decorator = 'admindecorator';

    public $template = 'amazonsettingsadmin';

    /**
     * @var FormUI $AmazonSettingsFormUI
     */
    public $AmazonSettingsFormUI;

    /**
     * @var AmazonService $AmazonService
     */
    private $AmazonService;

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
        $CoreMenuObject->setId('adminamazonsettings');
        $CoreMenuObject->setName(CoreLanguage::get('admin:system:amazon:settings:link:name'));
        $CoreMenuObject->setHref('/admin/amazon/settings');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:system:amazon:settings:link:title'));
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
        array_push($routes, CoreControllerObject::buildAction('/admin/amazon/settings', __CLASS__ , CoreControllerObject::MATCH_TYPE_STRING));
        return $routes;
    }

    /**
     * Register the model
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
        $this->title = CoreLanguage::get('admin:amazon:settings:title');
        $this->description = CoreLanguage::get('admin:amazon:settings:description');

        /**
         * Get amazon service
         */
        $this->AmazonService = CoreLogic::getService('AmazonService');

        /**
         * Set form data
         * @var FormUI $this->AmazonSettingsFormUI
         */
        $this->AmazonSettingsFormUI = CoreForm::register('adminamazonsettings', array('method' => 'post', 'action' => ''));

        /**
         * Webmaster Tools
         */
        $FormField = new FormField();
        $FormField->setName('amazonaccesskey');
        $FormField->setLabel('Amazon Access Key');
        $FormField->setHelper('Set the Amazon access key');
        $FormField->setCondition('*');
        $FormField->setValue(CoreModule::getProp('AmazonModule', AmazonModule::AMAZON_SETTINGS_ACCESS_KEY_KEY, null));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->AmazonSettingsFormUI->addField($FormField);

        /**
         * Webmaster Tools
         */
        $FormField = new FormField();
        $FormField->setName('amazonsecretkey');
        $FormField->setLabel('Amazon Secret Key');
        $FormField->setHelper('Set the Amazon secret key');
        $FormField->setCondition('*');
        $FormField->setValue(CoreModule::getProp('AmazonModule', AmazonModule::AMAZON_SETTINGS_SECRET_KEY_KEY, null));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->AmazonSettingsFormUI->addField($FormField);

        /**
         * Webmaster Tools
         */
        $FormField = new FormField();
        $FormField->setName('amazonassociatetag');
        $FormField->setLabel('Amazon Associate Tag');
        $FormField->setHelper('Get this value from the affiliate center');
        $FormField->setCondition('*');
        $FormField->setValue(CoreModule::getProp('AmazonModule', AmazonModule::AMAZON_SETTINGS_ASSOCIATE_TAG_KEY, null));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->AmazonSettingsFormUI->addField($FormField);

        /**
         * Webmaster Tools
         */
        $FormField = new FormField();
        $FormField->setName('amazoncountry');
        $FormField->setLabel('Amazon Country');
        $FormField->setHelper('Set this to be the super domain. IE .com for the U.S.A.');
        $FormField->setCondition('*');
        $FormField->setValue(CoreModule::getProp('AmazonModule', AmazonModule::AMAZON_SETTINGS_COUNTRY_KEY, null));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->AmazonSettingsFormUI->addField($FormField);

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
        $this->AmazonSettingsFormUI->addField($FormField);

        //handle submission
        if($this->AmazonSettingsFormUI->validFormSubmitted()){
            if($this->AmazonSettingsFormUI->validateSubmission()){

                /**
                 * Show notification
                 */
                CoreNotification::set('Amazon settings updated!', CoreNotification::SUCCESS);

                /**
                 * Store properties
                 */
                CoreModule::setProp('AmazonModule', AmazonModule::AMAZON_SETTINGS_ACCESS_KEY_KEY, $this->AmazonSettingsFormUI->grabFieldValue('amazonaccesskey'));
                CoreModule::setProp('AmazonModule', AmazonModule::AMAZON_SETTINGS_SECRET_KEY_KEY, $this->AmazonSettingsFormUI->grabFieldValue('amazonsecretkey'));
                CoreModule::setProp('AmazonModule', AmazonModule::AMAZON_SETTINGS_ASSOCIATE_TAG_KEY, $this->AmazonSettingsFormUI->grabFieldValue('amazonassociatetag'));
                CoreModule::setProp('AmazonModule', AmazonModule::AMAZON_SETTINGS_COUNTRY_KEY, $this->AmazonSettingsFormUI->grabFieldValue('amazoncountry'));

                /**
                 * Run through a request to catch
                 * potential issues
                 */
                $this->AmazonService->search('movie', 1);

            }
        }

    }

}