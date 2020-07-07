<?php

class OpenSocialFacebookAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = 'Facebook App Settings';
    public $description = 'Configure a facebook connected app here.';

    public $decorator = 'admindecorator';
    public $template = 'opensocialfacebookadmin';

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
        $CoreMenuObject->setId('opensocialfacebook');
        $CoreMenuObject->setName(CoreLanguage::get('admin.opensocial.link.name'));
        $CoreMenuObject->setHref('/admin/people/facebook');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin.opensocial.link.title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(20);
        $CoreMenuObject->setParentId(AdminModule::ADMIN_NAV_ID_PEOPLE);
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

        $route = new CoreControllerObject('/admin/people/facebook', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
        $route->setType(CoreControllerObject::TYPE_ACTION);
        array_push($routes, $route);

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

        /**
         * Set form data
         * @var FormUI $this->LoginForm
         */
        $this->facebookForm = CoreForm::register('adminfacebook', array('method' => 'post', 'action' => ''));

        $FormField = new FormField();
        $FormField->setName('facebookenabled');
        $FormField->setLabel('Facebook Enabled?');
        $FormField->setType('forminputboolean');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setCondition(null);
        $FormField->setHelper('Please pick if Facebook should be enabled');
        $FormField->setPlaceholder(null);
        $FormField->setValue((CoreStringUtils::evaluateBoolean(CoreModule::getProp('OpenSocialModule', 'facebook.enabled', 'false')) ? 1 : 0));
        $this->facebookForm->addField($FormField);

        /**
         * Facebook App ID
         */
        $FormField = new FormField();
        $FormField->setName('facebookid');
        $FormField->setLabel('Facebook App ID');
        $FormField->setHelper('Set Facebook App ID');
        $FormField->setCondition('/^[0-9]{6,30}$/');
        $FormField->setValue(CoreModule::getProp('OpenSocialModule', 'facebook.application.id', ''));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->facebookForm->addField($FormField);

        /**
         * Facebook App Secret
         */
        $FormField = new FormField();
        $FormField->setName('facebooksecret');
        $FormField->setLabel('Facebook App Secret');
        $FormField->setHelper('Set Facebook App Secret');
        $FormField->setCondition('/^[a-z0-9]{9,55}$/');
        $FormField->setValue(CoreModule::getProp('OpenSocialModule', 'facebook.application.secret', ''));
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->facebookForm->addField($FormField);

        /**
         * Facebook scope
         */
        $FormField = new FormField();
        $FormField->setName('facebookscope');
        $FormField->setLabel('Facebook Data Scope');
        $FormField->setType('forminputtext');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setCondition(null);
        $FormField->setHelper('Set facebook data scope. Enter comma separated values');
        $FormField->setPlaceholder('ie: email');
        $FormField->setValue(CoreModule::getProp('OpenSocialModule', 'facebook.data.scope', ''));
        $this->facebookForm->addField($FormField);

        /**
         * Create Submit Button
         */
        $FormField = new FormField();
        $FormField->setName('facebooksumit');
        $FormField->setLabel(null);
        $FormField->setType('formbuttonslarge');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setCondition(null);
        $FormField->setHelper(null);
        $FormField->setPlaceholder('Save Changes');
        $FormField->setValue('Save');
        $this->facebookForm->addField($FormField);

        //handle submission
        if($this->facebookForm->validFormSubmitted()){
            if($this->facebookForm->validateSubmission()){

                /**
                 * Show notification
                 */
                CoreNotification::set('Updated!', CoreNotification::SUCCESS);

                /**
                 * Store properties
                 */
                CoreModule::setProp('OpenSocialModule', 'facebook.enabled', $this->facebookForm->grabFieldValue('facebookenabled'));
                CoreModule::setProp('OpenSocialModule', 'facebook.application.id', $this->facebookForm->grabFieldValue('facebookid'));
                CoreModule::setProp('OpenSocialModule', 'facebook.application.secret', $this->facebookForm->grabFieldValue('facebooksecret'));
                CoreModule::setProp('OpenSocialModule', 'facebook.data.scope', $this->facebookForm->grabFieldValue('facebookscope'));

            }
        }

    }

}