<?php

/**
 * Core Crutches
 * keeps track of any crutches requested by werock components
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreCrutches {

    /**
     * Allow reflection
     */
    use ClassReflectionTrait;

    /**
     * Allow interceptors
     */
    use CoreInterceptorTrait;

	/**
	 * Keys
	 */
	const CRUTCH_FILE = 'file';
	const CRUTCH_TYPE = 'type';
	const CRUTCH_BASE_PATH = 'basePath';
    const PATH_VERSION = '?v=';

    /**
     * Constants
     */
    const SLASH = '/';
	
	/**
	 * Crutches folder
	 */
	const CRUTCHES_FOLDER = "crutches";
	const CRUTCH_CONTENT = "content";
	const CRUTCH_FILENAME = "crutch.php";
	const CRUTCH_FILE_PLACEHOLDER = "[[[file]]]";

	/**
	 * Public document keys
	 */
	const CRUTCHES = "crutches";	
	const DOCUMENT_HEAD = "document:head";
	const DOCUMENT_BODY = "document:body";

    /**
     * Cache keys
     */
    const CACHE_CRUTCHES_NS = 'crutches';
    const CACHE_CRUTCHES_KEY = 'crutches';

    /**
     * Cached
     *
     * @var bool
     */
    public static $cached = false;

    /**
	 * Crutches 
	 * and registered crutches
	 */
	public static $crutches = array();
	private static $registered_crutches = array();

 	/**
	 * White-list a crutch
	 * @param String/Array $crutch_name(s) UserRegisterAction crutch
	 */
	public static function useCrutch($crutch = null){
		
		//sanity check
		if(empty($crutch) || !is_string($crutch)){
			CoreLog::error('Cannot understand crutch type ' . serialize($crutch));
		}

		//check to see if one has already been loaded
        //if not throw an exception
		if(!self::haveCrutch($crutch)){
			CoreLog::error('Did not find crutch [' . $crutch . ']');
		}

        //crutch is already loaded
        if(in_array($crutch, self::$registered_crutches)) return;

		//stack registered crutch
		array_push(self::$registered_crutches, $crutch);
		
	}

    /**
     * Get crutches
     * @param String $crutch
     * @return Boolean true
     */
    public static function haveCrutch($crutch = null){
        foreach(self::$crutches as $key => $crutchList){
            foreach($crutchList as $crutchKey => $theCrutch){
                if($crutchKey == $crutch){
                    return true;
                }
            }
        }
        return false;
    }

	/**
	 * Get crutches
	 * @param String $key
	 * @return crutches stack
	 */
	public static function getCrutches($key = null){
		return isset(self::$crutches[$key]) ? self::$crutches[$key] : false;
	}
	
	/**
	 * Get marked crutches only
	 * @param String $key crutches category key
	 * @return Array crutches
	 */
	public static function getMarkedCrutches($key = null){
		$returnCrutches = array();
		if(isset(self::$crutches[$key]) && !empty(self::$crutches[$key])){
			if(isset(self::$registered_crutches) && !empty(self::$registered_crutches)){
				foreach(self::$registered_crutches as $registered_crutch){
					if(isset(self::$crutches[$key][$registered_crutch]) && !empty(self::$crutches[$key][$registered_crutch])){

                        /** @var array $CoreCrutchObjects */
                        $CoreCrutchObjects = self::$crutches[$key][$registered_crutch];

                        /** Handle dependencies */
                        if(!empty($CoreCrutchObjects)){
                            /** @var CoreCrutchObject $CoreCrutchObject */
                            foreach($CoreCrutchObjects as $CoreCrutchObject){
                                $dependencies = $CoreCrutchObject->getDependencies();
                                if(!empty($dependencies)){
                                    foreach($dependencies as $dependency){
                                        if(!array_key_exists($dependency, $returnCrutches)){
                                            if(!self::haveCrutch($dependency)) {
                                                CoreLog::error('Unable to find dependency[' . $dependency . '] for crutch[' . $registered_crutch . ' ]');
                                            }
                                            if(!isset(self::$crutches[$key][$dependency])) {
                                                CoreLog::error('Have crutch[' . $dependency . '] but cannot find it in list[' . $key . ']');
                                            }
                                            $returnCrutches[$dependency] = self::$crutches[$key][$dependency];
                                        }

                                    }
                                }
                            }
                            $returnCrutches[$registered_crutch] = $CoreCrutchObjects;
                        }

                    }
				}
			}
		}

		return $returnCrutches;
	}

    /**
     * Cache all
     */
    public static function cacheAll(){

        //store cache
        CoreCache::saveCache(static::CACHE_CRUTCHES_KEY, static::$crutches, 0, true);

    }

    /**
     * Set from cache
     */
    public static function setFromCacheAll(){

        //set from cached
        if(false !== ($set = CoreCache::getCache(static::CACHE_CRUTCHES_KEY, true))){
            static::$crutches = $set;
            static::$cached = true;
        }

    }

    /**
     * Register crutches
     *
     */
    public static function registerCrutches(){
        static::$crutches = CoreCache::getCache(static::CACHE_CRUTCHES_KEY, true, array(self::CACHE_CRUTCHES_NS), false);
        if(!empty(static::$crutches)) return;
        static::$crutches = array();
        foreach(CoreModule::$modules as $coreModuleObject) {
            $crutches = $coreModuleObject->getCrutches();
            if(!empty($crutches)) {
                foreach ($crutches as $crutchAssets) {

                    /** @var CoreCrutchObject $CoreCrutchObject */
                    foreach ($crutchAssets as $CoreCrutchObject) {

                        /**
                         * Lets see if this crutch has already
                         * been registered
                         */
                        if (isset(self::$crutches[self::CRUTCHES][$CoreCrutchObject->getName()])) {
                            return null;
                        }

                        if (!$CoreCrutchObject->hasType()) continue;
                        switch ($CoreCrutchObject->getType()) {

                            /**
                             * Add asset to head block
                             */
                            case self::DOCUMENT_HEAD:

                                //make sure key exists
                                if (!isset(self::$crutches[self::DOCUMENT_HEAD])) {
                                    self::$crutches[self::DOCUMENT_HEAD] = array();
                                }

                                //assure holder exists
                                if (!isset(self::$crutches[self::DOCUMENT_HEAD][$CoreCrutchObject->getName()])) {
                                    self::$crutches[self::DOCUMENT_HEAD][$CoreCrutchObject->getName()] = array();
                                }

                                //stack crutch
                                array_push(self::$crutches[self::DOCUMENT_HEAD][$CoreCrutchObject->getName()], $CoreCrutchObject);

                                break;

                            /**
                             * Add asset to body block
                             */
                            case self::DOCUMENT_BODY:

                                //make sure key exists
                                if (!isset(self::$crutches[self::DOCUMENT_BODY])) {
                                    self::$crutches[self::DOCUMENT_BODY] = array();
                                }

                                //assure holder exists
                                if (!isset(self::$crutches[self::DOCUMENT_BODY][$CoreCrutchObject->getName()])) {
                                    self::$crutches[self::DOCUMENT_BODY][$CoreCrutchObject->getName()] = array();
                                }

                                //stack crutch
                                array_push(self::$crutches[self::DOCUMENT_BODY][$CoreCrutchObject->getName()], $CoreCrutchObject);

                                break;

                        }

                    }
                }
            }
        }
        CoreCache::saveCache(static::CACHE_CRUTCHES_KEY, static::$crutches, 86400, true, array(self::CACHE_CRUTCHES_NS), false);
    }

	/**
	 * Stack a crutch
	 * @param String $basePath
	 * @param String $name
	 * @param Array $crutchAssets
     *
     * @@return Array $crutchAssets
	 */
	public static function registerCrutch($basePath = null, $name = null, $crutchAssets = array()){
	
		/**
		 * Stack the assets
		 */
		foreach($crutchAssets as &$crutchAsset){
			
			/**
			 * Add in base path
             * The new structure uses an object to describe a crutch
			 */
			if(is_object($crutchAsset) && get_class($crutchAsset) == CoreCrutchObject::class){

                /** @var CoreCrutchObject $crutchAsset */
                $crutchAsset->setBasePath($basePath);
                $crutchAsset->setName($name);

            }else{
                CoreLog::error('Invalid crutch! Was expecting crutch of type CoreCrutchObject');
            }

		}

        return $crutchAssets;
		
	}

    /**
     * Load crutches
     *
     * @param CoreModuleObject $coreModuleObject
     * @return array
     */
    public static function _loadCrutches(CoreModuleObject $coreModuleObject){

		/**
		 * Crutches
		 */
		$crutchFolders = CoreFilesystemUtils::readFolders($coreModuleObject->getPath() . self::SLASH . self::CRUTCHES_FOLDER);

        /**
         * Return crutches
         */
        $theCrutches = array();

		/**
		 * Crutch folder
		 */
		if(!empty($crutchFolders)){
			foreach($crutchFolders as $crutchFolder){

				/**
				 * Get crutch base path
				 */
				$crutchBasepath = $coreModuleObject->getPath() . self::SLASH . self::CRUTCHES_FOLDER . self::SLASH . $crutchFolder;

				/**
				 * Get crutch path
				 */
				$crutchFile = $crutchBasepath . self::SLASH . self::CRUTCH_FILENAME;				

				/**
				 * Lets make sure the crutch exists
				 */
				if(is_file($crutchFile)){
										
					/**
					 * Load crutch
					 */
					require($crutchFile);
					
					/**
					 * If crutch is found - lets stack it up
					 */
					if(isset($crutch)){
						
						/**
						 * UserRegisterAction crutch
						 */
                        $theCrutches[$crutchFolder] = self::registerCrutch($crutchBasepath . self::SLASH . self::CRUTCH_CONTENT . self::SLASH, $crutchFolder, $crutch);
						
					}
					
				}
				
			}
			
		}

        /**
         * The crutches
         */
        return $theCrutches;
		
	}

    /**
     * Render crutch
     * Note: Allows interception
     *
     * @param CoreCrutchObject $crutchAsset
     * @return bool
     */
	public static function _renderCrutch(CoreCrutchObject $crutchAsset){

        try {

            /**
             * We'll need to fix the file path
             * looking to replace
             * CoreCrutches::CRUTCH_FILE_PLACEHOLDER
             */
            if($crutchAsset->hasAttr()){
                $attr = $crutchAsset->getAttr();
                foreach($attr as $attrKey => &$attrValue){
                    if($attrValue == CoreCrutches::CRUTCH_FILE_PLACEHOLDER){
                        $attrValue = str_replace(CoreCrutches::CRUTCH_FILE_PLACEHOLDER, HTTP_PROTOCOL . DOMAIN_NAME . $crutchAsset->getWebPath() . $crutchAsset->getFile() . self::PATH_VERSION . $crutchAsset->getVersion(), $attrValue);
                    }
                }
                $crutchAsset->setAttr($attr);
            }

            /**
             * Get tag
             */
            $tag = $crutchAsset->getTag();

            /**
             * Render html element
             */
            return CoreHtmlUtils::$tag(null, $crutchAsset->getAttr());

        } catch(Exception $e){

            /**
             * Handle error
             */
            CoreLog::error('Unable to render html string from crutch: ' . serialize($crutchAsset) . ' Info: ' . $e->getMessage());

        }

        return false;

	}
	
}