<?php

class ExperimentationExperimentsAdminAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';

    public $description = '';

    public $template = 'maptabledefault';

    public $decorator = 'admindecorator';

    public $path = '/admin/tools/experiments';

    /** @var MapTableContextObject $MapTableContextObject */
    public $MapTableContextObject;

    public function getMenus()
    {

        $CoreMenuObject = new CoreMenuObject();
        $CoreMenuObject->setId('experimentationexperiments');
        $CoreMenuObject->setName('Configure Experiments');
        $CoreMenuObject->setHref($this->path);
        $CoreMenuObject->setTitle('Tool to run AB tests');
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(60);
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

        $this->title = 'Experimentation';
        $this->description = 'Tools like AB testing';

        /** @var MapTableContextObject $MapTableContextObject */
        $MapTableContextObject = CoreLogic::getObject('MapTableContextObject');
        $MapTableContextObject->setTable('werock_experiments');
        $MapTableContextObject->setTemplate('maptabledefault');
        $MapTableContextObject->setListingTemplate('maptablelisting');
        $MapTableContextObject->setListingHeaderTemplate('maptablelistingheader');

        /** @var MapTableRelatedTableDescriptionObject $MapTableRelatedTableDescriptionObject */
        $MapTableRelatedTableDescriptionObject = CoreLogic::getObject('MapTableRelatedTableDescriptionObject');
        $MapTableRelatedTableDescriptionObject->setTable('werock_experiment_variants');
        $MapTableRelatedTableDescriptionObject->setTitle('Variants');
        $MapTableRelatedTableDescriptionObject->setDescription('Experiment variants');
        $MapTableRelatedTableDescriptionObject->setInputTemplate('maptablerelatedtable');
        $MapTableRelatedTableDescriptionObject->setFieldTemplate('formfieldnaked');

        /** add related field description */
        $MapTableContextObject->addRelatedTable($MapTableRelatedTableDescriptionObject);

        /** @var set action reference MapTableContextObject */
        $this->MapTableContextObject = $MapTableContextObject;

        /** @var MapTableService $MapTableManager */
        $MapTableManager = CoreLogic::getService('MapTableService');

        /** @var MapTableActionModifierObject $MapTableActionModifierObject */
        $MapTableActionModifierObject = $MapTableManager->fromContext($MapTableContextObject);

        /**
         * Set data on this action accordingly
         */
        $this->template = $MapTableActionModifierObject->getTemplate();

    }

}