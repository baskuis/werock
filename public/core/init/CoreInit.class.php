<?php

/**
 * Core Init
 * This is the highest level object in charge of loading all other components
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */ 
class CoreInit { 

    use CoreInterceptorTrait;
    use ClassReflectionTrait;

	/**
	 * Theme template view
	 * folder name containing templates
	 */
	const THEME_FOLDER = "themes";
	const THEME_TEMPLATE_FOLDER = "view";
	const THEME_TEMPLATE_DEFAULT_FOLDER = "view";

    /**
     * Cache keys
     */
    const CACHE_ASSETS_LESS_KEY = "assetsless";
    const CACHE_ASSETS_SCSS_KEY = "assetsscss";
    const CACHE_ASSETS_CRUTCHES_KEY = "assetscrutches";
    const CACHE_ASSETS_TEMPLATES_KEY = "assetstemplates";
    const CACHE_ASSETS_SCRIPTS_KEY = "assetsscripts";

    /**
     * Events
     */
    const EVENT_ON_UNLOAD = 'EVENTS:INIT:UNLOAD';

    /**
	 * Default init method
	 */
	const DEFAULT_INIT_METHOD = "__init__";

    /**
     * Self Awareness
     */
    public static $reflection = true;
	
	/**
	 * Load plugins
	 */
	public static function loadPlugins(){

		//load the plugins
		CorePlugin::loadPlugins();
		
	}
	
	/**
	 * Load modules
     *
     * TODO: find better place for this kind of procedural loading
	 */
	public static function loadModules(){

        //register module class loader
        CoreModule::registerClassloader();

        //register class loader
        CoreLogic::registerClassLoader();

		//load the modules
		CoreModule::loadModules();

        //load logic
        CoreLogic::buildReferences();

        //register languages
        CoreLanguage::registerLanguages();

        //register interceptors
        CoreInterceptor::registerInterceptors();

        //register observers
        CoreObserver::registerListeners();

        //register menus
        CoreMenu::registerMenus();

        //register routes
        CoreController::registerRoutes();

        //stand them up
        CoreModule::initModules();

        //load templates
        CoreTemplate::registerViews();

        //load crutches
        CoreCrutches::registerCrutches();

        //register less
        CoreLess::registerLess();

        //register scss
        CoreSCSS::registerSCSS();

        //register scripts
        CoreScript::registerScripts();

	}

    /**
     * Load module assets
     *
     * @param CoreModuleObject $coreModuleObject
     * @return CoreInitAssetsReferenceObject
     */
    public static function loadModuleAssets(CoreModuleObject $coreModuleObject){

        /**
         * Core Init assets reference
         */
        $CoreInitAssetsReferenceObject = new CoreInitAssetsReferenceObject();

        //set relevant info
        $CoreInitAssetsReferenceObject->setName($coreModuleObject->getName());
        $CoreInitAssetsReferenceObject->setContext($coreModuleObject->getPath());

        //load assets
        $CoreInitAssetsReferenceObject->setLess(CoreLess::loadModuleLess($coreModuleObject));
        $CoreInitAssetsReferenceObject->setScss(CoreSCSS::loadModuleSCSS($coreModuleObject));
        $CoreInitAssetsReferenceObject->setScripts(CoreScript::loadModuleScripts($coreModuleObject));

        return $CoreInitAssetsReferenceObject;

    }

    /**
     * Load assets for template
     *
     * @param CoreTemplateObject $coreTemplateObject
     * @return CoreInitAssetsReferenceObject
     */
	public static function loadTemplateAssets(CoreTemplateObject $coreTemplateObject){

        /**
         * Core Init assets reference
         */
        $CoreInitAssetsReferenceObject = new CoreInitAssetsReferenceObject();

        //set relevant info
        $CoreInitAssetsReferenceObject->setName($coreTemplateObject->getNamespace());
        $CoreInitAssetsReferenceObject->setContext($coreTemplateObject->getBasePath());

        //load assets
        $CoreInitAssetsReferenceObject->setLess(CoreLess::loadTemplateLess($coreTemplateObject));
        $CoreInitAssetsReferenceObject->setScss(CoreSCSS::loadTemplateSCSS($coreTemplateObject));
        $CoreInitAssetsReferenceObject->setScripts(CoreScript::loadTemlateScripts($coreTemplateObject));

        /**
         * Return for immediate use
         */
        return $CoreInitAssetsReferenceObject;

	}

