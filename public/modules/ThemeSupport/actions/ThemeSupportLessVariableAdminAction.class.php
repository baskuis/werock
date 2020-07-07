<?php

class ThemeSupportLessVariableAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $decorator = 'admindecorator';

    public $template = 'adminlessvariables';

    public $title = 'Less Variables';

    public $description = 'Define less variables';

    /**
     * The form
     *
     * @var string
     */
    public $lessVarsFormName = 'lessvarsform';
    public $lessVarsForm = null;

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus()
    {

        $menus = array();

        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('adminlessvariables');
        $CoreMenuObject->setName(CoreLanguage::get('admin:system:adminlessvars:link:name'));
        $CoreMenuObject->setHref('/admin/system/lessvariables');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:system:adminlessvars:link:title'));
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

        array_push($routes, CoreControllerObject::buildAction('/admin/system/lessvariables', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));

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
    public function build($params = array())
    {

        /**
         * Register the form
         */
        $this->lessVarsForm = CoreForm::register('lessvarsform', array('method' => 'POST', 'action' => ''));

        /**
         * Create the form fields
         */
        foreach(ThemeSupportModule::$themeOptions as $type => $entries){
            foreach($entries as $key => $value){
                $FormField = new FormField();
                $FormField->setName($type . '-' . $key);
                $FormField->setLabel($key . ' (' . $type . ')');
                $FormField->setValue($value);
                switch($type){
                    case 'colors':
                        $FormField->setCondition('/^#[a-z0-9]{6}$/');
                        $FormField->setHelper('Enter a valid color');
                        $FormField->setType('forminputcolor');
                        break;
                    case 'sizes':
                        $FormField->setCondition('/^[0-9,]{1,5}(px|\%|em)$|^auto$|^inherit$/');
                        $FormField->setHelper('Enter a valid size');
                        $FormField->setType('forminputtext');
                        break;
                    default:
                        $FormField->setType('forminputtext');
                        break;
                }
                $FormField->setTemplate('formfieldflexible');
                $this->lessVarsForm->addField($FormField);
            }
        }

        /**
         * Create submit button
         */
        $FormField = new FormField();
        $FormField->setType('formbuttonlarge');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setName('submit');
        $FormField->setPlaceholder('Save Changes');
        $this->lessVarsForm->addField($FormField);

        //handle submission
        if($this->lessVarsForm->validFormSubmitted()){

            //validate submission
            if($this->lessVarsForm->validateSubmission()){

                /**
                 * Step through theme options
                 */
                foreach(ThemeSupportModule::$themeOptions as $type => $entries){
                    foreach($entries as $key => $value){

                        /**
                         * Set property
                         */
                        CoreModule::setProp('ThemeSupport', $type . ':' . $key, $this->lessVarsForm->grabFieldValue($type . '-' . $key));

                    }
                }

                /**
                 * Update successful
                 */
                CoreNotification::set('Less Variables Updated!', CoreNotification::SUCCESS);

            }

        }

    }


}