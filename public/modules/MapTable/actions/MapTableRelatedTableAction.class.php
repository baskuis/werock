<?php

class MapTableRelatedTableAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = 'Map Table Example';

    public $template = 'maptabledefault';

    public $decorator = 'maptableembeddeddecorator';

    public $MapTableContextObject;

    /**
     * Get menu's for this action
     *
     * @return array
     */
    public function getMenus()
    {

    }

    /**
     * Get routes for this action
     *
     * @return array
     */
    public function getRoutes()
    {

        $routes = array();

        $route = new CoreControllerObject('/maptable\/related\/table\/([a-z0-9\-_]+)\/?/i', __CLASS__, null, CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::GROUP_PAGE);
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
    public function build($params)
    {

        /** @var MapTableContextObject $MapTableContextObject */
        $MapTableContextObject = CoreLogic::getObject('MapTableContextObject');
        $MapTableContextObject->setTable($params[1]);
        $MapTableContextObject->setTemplate('maptablesimple');
        $MapTableContextObject->setListingTemplate('maptablelistingsimple');
        $MapTableContextObject->setListingHeaderTemplate('maptablelistingheadersimple');

        /** @var MapTableLightContextObject $parentContext */
        $parentContext = json_decode(CoreEncryptionUtils::decryptString((isset($_GET['context']) ? $_GET['context'] : null)));

        /** assertions */
        if(!isset($parentContext->primaryKey->field)) CoreLog::error('Need $parentContext->primaryKey->field!');
        if(!isset($parentContext->primaryValue)) CoreLog::error('Need $parentContext->primaryValue!');

        /** @var MapTableStickyFieldObject $MapTableStickyFieldObject */
        $MapTableStickyFieldObject = CoreLogic::getObject('MapTableStickyFieldObject');
        $MapTableStickyFieldObject->setName($parentContext->primaryKey->field);
        $MapTableStickyFieldObject->setValue($parentContext->primaryValue);

        /** set sticky fields */
        $MapTableContextObject->setStickyFields(array($MapTableStickyFieldObject));

        /** @var MapTableContextObject $this->MapTableContextObject */
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