    /**
     * Load schema
     *
     * @param null $path
     * @param bool $force
     */
    public static function loadSchema($path = null, $force = false){
        CoreSchema::load($path, $force);
    }

	/**
	 * Load core
	 */
	public static function loadCore(){
		
		//connect to mysql
		CoreData::connectMysql(
			MYSQL_SERVER,
			MYSQL_DATABASE,
			MYSQL_USER,
			MYSQL_PASSWORD
		);

		//set mysql
		CoreData::setSqlStore('mysql');

        /**
         * DEV Mode
         * Schema Creation Toggle
         */

        if(!DEV_MODE){
            if(false === ($forceDevMode = CoreCache::getCache('forceDevMode', true))){
                $forceDevMode = (CoreSqlUtils::tableExists('werock_properties')) ? 'NO' : 'YES';
                CoreCache::saveCache('forceDevMode', $forceDevMode, 86400, true);
            }
            define('FORCE_DEV_MODE', ($forceDevMode == 'YES'));
        }
        if(!defined('FORCE_DEV_MODE')){
            define('FORCE_DEV_MODE', false);
        }

        /**
         * Theme schema files maintain core sql tables
         *
         */
        if((DEV_MODE && BUILD_SCHEMA) || FORCE_DEV_MODE){
            CoreSchema::load('core/session');
            CoreSchema::load('core/prop');
            CoreSchema::load('core/visitor');
            CoreSchema::load('core/module');
        }

        //load Mustache
		CoreTemplate::loadMustache();

        //start a session
        //Note: Requires database connection
        CoreSessionUtils::assureSession();

	}
	
	/**
	 * Identify user or visitor
	 */
	public static function _identify(){
		
		//set visitor
		CoreVisitor::set();
		
	}

    /**
     * Route request
     *
     * @param null $group
     */
	public static function routeRequest($group = null){
	
		/**
		 * Handle the request
		 */
		CoreController::handleRequest($group);
	
	}
	
	/**
	 * Load theme
	 */
	public static function loadTheme(){
		
		/**
		 * Core template
		 */
		
	}

    /**
     * Set default API headers
     */
    public static function setDefaultApiHeaders(){

        /**
         * Set default content type header
         */
        CoreHeaders::add('Content-Type', 'application/json');

    }

    /**
     * Set headers
     */
    public static function setHeaders(){

        /**
         * Check headers not already sent
         */
        if(headers_sent()) CoreLog::error('Headers already sent!');

        /**
         * Set headers
         */
        foreach(CoreHeaders::getAll() as $key => $value){
            try {
                header($key . ': ' . $value, true);
            } catch(Exception $e){
                //ignore
            }
        }

    }

    /**
     * Set api headers
     *
     */
    public static function setApiHeaders(){

        /**
         * Check headers not already sent
         */
        if(headers_sent()) CoreLog::error('Headers already sent!');

        /**
         * Disable redirect on Api calls - not very useful
         * and problematic when using $http or other wrapper which fails on
         * non 200 responses - or will follow redirects
         *
         */
        CoreHeaders::disableRedirect();

        /**
         * Set headers
         */
        foreach(CoreHeaders::getAll() as $key => $value){
            try {
                header($key . ': ' . $value, true);
            } catch(Exception $e){
                //ignore
            }
        }

    }

	/**
	 * Render output
	 */
	public static function render(){

        /**
         * Ouput html string
         */
        echo CoreRender::getOutput();

	}
	
	/**
	 * Render data
	 */
	public static function renderData(){

        /**
         * Build return
         */
        CoreApi::execute();

        /**
         * Output String
         */
        echo CoreApi::getOutput();

    }

	/**
	 * Unload page
	 */
	public static function unload(){

        /** Commit session */
        session_write_close();

        /** Dispatch subscribed events */
        CoreObserver::dispatch(self::EVENT_ON_UNLOAD, null);

    }
	
}