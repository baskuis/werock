<?php

class ElasticSearchAdminConfigureAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $decorator = 'admindecorator';

    public $template = 'elasticsearchsettings';

    public $title = 'ElasticSearch Connection';

    public $description = 'Edit ElasticSearch connection details.';

    public $elasticsearchStatus = null;

    /** @var FormUI $form  */
    public $form = null;

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus()
    {

        $menus = array();

        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('elasticsearch');
        $CoreMenuObject->setName('Elasticsearch');
        $CoreMenuObject->setTitle('Elasticsearch Settings');
        $CoreMenuObject->setParentId('System');
        $CoreMenuObject->setHref('/admin/settings/elasticsearch');
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

        $route = new CoreControllerObject('/admin/settings/elasticsearch', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
        $route->setType(CoreControllerObject::TYPE_ACTION);
        array_push($routes, $route);

        return $routes;

    }

    /**
     * UserRegisterAction the model
     *
     * @return mixed
     */
    public function register(){


    }

    /**
     * Build the model
     *
     * @param $params
     * @return mixed
     */
    public function build($params){

        $this->form = CoreForm::register('elasticsearchconnectiondetails', array('method' => 'post', 'action' => ''));

        $FormField = new FormField();
        $FormField->setName('elasticsearch_hosts');
        $FormField->setLabel('Elasticsearch Hosts');
        $FormField->setCondition('*');
        $FormField->setType('forminputtext');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setPlaceholder('ie: http://user:pass@host.com:9200');
        $FormField->setValue(CoreModule::getProp('ElasticSearchModule', 'elasticsearch:hosts', 'http://localhost:9200'));
        $this->form->addField($FormField);

        $FormField = new FormField();
        $FormField->setName('elasticsearch_buttons');
        $FormField->setType('formbuttonlarge');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setPlaceholder('Update & Get Status');
        $this->form->addField($FormField);

        if($this->form->validFormSubmitted()){
            if($this->form->validateSubmission()){

                $submission = $this->form->getFormValues();

                if(isset($submission['elasticsearch_hosts'])){

                    /** @var ElasticSearchService $ElasticSearchManager */
                    $ElasticSearchManager = CoreLogic::getService('ElasticSearchService');

                    /**
                     * Get elastic search status
                     */
                    $status = $ElasticSearchManager->status();
                    if(!empty($status)){
                        $this->elasticsearchStatus = print_r($status, true);
                    }

                    CoreModule::setProp('ElasticSearchModule', 'elasticsearch:hosts', $submission['elasticsearch_hosts']);
                    CoreNotification::set('ElasticSearch connection details updated!', CoreNotification::SUCCESS);

                }

            }
        }

    }

}