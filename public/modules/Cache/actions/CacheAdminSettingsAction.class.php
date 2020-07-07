<?php

/**
 * Page Cache Settings Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CacheAdminSettingsAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    /**
     * Title
     */
    public $title = "Page Cache Settings";

    /**
     * Description
     *
     * @var string
     */
    public $description = "Configure page cache";

    /**
     * Set main template
     */
    public $template = 'adminpagecachesettings';

    /**
     * Decorator
     */
    public $decorator = 'admindecorator';

    /**
     * PageCacheForm form
     * @var FormUI $LoginForm
     */
    private $PageCacheForm;

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
        $CoreMenuObject->setId('pagecachesettings');
        $CoreMenuObject->setName(CoreLanguage::get('admin:system:pagecache:link:name'));
        $CoreMenuObject->setHref('/admin/system/pagecache');
        $CoreMenuObject->setTitle(CoreLanguage::get('admin:system:pagecache:link:title'));
        $CoreMenuObject->setTemplate('adminnavsection');
        $CoreMenuObject->setZIndex(20);
        $CoreMenuObject->setParentId('System');
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

        $route = new CoreControllerObject('/admin/system/pagecache', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
        $route->setType(CoreControllerObject::TYPE_ACTION);
        array_push($routes, $route);

        return $routes;

    }

    /**
     * Register
     *
     * @return mixed|void
     */
    public function register(){


    }

    /**
     * Catch params
     */
    public function build($params = array()){

        /**
         * Set form data
         * @var FormUI $this->LoginForm
         */
        $this->PageCacheForm = CoreForm::register('pagecachesettings', array('method' => 'post', 'action' => ''));

        /**
         * Create Username Field
         */
        $FormField = new FormField();
        $FormField->setName('pagecacheenabled');
        $FormField->setLabel('Page Cache Enabled?');
        $FormField->setType('forminputboolean');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setCondition('');
        $FormField->setHelper('Should page cache be enabled');
        $FormField->setPlaceholder(null);
        $FormField->setValue(CoreModule::getProp('CacheModule', 'caching:enabled', 1));
        $this->PageCacheForm->addField($FormField);

        /**
         * Create Password Field
         */
        $FormField = new FormField();
        $FormField->setName('pagecacheduration');
        $FormField->setLabel('Seconds untill cache expires');
        $FormField->setType('forminputtext');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setCondition('/^[0-9]+$/');
        $FormField->setHelper('Please enter a numerical value');
        $FormField->setPlaceholder(null);
        $FormField->setValue(CoreModule::getProp('CacheModule', 'caching:duration', 1));
        $this->PageCacheForm->addField($FormField);

        /**
         * Create Submit Button
         */
        $FormField = new FormField();
        $FormField->setName('cache_settings_submit');
        $FormField->setLabel(null);
        $FormField->setType('forminputsubmit');
        $FormField->setTemplate('formfieldflexible');
        $FormField->setCondition(null);
        $FormField->setHelper(null);
        $FormField->setPlaceholder(null);
        $FormField->setValue('Save Settings');
        $this->PageCacheForm->addField($FormField);

        /**
         * Handle Submission
         *
         */
        if($this->PageCacheForm->validFormSubmitted()){
            if($this->PageCacheForm->validateSubmission()){
                CoreModule::setProp('CacheModule', 'caching:enabled', $this->PageCacheForm->grabFieldValue('pagecacheenabled'));
                CoreModule::setProp('CacheModule', 'caching:duration', $this->PageCacheForm->grabFieldValue('pagecacheduration'));
                CoreNotification::set(CoreLanguage::get('update:successful'), CoreNotification::SUCCESS);
            }
        }

        /**
         * Show notifications when global caching is disabled
         * or dev mode is enabled
         */
        if(CoreModule::getProp('CacheModule', 'caching:enabled', 1)){
            if(!CACHING_ENABLED) CoreNotification::set('Caching is disabled. Settings will not take effect until caching is enabled in configuration file', CoreNotification::WARNING);
            if(DEV_MODE) CoreNotification::set('Site is currenlty in DEV mode. Caching will not be enabled until DEV mode is disabled', CoreNotification::WARNING);
        }

    }

}