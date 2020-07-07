<?php

class ExperimentationDashboardAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $decorator = 'admindecorator';
    public $template = 'adminexperimentationdashboard';

    public $title;
    public $description;

    public $path = '/admin/tools/experimentsdash';

    /** @var ExperimentationService $ExperimentationService */
    public $ExperimentationService;

    /** @var array $experiments */
    public $experiments = array();

    public function getMenus()
    {

        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('experimentationdashboard');
        $CoreMenuObject->setName('Experiments Dashboard');
        $CoreMenuObject->setHref($this->path);
        $CoreMenuObject->setTitle('Tool to run AB tests');
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(61);
        $CoreMenuObject->setParentId(AdminModule::ADMIN_NAV_ID_TOOLS);
        $CoreMenuObject->setTarget(AdminModule::ADMIN_NAV_ID);

        return CoreArrayUtils::asArray($CoreMenuObject);

    }

    public function getRoutes()
    {
        return CoreArrayUtils::asArray(CoreControllerObject::buildAction($this->path, __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));
    }

    public function register()
    {

    }

    public function build($params)
    {

        $this->title = 'Experiments Dashboard';
        $this->description = 'Review active experiments';

        $this->ExperimentationService = CoreLogic::getService('ExperimentationService');

        $this->experiments = $this->ExperimentationService->getExperiments();
        /** @var ExperimentationObject $experiment */
        foreach($this->experiments as &$experiment){
            $variants = $experiment->getVariants();
            /** @var ExperimentationVariantObject $variant */
            foreach($variants as &$variant){
                $this->ExperimentationService->setVariantSummary($variant);
                $variant->getExperimentationVariantEntrySummaryObject()->calculate();
            }
            $experiment->buildSummary();
        }

    }

}