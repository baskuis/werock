<?php

/**
 * Theme Support Module
 * This Module allows for configurable theme options
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class ThemeSupportModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Theme Support Module';
    public static $description = 'Theme support module';
    public static $version = '1.0.0';
    public static $dependencies = array(
        'CrutchKit' => array('min' => '1.0.0', 'max' => '1.0.0')
    );

    /**
     * Default theme variables map
     * can be set in persistent storage
     */
    public static $themeOptions = array(
        'colors' => array(
            'bodybg' => '#efefef',
            'header1' => '#222222',
            'header2' => '#333333',
            'header3' => '#444444',
            'header4' => '#555555',
            'leadtext' => '#111111',
            'text' => '#444444',
            'link' => '#1f6098',
            'active' => '#2a6496',
            'success' => '#11a215',
            'successbg' => '#11a215',
            'error' => '#9f1d1d',
            'errorbg' => '#9f1d1d',
            'notification' => '#5051aa',
            'notificationbg' => '#5051aa',
            'warning' => '#f08200',
            'warningbg' => '#f08200',
            'highlight' => '#428bca',
            'darkest' => '#111111',
            'darker' => '#333333',
            'dark' => '#555555',
            'light' => '#999999',
            'lighter' => '#cccccc',
            'lightest' => '#eeeeee'
        ),
        'display' => array(
            'bglighten'=> 0.06
        ),
        'fonts' => array(
            'mainfont' => 'proximanova'
        ),
        'sizes' => array(
            'gutterwidth' => '2em',
            'mobilebreakpoint' => '480px',
            'tabletbreakpoint' => '768px',
            'laptopbreakpoint' => '1024px',
            'desktopbreakpoint' => '1224px',
            'largebreakpoint' => '1824px'
        )
    );

    /**
     * Get listeners
     *
     * @return mixed
     */
    public static function getListeners()
    {

        $listeners = array();

        array_push($listeners, new CoreObserverObject(CoreLess::EVENT_COMPILE_LESS_FILE_BEFORE, __CLASS__, 'insertLessVariables'));

        return $listeners;

    }

    /**
     * Get interceptors
     *
     * @return mixed
     */
    public static function getInterceptors()
    {
        // TODO: Implement getInterceptors() method.
    }

    /**
     * Get menus
     *
     * @return mixed
     */
    public static function getMenus()
    {
        // TODO: Implement getMenus() method.
    }

    /**
     * Get routes
     *
     * @return mixed
     */
    public static function getRoutes()
    {
        // TODO: Implement getRoutes() method.
    }

    /**
     * UserRegisterAction listeners, toMethod
     */
    public static function __init__(){

        /**
         * Update theme options with values in database if needed
         */
        foreach(self::$themeOptions as $sectionKey => &$section){
            foreach($section as $key => &$value){
                $section[$key] = CoreModule::getProp(__CLASS__, $sectionKey . ':' . $key, $value);
            }
        }

    }

    /**
     * Insert less variables
     */
    public static function insertLessVariables(){

        //step through sections
        foreach(self::$themeOptions as $sectionKey => &$section){

            //step though variables
            foreach($section as $key => &$value){

                /**
                 * Set to empty string when empty
                 */
                if(empty($value)){
                    $value = "''";
                }

                /**
                 * Define LESS variable and add to stack
                 */
                CoreLess::$lessString .= '@' . $key . ': ' . $value . ';' . "\n";

            }
        }

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

    /**
     * Run on install
     *
     */
    public static function __install__()
    {

    }

}