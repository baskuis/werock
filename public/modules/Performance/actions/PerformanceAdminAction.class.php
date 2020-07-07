<?php

class PerformanceAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title;
    public $description;

    public $decorator = 'admindecorator';
    public $template = 'adminperformanceutils';

    /** @var FormUI $performanceFormSortRoutes */
    public $performanceFormSortRoutes;

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus()
    {
        $menus = array();

        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('performanceutils');
        $CoreMenuObject->setName('admin.performance.utils.link.name');
        $CoreMenuObject->setTitle('admin.performance.utils.link.title');
        $CoreMenuObject->setHref('/admin/performanceutils');
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
        array_push($routes, CoreControllerObject::buildAction('/admin/performanceutils', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));
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

        $this->title = 'Performance Utils';
        $this->description = 'Tools to help tune werock performance';

        $this->performanceFormSortRoutes = CoreForm::register('performanceFormSortRoutes', array('method' => 'post', 'action' => ''));

        $FormField = new FormField();
        $FormField->setName('submit');
        $FormField->setType('formbuttonlarge');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setValue('true');
        $FormField->setPlaceholder('Sort Routes By Popularity');

        $this->performanceFormSortRoutes->addField($FormField);

        if($this->performanceFormSortRoutes->validFormSubmitted()){
            if($this->performanceFormSortRoutes->validateSubmission()){

                try {
                    PerformanceModule::orderRoutesByPopularity();
                    CoreNotification::set('Sorted routes by popularity for better performance', CoreNotification::SUCCESS);
                } catch(Exception $e){
                    CoreNotification::set('An error occurred. Info: ' . $e->getMessage(), CoreNotification::ERROR);
                }

            }
        }

    }

}