<?php

/**
 * TemplateUtilsModule
 * allows for wrappers to be automatically added to template definitions and other template enhancements
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class TemplateUtilsModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Template Utils Module';
    public static $description = 'Theme support module';
    public static $version = '1.0.0';
    public static $dependencies = array(
        'Admin' => array('min' => '1.0.0', 'max' => '1.0.0')
    );

    /**
     * CSS template wrapper prepend
     */
    const TEMPLATE_ID_WRAPPER_PREPEND = '___';

    /**
     * Allow template type which will indicate this template needs to be wrapped
     */
    const WRAP_TEMPLATE_TYPE = 'wrap';

    /**
     * Wrapper element
     */
    const WRAPPER_ELEMENT = 'div';

    /**
     * Get listeners
     *
     * @return mixed
     */
    public static function getListeners()
    {

    }

    /**
     * Get interceptors
     *
     * @return mixed
     */
    public static function getInterceptors()
    {

        $interceptors = array();

        /**
         * The below interceptors allow for autowrapping of templates which allows cleaner CSS rules with less chance of collisions
         */
        array_push($interceptors, new CoreInterceptorObject(CoreLess::getClassName(), "getLessContentsFromFile", __CLASS__, 'wrapLessFile', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));

        /**
         * Wrap view/render
         */
        array_push($interceptors, new CoreInterceptorObject(CoreTemplate::getClassName(), 'render', __CLASS__, 'wrapTemplateRender', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));
        array_push($interceptors, new CoreInterceptorObject(CoreTemplate::getClassName(), 'getView', __CLASS__, 'wrapTemplateGetView', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));
        array_push($interceptors, new CoreInterceptorObject(CoreFeTemplate::getClassName(), 'getView', __CLASS__, 'wrapFeTemplateGetView', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));

        /**
         * The below interceptor allows for relative image paths to be used in CSS rules
         */
        array_push($interceptors, new CoreInterceptorObject(CoreLess::getClassName(), 'getLessContentsFromFile', __CLASS__, 'replaceRelativeImagePaths', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));

        return $interceptors;

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
     * Called when module is loaded
     */
    static function __init__(){

    }

    /**
     * Wraps the less file
     *
     * @param $passedLess
     * @param CSSAsset $CSSAsset
     * @return string
     */
    public static function wrapLessFile($passedLess, $params){

        /**
         * @var CSSAsset $CSSAsset
         */
        $CSSAsset = null;
        if(get_class($params[0]) == CSSAsset::class){
            $CSSAsset = $params[0];
        }

        //define less string
        $less = "";

        //do not wrap less if not requested
        $templatename = $CSSAsset->getTemplateName();

        //attempt to lookup template object
        $CoreTemplateObject = CoreTemplate::getByNamespace($templatename);

        //return original less
        if(empty($CoreTemplateObject) || !$CoreTemplateObject->hasType(TemplateUtilsModule::WRAP_TEMPLATE_TYPE) || stripos($CSSAsset->getPath(), CoreLess::GLOBAL_STYLES) > -1){
            return $passedLess;
        }

        //open class wrapper
        if($CSSAsset->haveTemplateName()){
            $less .= "\n" . '.' . self::TEMPLATE_ID_WRAPPER_PREPEND . $CSSAsset->getTemplateName() . ' {' . "\n" . 'display: inline;' . "\n" . $passedLess . "\n" . '}' . "\n";
        }

        //return wrapped less string
        return $less;

    }

    /**
     * Wrap on template render
     *
     * @param String $html
     * @param array $params
     * @return string
     */
    public static function wrapTemplateRender($html, $params){

        /**
         * @var String $namespace
         */
        $namespace = null;
        if(isset($params[0]) && is_string($params[0])){
            $namespace = $params[0];
        }

        /**
         * @var CoreTemplateObject $CoreTemplateObject
         */
        $CoreTemplateObject = CoreTemplate::getByNamespace($namespace);
        if(empty($CoreTemplateObject) || !$CoreTemplateObject->hasType(TemplateUtilsModule::WRAP_TEMPLATE_TYPE)){
            if(!DEV_MODE) return $html;
            return '<!-- ' . $namespace . ' -->' . $html . '<!-- /' . $namespace . ' -->';
        }

        //wrap html
        return '<!-- ' . $namespace . ' --><' . self::WRAPPER_ELEMENT . ' class="' . self::TEMPLATE_ID_WRAPPER_PREPEND . $namespace . '"' . (DEV_MODE ? ' title="' . $CoreTemplateObject->getTemplatePath() . '"' : null) . '>' . $html . '</' . self::WRAPPER_ELEMENT . '><!-- /' . $namespace . ' -->';

    }

    /**
     * Wrap on template get view
     *
     * @param String $view
     * @param array $params
     * @return string
     */
    public static function wrapTemplateGetView($view, $params){

        /**
         * @var String $namespace
         */
        $namespace = null;
        if(isset($params[0]) && is_string($params[0])){
            $namespace = $params[0];
        }

        /**
         * @var CoreTemplateObject $CoreTemplateObject
         */
        $CoreTemplateObject = CoreTemplate::getByNamespace($namespace);
        if(empty($CoreTemplateObject) || !$CoreTemplateObject->hasType(TemplateUtilsModule::WRAP_TEMPLATE_TYPE)){
            if(!DEV_MODE) return $view;
            return '<!-- ' . $namespace . ' -->' . $view . '<!-- /' . $namespace . ' -->';
        }

        //wrap the view
        return '<!-- ' . $namespace . ' --><' . self::WRAPPER_ELEMENT . ' class="' . self::TEMPLATE_ID_WRAPPER_PREPEND . $namespace . '"' . (DEV_MODE ? ' title="' . $CoreTemplateObject->getTemplatePath() . '"' : null) . '>' . $view . '</' . self::WRAPPER_ELEMENT . '><!-- /' . $namespace . ' -->';

    }

    /**
     * Wrap the generated view for fe templates
     *
     * @param $view
     * @param $params
     * @return string
     */
    public static function wrapFeTemplateGetView($view, $params){

        /**
         * @var CoreTemplateObject $CoreTemplateObject
         */
        $CoreTemplateObject = $params[0];

        /**
         * If no wrap skip this
         */
        if(!$CoreTemplateObject->hasType(TemplateUtilsModule::WRAP_TEMPLATE_TYPE)){
            if(!DEV_MODE) return $view;
            return '<!-- ' . $CoreTemplateObject->getNamespace() . ' -->' . $view . '<!-- /' . $CoreTemplateObject->getNamespace() . ' -->';
        }

        //wrap the view
        return '<!-- ' . $CoreTemplateObject->getNamespace() . ' --><' . self::WRAPPER_ELEMENT . ' class="' . self::TEMPLATE_ID_WRAPPER_PREPEND . $CoreTemplateObject->getNamespace() . '"' . (DEV_MODE ? ' title="' . $CoreTemplateObject->getTemplatePath() . '"' : null) . '>' . $view . '</' . self::WRAPPER_ELEMENT . '><!-- /' . $CoreTemplateObject->getNamespace() . ' -->';

    }

    /**
     * Replace relative image paths
     *
     * @param $less
     * @param CSSAsset $CSSAsset
     * @return mixed
     */
    public static function replaceRelativeImagePaths($less, $params){

        /**
         * @var CSSAsset $CSSAsset
         */
        $CSSAsset = null;
        if(get_class($params[0]) == CSSAsset::class){
            $CSSAsset = $params[0];
        }

        /**
         * Fix paths
         */
        return str_ireplace(CoreLess::LESS_IMAGES, HTTP_PROTOCOL . DOMAIN_NAME . dirname($CSSAsset->getWebPath()) . '/' . CoreLess::LESS_IMAGES, $less);

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