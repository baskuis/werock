<?php

/**
 * Google tools module
 * Allows for configuration of google tools
 *
 * PHP version 7
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class GoogleToolsModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Google Tools Module';
    public static $description = 'Adds google tools';
    public static $version = '1.0.0.4';
    public static $dependencies = array(
        'Form' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'Admin' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        )
    );

    const GOOGLE_WEBMASTER_VERIFICATION_KEY = 'google.webmaster.verification.key';
    const GOOGLE_ANALYTICS_KEY = 'google.analytics.key';
    const GOOGLE_ADSENSE_KEY = 'google.adsense.key';
    const GOOGLE_ADWORDS_KEY = 'google.adwords.key';

    /**
     * Init script
     *
     */
    public static function __init__()
    {

    }

    /**
     * Get listeners
     *
     * @return mixed
     */
    public static function getListeners()
    {
        $listeners = array();
        array_push($listeners, CoreObserverObject::build(CoreRender::EVENT_RENDER_PAGE_END_HEAD, __CLASS__, 'appendHead'));
        array_push($listeners, CoreObserverObject::build(CoreController::CONTROLLER_EVENT_RENDER_BEFORE, __CLASS__, 'stackScripts'));
        return $listeners;
    }

    /**
     * Append in head block
     */
    public static function appendHead(){

        /**
         * Retrieve google webmaster tools tag value
         */
        $googleWebmasterVerificationKey = CoreModule::getProp(__CLASS__, self::GOOGLE_WEBMASTER_VERIFICATION_KEY, CoreStringUtils::EMPTY_STRING);

        /**
         * Assume not needed
         */
        if(empty($googleWebmasterVerificationKey)) return;

        /**
         * Add Google webmaster tools meta tag
         */
        CoreRender::$output .= CoreHtmlUtils::meta(null, array(
            'name' => 'google-site-verification',
            'content' => $googleWebmasterVerificationKey
        ));

    }

    /**
     * Stack scripts
     */
    public static function stackScripts(){

        /**
         * Retrieve google analytics
         */
        $googleAnalyticsKey = CoreModule::getProp(__CLASS__, self::GOOGLE_ANALYTICS_KEY, CoreStringUtils::EMPTY_STRING);

        /**
         * Assume not needed
         */
        if(empty($googleAnalyticsKey)) return;

        /**
         * Add google analytics code
         */
        CoreScript::$requestScopedScript .= '
            (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,\'script\',\'https://www.google-analytics.com/analytics.js\',\'ga\');
            ga(\'create\', \'' . $googleAnalyticsKey . '\', \'auto\');
            ga(\'send\', \'pageview\');
            window.addEventListener(\'error\', function(e) {
                console.error(e);
                if(ga !== undefined){
                    ga([\'_trackEvent\', \'JavaScript Error\', e.message, e.filename + \':  \' + e.lineno, true]);
                }
            });
            $(document).ajaxError(function(e, request, settings) {
                console.error(e);
                if(ga !== undefined){
                    ga([\'_trackEvent\', \'Ajax error\', settings.url, e.result, true]);
                }
            });
        ';

    }

    /**
     * Get interceptors
     *
     * @return mixed
     */
    public static function getInterceptors()
    {

    }

    /**
     * Get menus
     *
     * @return mixed
     */
    public static function getMenus()
    {

    }

    /**
     * Get routes
     *
     * @return mixed
     */
    public static function getRoutes()
    {

    }

    /**
     * Run on install
     *
     */
    public static function __install__()
    {

    }

    /**
     * Run on update
     *
     * @param $previousVersion
     * @param $newVersion
     *
     * @return void
     */
    public static function __update__($previousVersion, $newVersion)
    {

    }

    /**
     * Run on enable
     *
     * @return void
     */
    public static function __enable__()
    {

    }

    /**
     * Run on disable
     *
     * @return mixed
     */
    public static function __disable__()
    {

    }

}