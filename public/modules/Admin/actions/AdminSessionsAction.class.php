<?php

class AdminSessionsAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';

    public $description = '';

    public $decorator = 'admindecorator';

    public $template = 'adminsessions';

    /**
     * Login form
     * @var FormUI $SessionsForm
     */
    private $SessionsForm;

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
        $CoreMenuObject->setId('adminsessions');
        $CoreMenuObject->setName(CoreLanguage::get('admin:system:sessions:link:name'));
        $CoreMenuObject->setHref('/admin/system/sessions');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:system:sessions:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(20);
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

        $route = new CoreControllerObject('/admin/system/sessions', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
        $route->setType(CoreControllerObject::TYPE_ACTION);
        array_push($routes, $route);

        return $routes;

    }

    public function register(){

    }

    public function build($params = array()){

        //set title and description
        $this->title = CoreLanguage::get('admin:sessions:title');
        $this->description = CoreLanguage::get('admin:sessions:description');

        /**
         * Set form data
         * @var FormUI $this->LoginForm
         */
        $this->SessionsForm = CoreForm::register('adminsessions', array('method' => 'post', 'action' => ''));

        /**
         * Session duration
         */
        $FormField = new FormField();
        $FormField->setName('sessionduration');
        $FormField->setLabel('Session Duration');
        $FormField->setHelper('Set maximum session duration');
        $FormField->setCondition('/^[0-9]{2,8}$/');
        $FormField->setValue(CoreProp::get(CoreSessionHandler::MAX_SESSION_LIFETIME_PROP_KEY, 600));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->SessionsForm->addField($FormField);

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
        $this->SessionsForm->addField($FormField);

        //handle submission
        if($this->SessionsForm->validFormSubmitted()){
            if($this->SessionsForm->validateSubmission()){

                /**
                 * Show notification
                 */
                CoreNotification::set('Updated!', CoreNotification::SUCCESS);

                /**
                 * Store property
                 */
                CoreProp::set(CoreSessionHandler::MAX_SESSION_LIFETIME_PROP_KEY, $this->SessionsForm->grabFieldValue('sessionduration'));

            }
        }

    }

}