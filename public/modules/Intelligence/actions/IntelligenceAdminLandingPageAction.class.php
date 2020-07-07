<?php

/**
 * Admin Intelligence Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class IntelligenceAdminLandingPageAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    public $title = '';

    public $template = 'adminintelligence';

    public $decorator = 'admindecorator';

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
        $CoreMenuObject->setId('adminintelligence');
        $CoreMenuObject->setName(CoreLanguage::get('admin:intelligence:link:name'));
        $CoreMenuObject->setHref('/admin/intelligence');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:intelligence:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(60);
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

        $route = new CoreControllerObject('/admin/intelligence', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
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

        //pick correct title
        $this->title = CoreLanguage::get('admin.intelligence.page.title');

        $widgets = array();

        /** @var IntelligenceWidgetObject $widget */
        $widget = CoreLogic::getObject('IntelligenceWidgetObject');
        $widget->setLabel('Page Views');
        $widget->setKey('page view');
        $widget->setStart(strtotime('-30 days'));
        $widget->setEnd(time());
        $widget->setInterval(24 * 3600);
        $widget->setCanEdit(true);
        $widget->setUniqueID('pageviews');
        $widget->setIsLineChart(true);
        $widget->setTemplate('googlechartswidget');
        array_push($widgets, $widget);

        /** @var IntelligenceWidgetObject $widget */
        $widget = CoreLogic::getObject('IntelligenceWidgetObject');
        $widget->setLabel('Actions');
        $widget->setKey('action');
        $widget->setStart(strtotime('-30 days'));
        $widget->setEnd(time());
        $widget->setInterval(24 * 3600);
        $widget->setCanEdit(true);
        $widget->setUniqueID('actions');
        $widget->setIsLineChart(true);
        $widget->setTemplate('googlechartswidget');
        array_push($widgets, $widget);

        /** @var IntelligenceWidgetObject $widget */
        $widget = CoreLogic::getObject('IntelligenceWidgetObject');
        $widget->setLabel('Browsers');
        $widget->setKey('browser');
        $widget->setStart(strtotime('-30 days'));
        $widget->setEnd(time());
        $widget->setInterval(30 * 24 * 3600);
        $widget->setCanEdit(true);
        $widget->setUniqueID('browsers');
        $widget->setIsPieChart(true);
        $widget->setHeight('320px');
        $widget->setTemplate('googlechartswidget');
        array_push($widgets, $widget);

        /** @var IntelligenceWidgetObject $widget */
        $widget = CoreLogic::getObject('IntelligenceWidgetObject');
        $widget->setLabel('Browser Versions');
        $widget->setKey('browser version');
        $widget->setStart(strtotime('-30 days'));
        $widget->setEnd(time());
        $widget->setInterval(24 * 3600);
        $widget->setCanEdit(true);
        $widget->setUniqueID('browsersversions');
        $widget->setIsLineChart(true);
        $widget->setTemplate('googlechartswidget');
        array_push($widgets, $widget);

        /** @var IntelligenceWidgetObject $widget */
        $widget = CoreLogic::getObject('IntelligenceWidgetObject');
        $widget->setLabel('Platform');
        $widget->setKey('platform');
        $widget->setStart(strtotime('-30 days'));
        $widget->setEnd(time());
        $widget->setInterval(24 * 3600);
        $widget->setCanEdit(true);
        $widget->setUniqueID('platform');
        $widget->setIsLineChart(true);
        $widget->setTemplate('googlechartswidget');
        array_push($widgets, $widget);

        /** @var IntelligenceWidgetObject $widget */
        $widget = CoreLogic::getObject('IntelligenceWidgetObject');
        $widget->setLabel('Records Added In The Last 30 Days');
        $widget->setKey('city');
        $widget->setStart(strtotime('-30 days'));
        $widget->setEnd(time());
        $widget->setCanEdit(true);
        $widget->setUniqueID('thecitytest');
        $widget->setIsCityChart(true);
        $widget->setTemplate('googlechartswidget');
        $widget->setHeight('320px');
        array_push($widgets, $widget);

        /** @var IntelligenceWidgetObject $widget */
        $widget = CoreLogic::getObject('IntelligenceWidgetObject');
        $widget->setLabel('Records Added In The Last 30 Days');
        $widget->setKey('region');
        $widget->setStart(strtotime('-30 days'));
        $widget->setEnd(time());
        $widget->setCanEdit(true);
        $widget->setUniqueID('regiontest');
        $widget->setIsRegionChart(true);
        $widget->setTemplate('googlechartswidget');
        $widget->setHeight('320px');
        array_push($widgets, $widget);
        
        /** @var IntelligenceWidgetObject $widget */
        $widget = CoreLogic::getObject('IntelligenceWidgetObject');
        $widget->setLabel('Countries');
        $widget->setKey('country');
        $widget->setStart(strtotime('-30 days'));
        $widget->setEnd(time());
        $widget->setCanEdit(true);
        $widget->setUniqueID('countrytest');
        $widget->setIsCountryChart(true);
        $widget->setTemplate('googlechartswidget');
        $widget->setHeight('320px');
        array_push($widgets, $widget);

        /** @var IntelligenceWidgetObject $widget */
        $widget = CoreLogic::getObject('IntelligenceWidgetObject');
        $widget->setLabel('Unable To Route');
        $widget->setKey('page not found');
        $widget->setStart(strtotime('-30 days'));
        $widget->setEnd(time());
        $widget->setInterval(24 * 3600);
        $widget->setCanEdit(true);
        $widget->setUniqueID('unabletoroute');
        $widget->setIsLineChart(true);
        $widget->setTemplate('googlechartswidget');
        array_push($widgets, $widget);

        CoreTemplate::setData('adminintelligence', 'widgets', $widgets);

    }

}