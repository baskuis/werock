<?php

/**
 * Admin Modules Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class AdminModulesAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    /**
     * Page title
     *
     * @var string
     */
    public $title = 'Modules';

    /**
     * Page description
     *
     * @var string
     */
    public $description = 'Enable or disable modules';

    /**
     * Set decorator
     *
     * @var string
     */
    public $decorator = 'admindecorator';

    /**
     * Set main template
     */
    public $template = 'adminmodules';

    /**
     * Login form
     * @var FormUI $ModulesForm
     */
    private $ModulesForm;

    /**
     * Modules
     *
     * @var
     */
    public $modules = array();

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
        $CoreMenuObject->setId('adminmodulesenabled');
        $CoreMenuObject->setName(CoreLanguage::get('admin:system:modulesenabled:link:name'));
        $CoreMenuObject->setHref('/admin/system/modules');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:system:modulesenabled:link:title'));
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

        $route = new CoreControllerObject('/admin/system/modules', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
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
     * Catch params
     */
    public function build($params = array()){

        /**
         * Set Modules
         */
        $this->modules = CoreModule::getAll();

        /**
         * Show warning
         */
        CoreNotification::set('Disabling or enabling modules could create unrecoverable conditions', CoreNotification::WARNING);

        /**
         * Set form data
         * @var FormUI $this->LoginForm
         */
        $this->ModulesForm = CoreForm::register('adminmodules', array('method' => 'post', 'action' => ''));

        //to obfuscate field names
        $append = 'enabled';

        /* @var CoreModuleObject $CoreModuleObject */
        foreach($this->modules as &$CoreModuleObject){

            /**
             * No need to allow editing of forced enabled or forced disabled modules
             */
            $disabled = false;
            if(defined('WEROCK_MODULES_ENABLED')){
                if(in_array($CoreModuleObject->getName(), explode(',', WEROCK_MODULES_ENABLED))){
                    $disabled = true;
                }
            }

            if(defined('WEROCK_MODULES_DISABLED')){
                if(in_array($CoreModuleObject->getName(), explode(',', WEROCK_MODULES_DISABLED))){
                    $disabled = true;
                }
            }

            $FormField = new FormField();
            $FormField->setName($CoreModuleObject->getName() . $append);
            $FormField->setLabel($CoreModuleObject->getName() . ' Enabled?');
            $FormField->setType('forminputboolean');
            $FormField->setDisabled($disabled);
            $FormField->setTemplate('moduleformrow');
            $FormField->setCondition(null);
            $FormField->setHelper('Please pick whether ' . $CoreModuleObject->getName() . ' enabled or not');
            $FormField->setPlaceholder(null);
            $FormField->setValue(($CoreModuleObject->isEnabled() ? 1 : 0));
            $FormField->setData(array('module' => $CoreModuleObject, 'isDependency' => CoreModule::_isActiveDependency($CoreModuleObject)));
            $this->ModulesForm->addField($FormField);

        }

        /**
         * Create Submit Button
         */
        $FormField = new FormField();
        $FormField->setName('modules_submit');
        $FormField->setLabel(null);
        $FormField->setType('formbuttonlarge');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setCondition(null);
        $FormField->setHelper(null);
        $FormField->setPlaceholder('Save Changes');
        $FormField->setValue('Save');
        $this->ModulesForm->addField($FormField);

        //handle submission
        if($this->ModulesForm->validFormSubmitted()){
            if($this->ModulesForm->validateSubmission()){

                /**
                 * Step through submitted values
                 */
                $values = $this->ModulesForm->getFormValues();
                foreach($values as $key => &$value){
                    if(substr($key, -strlen($append)) == $append){
                        if($value == 1){
                            CoreModule::enable(str_replace($append, null, $key));
                        }else if($value == 0){
                            CoreModule::disable(str_replace($append, null, $key));
                        }
                    }
                }

                //flush cache
                CoreCache::flushCache();

                //show success message
                CoreNotification::set('Modules updated!', CoreNotification::SUCCESS);

                //need to reload the page to take effect
                CoreNotification::set('Please <a href="">reload this page</a> for this change to take effect', CoreNotification::SUCCESS);

            }
        }

    }

}