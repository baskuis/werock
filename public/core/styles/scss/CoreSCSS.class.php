<?php

/**
 * SCSS compiler
 * Provides logic that stacks, compiles and cached SCSS
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreSCSS {

    /**
     * Allow interceptors
     */
    use CoreInterceptorTrait;

    /**
     * Core less constants
     */
    const SCSS_PREPEND = 'styles-';
    const SCSS_FOLDER = 'scss';
    const SCSS_IMAGES = 'images';

    /**
     * Styles files
     */
    const ALL_STYLES = 'styles.scss'; //styles.scss
    const IE_8_STYLES = 'styles-ie8.scss'; //styles-ie8.scss
    const IE_7_STYLES = 'styles-ie7.scss'; //styles-ie7.scss
    const LT_IE_7_STYLES = 'styles-lt-ie7.scss'; //styles-lt-ie7.scss

    /**
     * Constants
     */
    const SLASH = "/";
    
    /**
     * Cache keys
     */
    const CACHE_SCSS_STACK_KEY = 'scssstack';
    const CACHE_SCSS_NS = 'scssstack';
    const CACHE_SCSS_LAST_KEY = 'cscclast';
    
    /**
     * Cached
     *
     * @var bool
     */
    public static $cached = false;

    /**
     * SCSS stack
     */
    public static $scssStack = array();

    /**
     * Latest modification date
     */
    private static $latestModificationDate = 0;

    /**
     * SCSS compiler
     */
    private static $scssCompiler = null;

    /**
     * Cache all
     */
    public static function cacheAll(){

        //store cache
        CoreCache::saveCache(static::CACHE_SCSS_STACK_KEY, static::$scssStack, 0, true);

    }

    /**
     * Set from cache
     */
    public static function setFromCacheAll(){

        //set from cached
        if(false !== ($set = CoreCache::getCache(static::CACHE_SCSS_STACK_KEY, true))){
            static::$scssStack = $set;
            static::$cached = true;
        }

    }

    /**
     * Load SCSS for template
     *
     * @param CoreTemplateObject $coreTemplateObject
     * @return array
     */
    public static function loadTemplateSCSS(CoreTemplateObject $coreTemplateObject){
        return self::loadSCSS($coreTemplateObject->getBasePath(), $coreTemplateObject->getNamespace());
    }

    /**
     * Load SCSS for module
     *
     * @param CoreModuleObject $coreModuleObject
     * @return mixed
     */
    public static function loadModuleSCSS(CoreModuleObject $coreModuleObject){
        return self::loadSCSS($coreModuleObject->getPath());
    }

    /**
     * Load SCSS
     *
     * @param null $path
     * @param null $namespace
     * @return array
     */
    private static function loadSCSS($path = null, $namespace = null){

        /**
         * return found less
         */
        $foundLess = array();

        /**
         * SCSS base path
         */
        $scssFolder = $path . self::SLASH . self::SCSS_FOLDER;

        /**
         * Get less files
         */
        $lessFiles = CoreFilesystemUtils::readFiles($path . self::SLASH . self::SCSS_FOLDER);

        /**
         * UserRegisterAction less files
         */
        if(!empty($lessFiles)){
            foreach($lessFiles as $lessFile){

                /**
                 * Get crutch base path
                 */
                $scssFilePath = $path . self::SLASH . self::SCSS_FOLDER . self::SLASH . $lessFile;

                /**
                 * Lets make sure the crutch exists
                 */
                if(is_file($scssFilePath)){

                    /**
                     * Create instance of CSS assets
                     */
                    $CssAsset = new CSSAsset($scssFilePath, $namespace, CSSAsset::TYPE_SCSS);

                    /**
                     * Stack on return
                     */
                    array_push($foundLess, $CssAsset);

                }
            }
        }

        return $foundLess;

    }

    /**
     * Register scss
     */
    public static function registerSCSS(){

        self::$scssStack = CoreCache::getCache(static::CACHE_SCSS_STACK_KEY, true, array(self::CACHE_SCSS_NS), false);

        if (!empty(self::$scssStack)) return;

        self::$scssStack = array();

        /** @var CoreModuleObject $coreModuleObject */
        foreach(CoreModule::$modules as $coreModuleObject) {

            /** @var CSSAsset $CSSAsset */
            if(null != $coreModuleObject->getCoreInitAssetsReferenceObject()){
                foreach ($coreModuleObject->getCoreInitAssetsReferenceObject()->getScss() as $scss) {
                    self::register($scss);
                }
            }

            /** @var CoreTemplateObject $view */
            $views = $coreModuleObject->getViews();
            if(!empty($views)) {
                foreach ($views as $view) {
                    /** @var CSSAsset $scss */
                    if (null != $view->getCoreAssetsReferenceObject()) {
                        foreach ($view->getCoreAssetsReferenceObject()->getScss() as $scss) {
                            self::register($scss);
                        }
                    }
                }
            }

        }

        CoreCache::saveCache(static::CACHE_SCSS_STACK_KEY, static::$scssStack, 86400, true, array(self::CACHE_SCSS_NS), false);

    }

    /**
     * Register CSS asset
     * 
     * @param CSSAsset $CSSAsset
     */
    private static function register(CSSAsset $CSSAsset){

        /**
         * Add less file to stack
         */
        array_push(self::$scssStack, $CSSAsset);

    }

    /**
     * Get latest modified
     */
    private static function getLatestModified(){
        return self::$latestModificationDate;
    }

    /**
     * Set latest modified date
     * @param int $timestamp
     */
    private static function setLatestModified($timestamp = 0){
        self::$latestModificationDate = $timestamp;
    }

    /**
     * Find latest modified
     */
    private static function findLatestModified(){

        /**
         * Quick sanity check
         */
        if(empty(self::$scssStack)){
            return 0;
        }

        /** @var CSSAsset $CSSAsset */
        foreach(self::$scssStack as $CSSAsset){

            /**
             * Update latest modified
             */
            if(($latestModDate = filemtime($CSSAsset->getPath())) > self::getLatestModified()){
                self::setLatestModified((int) $latestModDate);
            }

        }

        /**
         * Get last modified date
         */
        return self::getLatestModified();

    }

    /**
     * Find match
     */
    public static function haveSCSSMatch($scssMatch = null){

        /**
         * Quick sanity check
         */
        if(empty(self::$scssStack)){
            return false;
        }

        /**
         * Empty match
         */
        if(empty($scssMatch) && !empty(self::$scssStack)){
            return true;
        }

        /** @var CSSAsset $CSSAsset */
        foreach(self::$scssStack as $CSSAsset){
            if(strpos($CSSAsset->getPath(), $scssMatch) !== false){
                return true;
            }
        }

        /**
         * Did not find match
         */
        return false;

    }

    /**
     * Get SCSS file name
     *
     * @param null $scssMatch
     * @return mixed|string
     */
    private static function getCssFileName($scssMatch = null){

        $response = CoreCache::getCache(self::CACHE_SCSS_LAST_KEY, true, array(self::CACHE_SCSS_NS), false);

        if(!empty($response) || STATIC_ASSET_CACHING_ENABLED) return $response;

        $response =  self::SCSS_PREPEND . md5(self::SCSS_FOLDER . $scssMatch . self::findLatestModified() . serialize(self::$scssStack)) . '.css';
    
        CoreCache::saveCache(self::CACHE_SCSS_LAST_KEY, $response, 86400, true, array(self::CACHE_SCSS_NS), false);
        
        return $response;

    }
    

    /**
     * Get css file
     * @return String css file
     */
    public static function getCssFile($scssMatch = null){

        //generate filename
        $filename = self::getCssFileName($scssMatch);

        //see if file exists
        if(!CoreResources::exists($filename)){

            //compile and store the css
            if(false === CoreResources::store($filename, self::compileSCSS($scssMatch))){

                /**
                 * Log this incident
                 * css file could not
                 * be written to
                 */
                CoreLog::error("Unable to write to css file [" . $filename . "]");

            }

        }

        //return path name
        return CoreResources::getPath($filename);

    }

    /**
     * Compile less
     */
    public static function compileSCSS($scssMatch = null){

        /**
         * Quick sanity check
         */
        if(empty(self::$scssStack)){
            return false;
        }

        /**
         * css string holder
         */
        $css = null;

        //load less compiler if needed
        if(empty(self::$scssCompiler)){

            /**
             * Load less compiler
             */
            require('inc/scss.inc.php');

            /**
             * SCSS compiler
             */
            self::$scssCompiler = new scssc();

        }

        /**
         * Styles string
         */
        $scssString = null;

        /** @var CSSAsset $CSSAsset */
        foreach(self::$scssStack as $CSSAsset){

            /**
             * Check for match
             */
            if(!empty($scssMatch) && strpos($CSSAsset->getPath(), $scssMatch) === false){ continue; }

            //define less holder
            $scss = null;

            /**
             * Add label
             */
            $scss .= "\n\n" . '/* source:' . $CSSAsset->getPath() . ' */' . "\n";

            //read file to string
            if(is_file($CSSAsset->getPath())){
                $scss .= file_get_contents($CSSAsset->getPath());
            }

            /**
             * Fix paths
             */
            $scss = str_ireplace('../' . self::SCSS_IMAGES, dirname($CSSAsset->getPath()) . '/' . self::SCSS_IMAGES, $scss);

            /**
             * Stack to combined css string
             */
            $scss .= "\n\n";

            //stack less string
            $scssString .= $scss;

        }

        /**
         * Attempt to compile less file
         */
        try {

            /**
             * Styles string
             */
            $css = self::$scssCompiler->compile($scssString);

        } catch (exception $e){

            /**
             * Stack this error
             */
            CoreLog::error($e->getMessage());

        }

        /**
         * Return css
         */
        return $css;

    }

}