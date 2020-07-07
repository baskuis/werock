<?php

/**
 * Media Failed Logins Action
 * built by maptable
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class AdminSchemaInterpretationAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';
    public $description = '';

    public $template = 'schemainterpretation';

    public $decorator = 'admindecorator';

    /** @var FormUI $SchemeInterpretationForm */
    public $SchemeInterpretationForm;
    public $SchemaOutline;

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
        $CoreMenuObject->setId('schemainterpretation');
        $CoreMenuObject->setName(CoreLanguage::get('admin:schemainterpretation:link:name'));
        $CoreMenuObject->setHref('/admin/tools/schemainterpretation');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:schemainterpretation:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(60);
        $CoreMenuObject->setParentId(AdminModule::ADMIN_NAV_ID_TOOLS);
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

        $route = new CoreControllerObject('/admin/tools/schemainterpretation', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
        $route->setType(CoreControllerObject::TYPE_ACTION);
        array_push($routes, $route);

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

        //pick correct title
        $this->title = CoreLanguage::get('admin.schemainterpretation.page.title');
        $this->description = CoreLanguage::get('admin.schemainterpretation.page.description');

        /**
         * Set form data
         * @var FormUI $this->LoginForm
         */
        $this->SchemeInterpForm = CoreForm::register('interpretschema', array('method' => 'post', 'action' => ''));

        /**
         * Session duration
         */
        $FormField = new FormField();
        $FormField->setName('schemainterptable');
        $FormField->setLabel('Table');
        $FormField->setHelper('Please enter a valid table name');
        $FormField->setPlaceholder('ie: werock_my_table_name');
        $FormField->setCondition('/^[a-zA-Z0-9\-_]{3,155}$/');
        $FormField->setValue('');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setType('forminputtext');
        $this->SchemeInterpForm->addField($FormField);

        /**
         * Create Submit Button
         */
        $FormField = new FormField();
        $FormField->setName('interp_schema_submit');
        $FormField->setLabel(null);
        $FormField->setType('forminputsubmit');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setCondition(null);
        $FormField->setHelper(null);
        $FormField->setPlaceholder('Interpret Table');
        $FormField->setValue('Interpret');
        $this->SchemeInterpForm->addField($FormField);

        //handle submission
        if($this->SchemeInterpForm->validFormSubmitted()){
            if($this->SchemeInterpForm->validateSubmission()){

                /**
                 * Attempt to generate json - and pretty load json string
                 * into the action model
                 */
                try {
                    $json = CoreSchema::tableToJson($this->SchemeInterpForm->grabFieldValue('schemainterptable'));
                    $this->SchemaOutline = json_encode($json, JSON_PRETTY_PRINT);
                    CoreNotification::set('Table Interpretation Completed!', CoreNotification::SUCCESS);
                } catch (CoreSchemaTableNotFoundException $e){
                    CoreNotification::set('Unable to find table: ' . $this->SchemeInterpForm->grabFieldValue('schemainterptable'),CoreNotification::ERROR);
                }

            }
        }

    }

}