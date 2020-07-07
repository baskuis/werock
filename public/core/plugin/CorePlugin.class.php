<?php

/**
 * Core Plugin
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
class CorePlugin {

	/**
	 * Core init configuration
	 * example: AdvancedProfilePlugin.class.php
	 * Plugin configuration
	 */
	const PLUGINS_FOLDER = "plugins";
	const PLUGIN_APPEND = "Plugin";
	const PLUGIN_FILE_APPEND = "Plugin.class.php";

    /**
     * Cache keys
     */
    const PLUGINS_CACHE_KEY = "plugins";

    /**
     * Events related to plugins
     */
    const EVENT_PLUGINS_LOADED = "event:plugins:loaded";
    const EVENT_PLUGINS_LOADING = "event:plugins:loading";
    const EVENT_PLUGINS_INSTANTIATED = "event:plugins:instantiated";
    const EVENT_PLUGINS_INSTANTIATING = "event:plugins:instantiating";

    /**
     * Internal Plugins reference
     * @var array
     */
    private static $plugins = array();

    /**
     * @param array $plugins
     */
    public static function setPlugins($plugins)
    {
        self::$plugins = $plugins;
    }

    /**
     * @return array
     */
    public static function getPlugins()
    {
        return self::$plugins;
    }

	/**
	 * Load plugins
	 */
	static public function loadPlugins(){

        //attempt to get cached
        self::$plugins = CoreCache::getCache(self::PLUGINS_CACHE_KEY, true);

        //re-set to array if not found
        if(!self::$plugins){
            self::$plugins = array();
        }

        /**
         * Core observer
         * before loading plugins
         */
        CoreObserver::dispatch(static::EVENT_PLUGINS_LOADING, null);

        /**
         * Return plugins if already loaded
         */
        if(!empty(self::$plugins)){
            return self::$plugins;
        }

		//get plugin folders
		$pluginFolders = CoreFilesystemUtils::readFolders(DOCUMENT_ROOT . self::PLUGINS_FOLDER);
				
		//check if any
		if(empty($pluginFolders)){ return null; }
		
		//load the plugins
		foreach($pluginFolders as $pluginFolder){
				
			//check for plugin class
			if(is_file(DOCUMENT_ROOT . "/" . self::PLUGINS_FOLDER . "/" . $pluginFolder . "/" . $pluginFolder . self::PLUGIN_FILE_APPEND)){

                /**
                 * Create Core Plugin Object
                 */
                $CorePluginObject = new CorePluginObject();
                $CorePluginObject->setName($pluginFolder);
                $CorePluginObject->setPath(DOCUMENT_ROOT . "/" . self::PLUGINS_FOLDER . "/" . $pluginFolder);
                $CorePluginObject->setFile(DOCUMENT_ROOT . "/" . self::PLUGINS_FOLDER . "/" . $pluginFolder . "/" . $pluginFolder . self::PLUGIN_FILE_APPEND);
                $CorePluginObject->setLogic(null);

                /**
                 * Stack plugin
                 */
                self::$plugins[$pluginFolder] = $CorePluginObject;

			}else{

                /**
                 * Handle error
                 */
                CoreLog::error("Unable to find valid plugin in: " . DOCUMENT_ROOT . "/" . self::PLUGINS_FOLDER . "/" . $pluginFolder . "/");

            }
		
		}

        /**
         * Store cached
         */
        CoreCache::saveCache(self::PLUGINS_CACHE_KEY, new ArrayObject(self::$plugins), 0, true);

        /**
         * Plugins loaded
         */
        CoreObserver::dispatch(self::EVENT_PLUGINS_LOADED, null);

	}

    /**
     * Instantiate plugins
     */
    static function instantiateAll(){

        //skip if null
        if(empty(self::$plugins)){
            return null;
        }

        /**
         * Instantiating plugins
         */
        CoreObserver::dispatch(static::EVENT_PLUGINS_INSTANTIATING, null);

        /**
         * @var \CorePluginObject $CorePluginObject
         */
        foreach(self::$plugins as $pluginName => $CorePluginObject){

            //load the plugin
            require $CorePluginObject->getFile();

            //class name
            $plugin_class_name = $CorePluginObject->getName() . self::PLUGIN_APPEND;

            //load class
            $plugin = new $plugin_class_name();

            //method exist
            if(method_exists($plugin, CoreInit::DEFAULT_INIT_METHOD)){
                $method = CoreInit::DEFAULT_INIT_METHOD;
                $plugin->$method();
            }

        }

        /**
         * Plugins instantiated
         */
        CoreObserver::dispatch(static::EVENT_PLUGINS_INSTANTIATED, null);

    }
	
}