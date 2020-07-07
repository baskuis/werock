<?php

class MessageSettingsAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = "Message Settings";

    public $description = "";

    public $template = "adminmessagesettings";

    public $decorator = "admindecorator";

    public $settingForm = null;

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus()
    {

        $menus = array();

        /**
         * Add Admin links/pages
         */
        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('AdminMessagesSettings');
        $CoreMenuObject->setName(CoreLanguage::get('admin:system:messages:link:name'));
        $CoreMenuObject->setHref('/admin/system/messages');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:system:messages:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(10);
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

        $route = new CoreControllerObject('/admin/system/messages', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
        $route->setType(CoreControllerObject::TYPE_ACTION);
        array_push($routes, $route);

        return $routes;

    }

    public function register(){

    }

    public function build($params = null){

        /* @var FormUI $this->settingForm */
        $this->settingForm = CoreForm::register('messagessettings', array('action' => '', 'method' => 'post'));

        /**
         * How many messages
         */
        $FormField = new FormField();
        $FormField->setName('showMessages');
        $FormField->setType('forminputtext');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setHelper('How many message should be shown?');
        $FormField->setValue(CoreModule::getProp('MessageModule', 'show:messages', 20));
        $FormField->setCondition('/^[0-9]+$/');
        $FormField->setLabel('Show Messages');
        $this->settingForm->addField($FormField);

        /**
         * Submit
         */
        $FormField = new FormField();
        $FormField->setName('showmessagesubmit');
        $FormField->setType('formbuttonlarge');
        $FormField->setTemplate('formfieldnaked');
        $FormField->setPlaceholder('Save Changes');
        $this->settingForm->addField($FormField);

        //handle submission
        if($this->settingForm->validFormSubmitted()){
            if($this->settingForm->validateSubmission()){

                /**
                 * Update property
                 */
                CoreModule::setProp('MessageModule', 'show:messages', $this->settingForm->grabFieldValue('showMessages'));

                /**
                 * Set notification
                 */
                CoreNotification::set('Updated!', CoreNotification::SUCCESS);

                /**
                 * Invalidate getmessages caches
                 */
                CoreCache::invalidateNamespace('getmessages');

            }
        }

    }

}