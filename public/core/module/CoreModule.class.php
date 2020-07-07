<?php

/**
 * Core Module Loader Component
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreModule {

    /**
     * Allow reflection
     */
    use ClassReflectionTrait;

    /**
     * Allow interceptors
     */
    use CoreInterceptorTrait;

	/**
	 * Core init configuration
	 */
	const MODULES_FOLDER = 'modules';
	const MODULE_APPEND = 'Module';
	const MODULE_FILE_APPEND = 'Module.class.php';
    const CLASS_APPEND = '.class.php';

    /**
     * Strings
     */
    const SQL_VALUE_KEY = 'werock_module_property_value';
    const SQL_VERSION_KEY = 'werock_module_version';
    const SQL_ENABLED_KEY = 'werock_module_enabled';
    const SQL_MODULE_LOOKUP_KEY = ':module';
    const SQL_KEY_LOOKUP_KEY = ':key';
    const SQL_VALUE_LOOKUP_KEY = ':value';
    const SQL_VERSION_LOOKUP_KEY = ':version';
    const SQL_ENABLED_LOOKUP_KEY = ':enabled';
    const EMPTY_STRING = '';
    const PATH_SLASH = '/';
    const GT = '<';
    const GTE = '<=';
    const LTE = '>=';
    const MIN = 'min';
    const MAX = 'max';
    const COMMA = ',';
    const CLASSLOADER_METHOD = 'classLoader';
    
    /**
     * Werock module interface
     */
    const MODULE_INTERFACE = 'CoreModuleInterface';

    /**
     * Cache keys
     */
    const MODULES_CACHE_KEY = 'modules';
    const MODULES_CACHE_NS = 'modules';

    /**
     * Modules events
     */
    const EVENT_MODULES_LOADED = 'event:modules:loaded';

    /**
	 * Queries
	 */
	const GET_MODULE_SQL = " SELECT * FROM `werock_modules` WHERE `werock_module_name` = :module; ";
	const INSERT_MODULE_SQL = " INSERT INTO `werock_modules` ( `werock_module_name`, `werock_module_version`, `werock_module_enabled`, `werock_module_date_added` ) VALUES ( :module, :version, :enabled, NOW() ); ";
	const GET_MODULE_PROP_SQL = " SELECT * FROM `werock_module_properties` WHERE `werock_module_name` = :module AND `werock_module_property_key` = :key; ";
	const INSERT_MODULE_PROP_SQL = " INSERT INTO `werock_module_properties` ( `werock_module_name`, `werock_module_property_key`, `werock_module_property_value`, `werock_module_property_date_added` ) VALUES ( :module, :key, :value, NOW() ); ";
    const UPDATE_MODULE_PROP_SQL = " UPDATE `werock_module_properties` SET `werock_module_property_value` = :value WHERE `werock_module_name` = :module AND `werock_module_property_key` = :key; ";
    const GET_MODULES_SQL = " SELECT * FROM `werock_modules` ORDER BY `werock_module_name` ASC ";
    const ENABLE_MODULE_SQL = " UPDATE `werock_modules` SET `werock_module_enabled` = 1 WHERE `werock_module_name` = :module; ";
    const DISABLE_MODULE_SQL = " UPDATE `werock_modules` SET `werock_module_enabled` = 0 WHERE `werock_module_name` = :module; ";
    const UPDATE_MODULE_VERSION_SQL = " UPDATE `werock_modules` SET `werock_module_version` = :version WHERE `werock_module_name` = :module; ";

    /**
     * Enabled, disabled modules
     */
    private static $enabledModules = array();
    private static $disabledModules = array();

    /**
     * Modules
     */
    public static $modules = array();

    /**
     * Register logic class loader
     */
    public static function registerClassloader(){
        spl_autoload_register(array(__CLASS__, self::CLASSLOADER_METHOD));
    }

    /**
     * Class loader -- allows for referencing non instantiated modules
     *
     * @param null $name
     */
    public static function classLoader($name = null){
        if(substr($name, -strlen(self::MODULE_APPEND)) == self::MODULE_APPEND){
            require DOCUMENT_ROOT . self::PATH_SLASH . self::MODULES_FOLDER . self::PATH_SLASH . str_ireplace(self::MODULE_APPEND, self::EMPTY_STRING, $name) . self::PATH_SLASH . $name . self::CLASS_APPEND;
        }
    }

    /**
	 * Get module property
     * Note: allows interception
     *
	 * @param String $className
	 * @param String $prop
	 * @param Mixed $defaultValue
	 * @return String
	 */
	public static function _getProp($className = null, $prop = null, $defaultValue = null){
		
		//get module property
		$propRecord = CoreSqlUtils::row(self::GET_MODULE_PROP_SQL, array(
			self::SQL_MODULE_LOOKUP_KEY => str_replace(self::MODULE_APPEND, self::EMPTY_STRING, $className),
			self::SQL_KEY_LOOKUP_KEY => $prop
		));

		//return value if found
		if(isset($propRecord[self::SQL_VALUE_KEY]) || (false !== $propRecord && array_key_exists(self::SQL_VALUE_KEY, $propRecord))){
            return $propRecord[self::SQL_VALUE_KEY];
		}
		
		//insert property
		CoreSqlUtils::insert(self::INSERT_MODULE_PROP_SQL, array(
			self::SQL_MODULE_LOOKUP_KEY => str_replace(self::MODULE_APPEND, self::EMPTY_STRING, $className),
			self::SQL_KEY_LOOKUP_KEY => $prop,
			self::SQL_VALUE_LOOKUP_KEY => $defaultValue			
		));
				
		//return default value
		return $defaultValue;
		
	}

    /**
     * Update property
     * Note: allows interception
     *
     * @param null $className
     * @param null $prop
     * @param null $value
     * @return True
     */
    public static function _setProp($className = null, $prop = null, $value = null){
        CoreSqlUtils::update(self::UPDATE_MODULE_PROP_SQL, array(
            self::SQL_MODULE_LOOKUP_KEY => str_replace(self::MODULE_APPEND, self::EMPTY_STRING, $className),
            self::SQL_KEY_LOOKUP_KEY => $prop,
            self::SQL_VALUE_LOOKUP_KEY => $value
        ));
        return true;
    }

    /**
     * Init module
     *
     * @param CoreModuleObject $Module
     * @param null $versionreq
     * @param null $requestor
     * @return bool
     */
    private static function initModule(CoreModuleObject &$Module, $versionreq = null, $requestor = null){

        /**
         * Cut short if already loaded
         */
        if($Module->isInstantiated() === true || $Module->isLoaded() === true){
            return true;
        }

        /**
         * Get record
         * @var \CoreModuleObject $Module
         */
        $moduleRecord = CoreSqlUtils::row(self::GET_MODULE_SQL, array(self::SQL_MODULE_LOOKUP_KEY => $Module->getName()));

        //check for module class
        if(!is_file($Module->getFile())){
            CoreLog::error('Unable to find module class at ' . $Module->getFile());
            return false;
        }

        /** @var CoreModuleInterface $Instance */
        $Instance = $Module->getName() . CoreModule::MODULE_APPEND;

        //module is loaded
        $Module->setLoaded(true);

        /**
         * Insert reference
         *
         * trigger install method
         */
        if(empty($moduleRecord)){

            try {

                CoreSqlUtils::beginTransaction();

                CoreLog::debug('Running install on module ' . $Module->getName());

                CoreInit::loadSchema(self::PATH_SLASH . self::MODULES_FOLDER . self::PATH_SLASH . $Module->getName(), true);

                $Instance::__install__();

                CoreSqlUtils::insert(self::INSERT_MODULE_SQL, array(
                    self::SQL_MODULE_LOOKUP_KEY => $Module->getName(),
                    self::SQL_VERSION_LOOKUP_KEY => $Module->getVersion(),
                    self::SQL_ENABLED_LOOKUP_KEY => 1
                ));

                CoreSqlUtils::commitTransaction();

            } catch(Exception $e){

                CoreLog::error('Unable to install module ' . $Module->getName() . '. Info: ' . $e->getMessage());
                CoreSqlUtils::rollbackTransaction();

            }

        }

        /**
         * Version number is higher
         *
         * trigger the update method
         *
         */
        if(version_compare($moduleRecord[self::SQL_VERSION_KEY], $Module->getVersion(), self::GT)){

            try {

                CoreSqlUtils::beginTransaction();

                CoreLog::debug('Running upgrade task on module ' . $Module->getName() . ' it went from version ' . $moduleRecord[self::SQL_VERSION_KEY] . ' to ' . $Module->getVersion());

                $Instance::__update__($moduleRecord[self::SQL_VERSION_KEY], $Module->getVersion());

                CoreInit::loadSchema(self::PATH_SLASH . self::MODULES_FOLDER . self::PATH_SLASH . $Module->getName(), true);

                CoreSqlUtils::update(self::UPDATE_MODULE_VERSION_SQL, array(
                    self::SQL_MODULE_LOOKUP_KEY => $Module->getName(),
                    self::SQL_VERSION_LOOKUP_KEY => $Module->getVersion()
                ));

                CoreSqlUtils::commitTransaction();

            } catch(Exception $e){

                CoreLog::error('Unable to upgrade module ' . $Module->getName() . ' from ' . $moduleRecord[self::SQL_VERSION_KEY] . ' to ' . $Module->getVersion());
                CoreSqlUtils::rollbackTransaction();

            }

        }

        /**
         * Run init method
         */
        $method = (CoreInit::DEFAULT_INIT_METHOD);
        $Instance::$method();

        $Module->setInstantiated(true);

        return true;

    }

    /**
     * Check if module is enabled. This method allows ENV specific configuration to override module settings.
     *
     * @param CoreModuleObject $coreModuleObject
     * @param bool $enabled
     * @return bool
     */
    private static function moduleEnabled(CoreModuleObject $coreModuleObject, $enabled){
        if(defined('WEROCK_MODULES_ENABLED')){
            if(in_array($coreModuleObject->getName(), self::$enabledModules)){
                return true;
            }
        }
        if(defined('WEROCK_MODULES_DISABLED')){
            if(in_array($coreModuleObject->getName(), self::$disabledModules)){
                return false;
            }
        }
        return $enabled;
    }

    /**
     * Instantiate all modules
     *
     * @return bool
     */
    public static function initModules(){

        //sanity check
        if(empty(CoreModule::$modules)){
            CoreLog::error('Unable to instantiate modules. Modules reference is empty!');
        }

        /** @var CoreModuleObject $Module */
        foreach(CoreModule::$modules as &$Module){

            if(!$Module->isEnabled()) continue;

            /**
             * Start/Init the module
             */
            self::initModule($Module);

        }

        //modules loaded
        CoreObserver::dispatch(static::EVENT_MODULES_LOADED, null);

        return true;

    }

    /**
     * Load modules
     */
	public static function loadModules(){

        /** @var array modules Attempt to restore */
        static::$modules = CoreCache::getCache(static::MODULES_CACHE_KEY, true, array(self::MODULES_CACHE_NS), false);

        /** Cut short */
        if(!static::$modules){ static::$modules = array(); }else{
            return;
        }

        /**
         * Get enabled, disabled modules
         */
        self::$enabledModules = explode(self::COMMA, WEROCK_MODULES_ENABLED);
        self::$disabledModules = explode(self::COMMA, WEROCK_MODULES_DISABLED);

		/** @var array $moduleFolders */
		$moduleFolders = CoreFilesystemUtils::readFolders(DOCUMENT_ROOT . CoreModule::MODULES_FOLDER);

		/** Assertion */
		if(empty($moduleFolders)) CoreLog::error('No modules in ' . DOCUMENT_ROOT . CoreModule::MODULES_FOLDER);

		/** @var String $moduleFolder */
        foreach($moduleFolders as $moduleFolder){

            self::discoverModule($moduleFolder, $moduleFolders);

		}

        /** Cache modules */
        CoreCache::saveCache(static::MODULES_CACHE_KEY, static::$modules, 86400, true, array(self::MODULES_CACHE_NS), false);

	}

    /**
     * Discover module
     *
     * @param null $moduleFolder
     * @param array $moduleFolders
     * @param null $versionReq
     * @param null $requestingModule
     * @return boolean
     */
    final private static function discoverModule($moduleFolder = null, $moduleFolders = array(), $versionReq = null, $requestingModule = null){

        /** @var CoreModuleObject $ExistingModule */
        $ExistingModule = isset(CoreModule::$modules[$moduleFolder]) ? CoreModule::$modules[$moduleFolder] : false;
        if($ExistingModule && $ExistingModule->isDiscovered()){
            return true;
        }

        /**
         * Get module record
         * @var CoreModuleObject $Module
         */
        $moduleRecord = CoreSqlUtils::row(self::GET_MODULE_SQL, array(self::SQL_MODULE_LOOKUP_KEY => $moduleFolder));

        /** @var string $moduleFolderPath */
        $moduleFolderPath = DOCUMENT_ROOT . self::PATH_SLASH . CoreModule::MODULES_FOLDER . self::PATH_SLASH . $moduleFolder;
        /** @var string $moduleFile */
        $moduleFile = DOCUMENT_ROOT . self::PATH_SLASH . CoreModule::MODULES_FOLDER . self::PATH_SLASH . $moduleFolder . self::PATH_SLASH . $moduleFolder . self::MODULE_FILE_APPEND;

        /** @var CoreModuleObject $coreModuleObject */
        $coreModuleObject = new CoreModuleObject();
        $coreModuleObject->setDiscovered(true);
        $coreModuleObject->setName($moduleFolder);
        $coreModuleObject->setFile($moduleFile);
        $coreModuleObject->setPath($moduleFolderPath);
        $coreModuleObject->setEnabled(self::moduleEnabled($coreModuleObject, ((!isset($moduleRecord[self::SQL_ENABLED_KEY]) || (isset($moduleRecord[self::SQL_ENABLED_KEY]) && $moduleRecord[self::SQL_ENABLED_KEY] == 1)))));
        $coreModuleObject->setLogic(null);

        /** @var CoreModuleInterface $Instance */
        $Instance = $coreModuleObject->getName() . CoreModule::MODULE_APPEND;

        /** Set module information */
        $coreModuleObject->setModuleName($Instance::$name);
        $coreModuleObject->setDescription($Instance::$description);
        $coreModuleObject->setVersion($Instance::$version);
        $coreModuleObject->setDependencies($Instance::$dependencies);

        /** @var array $interfaces - assure interface is implemented */
        $interfaces = class_implements($Instance);
        if(!in_array(self::MODULE_INTERFACE, $interfaces)){
            CoreLog::error('Module ' . $coreModuleObject->getName() . ' must implement ' . self::MODULE_INTERFACE);
            return false;
        }

        /** Check for init method */
        if(!method_exists($Instance, CoreInit::DEFAULT_INIT_METHOD)){
            CoreLog::error($Instance . ' needs method ' . CoreInit::DEFAULT_INIT_METHOD);
            return false;
        }

        /** Check if enabled */
        if(!self::moduleEnabled($coreModuleObject, (!isset($moduleRecord[self::SQL_ENABLED_KEY]) || (isset($moduleRecord[self::SQL_ENABLED_KEY]) && $moduleRecord[self::SQL_ENABLED_KEY] == 1)))){
            $coreModuleObject->setEnabled(false);
            $coreModuleObject->setInstantiated(false);
        }



        /**
         * Handle dependencies
         * only if Module is enabled
         */
        $dependencies = $coreModuleObject->getDependencies();
        if($coreModuleObject->isEnabled() && !empty($dependencies)){
            foreach($dependencies as $moduleName => $versionRequirement){

                $requiredModuleFolder = null;

                foreach($moduleFolders as $thisModuleFolder){
                    if($thisModuleFolder == $moduleName){
                        $requiredModuleFolder = $thisModuleFolder;
                    }
                }

                //make sure module is available
                if(null == $requiredModuleFolder){
                    CoreLog::error('Unable to find ' . $Module->getName() . ' dependency ' . $moduleName);
                    continue;
                }

                //load the module
                self::discoverModule($requiredModuleFolder, $moduleFolders, $versionRequirement, $coreModuleObject->getName());

            }
        }

        /** Save reference */
        CoreModule::$modules[$moduleFolder] = $coreModuleObject;

        /** Skip */
        if(!$coreModuleObject->isEnabled()) return;

        /** Make sure version is in range of requestor */
        if(isset($versionReq[self::MAX]) && isset($versionReq[self::MIN])){
            if(!version_compare($versionReq[self::MIN], $coreModuleObject->getVersion(), self::GTE) || !version_compare($versionReq[self::MAX], $coreModuleObject->getVersion(), self::LTE)){
                CoreLog::error('Requestor ' . $requestingModule . ' requires ' . $coreModuleObject->getName() . ' (version: ' . $coreModuleObject->getVersion() . ') is out of range ' . $versionReq[self::MIN] . '-' . $versionReq[self::MAX]);
                return false;
            }
            if(!$coreModuleObject->isEnabled()){
                CoreLog::error('Requestor ' . $requestingModule . ' requires disabled module: ' . $coreModuleObject->getName());
                return false;
            }
        }

        /**
         * Populate module reference object
         */
        $coreModuleObject->setRoutes($Instance::getRoutes());
        $coreModuleObject->setMenus($Instance::getMenus());
        $coreModuleObject->setInterceptors($Instance::getInterceptors());
        $coreModuleObject->setObservers($Instance::getListeners());


        /** Load language files */
        $coreModuleObject->setLanguages(CoreLanguage::loadLanguages($coreModuleObject));

        /** Set logic components */
        $coreModuleObject->setInterfaces(CoreLogic::loadInterfaces($coreModuleObject));
        $coreModuleObject->setRepositories(CoreLogic::loadRepositories($coreModuleObject));
        $coreModuleObject->setProcedures(CoreLogic::loadProcedures($coreModuleObject));
        $coreModuleObject->setServices(CoreLogic::loadServices($coreModuleObject));
        $coreModuleObject->setObjects(CoreLogic::loadObjects($coreModuleObject));
        $coreModuleObject->setExceptions(CoreLogic::loadExceptions($coreModuleObject));
        $coreModuleObject->setActions(CoreLogic::loadActions($coreModuleObject));

        /**
         * Populate menus and routes from embedded actions
         */
        $actions = $coreModuleObject->getActions();
        if(!empty($actions)){
            /** @var CoreLogicObject $CoreLogicObject */
            foreach($actions as $CoreLogicObject){

                require $CoreLogicObject->getPath() . self::PATH_SLASH . $CoreLogicObject->getClass();

                $ActionName = $CoreLogicObject->getName();

                /** @var CoreRenderTemplateInterface $TheAction */
                $TheAction = new $ActionName;

                $menus = $TheAction->getMenus();
                if(!empty($menus)){
                    $coreModuleObject->setMenus(CoreArrayUtils::mergeArrays($coreModuleObject->getMenus(), $menus));
                }
                $routes = $TheAction->getRoutes();
                if(!empty($routes)){
                    $coreModuleObject->setRoutes(CoreArrayUtils::mergeArrays($coreModuleObject->getRoutes(), $routes));
                }

            }
        }

        /** Set crutches */
        $coreModuleObject->setCrutches(CoreCrutches::loadCrutches($coreModuleObject));

        /** Set views */
        $coreModuleObject->setViews(CoreTemplate::loadTemplates($coreModuleObject));

        /** Set assets */
        $coreModuleObject->setCoreInitAssetsReferenceObject(CoreInit::loadModuleAssets($coreModuleObject));

    }

    /**
     * Return all modules
     * Note: Allows interception
     *
     * @return array
     */
    final public static function _getAll(){
        return CoreModule::$modules;
    }

    /**
     * Disable module
     * Note: Allows interception
     *
     * @param null $module
     * @return bool|True
     */
    final public static function _disable($module = null){

        if(!isset(self::$modules[$module])){
            CoreLog::error('Unable to find module by name: ' . $module);
            return false;
        }

        if(self::isActiveDependency(self::$modules[$module])){
            CoreLog::error('Unable to disable ' . $module . ' for it is an active dependency');
            return false;
        }

        try {

            CoreSqlUtils::beginTransaction();

            /** @var CoreModuleInterface $Instance */
            $Instance = self::$modules[$module]->getName() . CoreModule::MODULE_APPEND;

            //run disable
            $Instance::__disable__();

            $update = CoreSqlUtils::update(self::DISABLE_MODULE_SQL, array(self::SQL_MODULE_LOOKUP_KEY => $module));

            CoreSqlUtils::commitTransaction();

            return $update;

        } catch(Exception $e){

            CoreLog::error('Unable to disable module ' . self::$modules[$module]->getName() . ' Info: ' . $e->getMessage());

        }

        return false;

    }

    /**
     * Enable module
     * Note: Allows interception
     *
     * @param null $module
     * @return bool|True
     */
    final public static function _enable($module = null){

        if(!isset(self::$modules[$module])){
            CoreLog::error('Unable to find module by name: ' . $module);
            return false;
        }

        try {

            CoreSqlUtils::beginTransaction();

            /** @var CoreModuleInterface $Instance */
            $Instance = self::$modules[$module]->getName() . CoreModule::MODULE_APPEND;

            //run disable
            $Instance::__enable__();

            $update =  CoreSqlUtils::update(self::ENABLE_MODULE_SQL, array(self::SQL_MODULE_LOOKUP_KEY => $module));

            CoreSqlUtils::commitTransaction();

            return $update;

        } catch(Exception $e){

            CoreLog::error('Unable to enable module ' . self::$modules[$module]->getName() . ' Info: ' . $e->getMessage());

        }

        return false;

    }

    /**
     * Check if module is an active dependency
     * Note: Allows interception
     *
     * @param CoreModuleObject $thisModule
     * @return bool
     */
    final public static function _isActiveDependency(CoreModuleObject $thisModule){

        /** @var CoreModuleObject $module */
        foreach(self::$modules as $key => $module){
            if($module->isEnabled()){
                $dependencies = $module->getDependencies();
                if(isset($dependencies[$thisModule->getName()])){
                    return true;
                }
            }
        }

        return false;

    }
	
}