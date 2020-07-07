<?php

/**
 * Required for minification
 */
require('inc/JSMin.class.php');

/**
 * Core Script
 * concatenates werock script into a single resource
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */

/**
 * Manager js scripts
 */
class CoreScript {
	
	/**
	 * Script folder
	 */
	const SCRIPT_FOLDER = 'script';

	/**
	 * Resource file prepend
	 */
	const SCRIPT_PREPEND = 'script-';

    /**
     * Cache keys
     */
    const CACHE_SCRIPT_KEY = 'scripts';
	const CACHE_SCRIPT_NS = 'scripts';
	const CACHE_SCRIPT_LAST_KEY = 'lasttsscript';

	/**
	 * Constants
	 */
	const SLASH = '/';
	const EMPTY_STRING = '';
	
	/**
	 * Cached
	 *
	 * @var bool
	 */
	public static $cached = false;

    /**
	 * Latest modification date
	 */
	private static $latestModificationDate = 0;
	
	/**
	 * Keep count of scripts for this request
	 */
	public static $scriptStack = array();

	/**
	 * @var bool $restoredFromCache
	 */
	public static $restoredFromCache = false;

	/**
	 * Request scoped reference
	 * to prevent duplicates
	 *
	 * @var array
	 */
	public static $requestScopedReferences = array();

	/**
	 * Request scoped javascript
	 *
	 * @var string
	 */
    public static $requestScopedScript = '';

    /**
     * Append request scoped javascriptscript
     *
     * @param string $script
	 * @param string $reference
	 * @param bool $once
     */
    public static function appendRequestScopedScript($script = null, $reference = null, $once = false){
		if(true === $once && in_array($reference, self::$requestScopedReferences)) return;
        self::$requestScopedScript .= "\n" . '//Template: ' . $reference . "\n" . $script . "\n";
		if(true === $once) array_push(self::$requestScopedReferences, $reference);
    }

    /**
     * Get request scoped script
     *
     * @return mixed
     */
    public static function getRequestScopedScript(){
        return CoreHtmlUtils::script(self::$requestScopedScript, array('type' => 'text/javascript'));
    }

	/**
	 * Load script string
	 * @param String $string
	 * @return void
	 */
	public static function loadScriptString($string = null){

		//create temp script
		if(false !== $filename = self::getScriptStringTempFileName($string)){

			/**
			 * UserRegisterAction crutch
			 */					
			self::register(CoreResources::RESOURCES_FOLDER . self::SLASH . $filename);
			
			//create on if it doesn't already exist
			if(!CoreResources::exists($filename)){

				//store temp js file
				if(false !== CoreResources::store($filename, $string)){
			
					//all good
					return true;
				
				}
			
			}else{
					
				//already had a temp copy
				return true;
				
			}
		
		}
		
		//handle error
		CoreLog::error('Unable to load script by string');
		
		//something went wrong
		return false;
		
	}

	/**
	 * Load module scripts
	 *
	 * @param CoreModuleObject $coreModuleObject
	 * @return Array
	 */
	public static function loadModuleScripts(CoreModuleObject $coreModuleObject){
		return self::loadScripts($coreModuleObject->getPath());
	}

	/**
	 * Load template scripts
	 *
	 * @param CoreTemplateObject $coreTemplateObject
	 * @return Array $theScripts All discovered scripts
	 */
	public static function loadTemlateScripts(CoreTemplateObject $coreTemplateObject){
		return self::loadScripts($coreTemplateObject->getBasePath());
	}

	/**
	 * Register scripts
	 */
	public static function registerScripts(){

		self::$scriptStack = CoreCache::getCache(static::CACHE_SCRIPT_KEY, true, array(self::CACHE_SCRIPT_NS), false);

		if(!empty(self::$scriptStack)) return;

		self::$scriptStack = array();

		/** @var CoreModuleObject $coreModuleObject */
		foreach(CoreModule::$modules as $coreModuleObject){
			
			if(null != $coreModuleObject->getCoreInitAssetsReferenceObject()){
				foreach($coreModuleObject->getCoreInitAssetsReferenceObject()->getScripts() as $script){
					self::register($script);
				}
			}
			
			$views = $coreModuleObject->getViews();
			if(!empty($views)) {
				/** @var CoreTemplateObject $coreTemplateObject */
				foreach ($views as $coreTemplateObject) {
					if (null != $coreTemplateObject->getCoreAssetsReferenceObject()) {
						foreach ($coreTemplateObject->getCoreAssetsReferenceObject()->getScripts() as $script) {
							self::register($script);
						}
					}
				}
			}

		}

		CoreCache::saveCache(static::CACHE_SCRIPT_KEY, static::$scriptStack, 86400, true, array(self::CACHE_SCRIPT_NS), false);

	}

	/**
	 * Load scripts filed
     *
	 * @param CoreTemplateObject $coreTemplateObject
	 * @return Array $theScripts All discovered scripts
	 */	
	public static function loadScripts($path = null){
		
		/**
		 * Less base path
		 */
		$scriptFolder = $path . self::SLASH . self::SCRIPT_FOLDER;

        /**
         * Return all scripts found
         */
        $theScripts = array();

		/**
		 * Get less files
		 */
		$scriptFiles = CoreFilesystemUtils::readFiles($path . self::SLASH . self::SCRIPT_FOLDER);
		
		/**
		 * UserRegisterAction less files
		 */
		if(!empty($scriptFiles)){
			foreach($scriptFiles as $scriptFile){
				
				/**
				 * Get crutch base path
				 */
				$scriptFilePath = $path . self::SLASH . self::SCRIPT_FOLDER . self::SLASH . $scriptFile;
							
				/**
				 * Lets make sure the crutch exists
				 */
				if(is_file($scriptFilePath)){

                    /**
                     * Stack for return
                     */
                    array_push($theScripts, $scriptFilePath);
					
				}
			}
		}

        /**
         * Return all found scripts for reference
         */
        return $theScripts;

	}

	/**
	 * Add script file
	 * @param String $path
	 * @return void
	 */
	public static function register($path = null){

		/**
		 * skip if there - no dubs
		 */
		if(in_array($path, self::$scriptStack)){
			//TODO: find root cause
			return;
		}

		/**
		 * Add less file to stack
		 */
		array_push(self::$scriptStack, $path);
		
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
		if(empty(self::$scriptStack)){
			return 0;
		}

		/**
		 * Step through less files
		 */
		foreach(self::$scriptStack as $scriptFile){
			
			/**
			 * Update latest modified
			 */
			if(($latestModDate = filemtime($scriptFile)) > self::getLatestModified()){
				self::setLatestModified((int) $latestModDate);
			}

		}

		/**
		 * Get last modified date
		 */
		return self::getLatestModified();
		
	}
	
	/**
	 * Get temp script filename
	 * @param String file key
	 * @return String file name
	 */
	private static function getScriptStringTempFileName($key){
		return self::SCRIPT_PREPEND . md5($key) . '-temp.js';
	}
	
	/**
	 * Get script filename
	 */
	private static function getScriptFileName(){

		$response = CoreCache::getCache(self::CACHE_SCRIPT_LAST_KEY, true, array(self::CACHE_SCRIPT_NS), false);

		if(!empty($response) && STATIC_ASSET_CACHING_ENABLED) return $response;

		$response = self::SCRIPT_PREPEND . md5(self::SCRIPT_FOLDER . self::findLatestModified() . serialize(self::$scriptStack)) . '.js';

		CoreCache::saveCache(self::CACHE_SCRIPT_LAST_KEY, $response, 86400, true, array(self::CACHE_SCRIPT_NS), false);

		return $response;
	}
	
	/**
	 * Get script file
	 * @return String script file
	 */
	public static function getScriptFile(){
		
		//generate filename
		$filename = self::getScriptFileName();
		
		//see if file exists
		if(!CoreResources::exists($filename)){
			
			//compile and store the script
			if(false === CoreResources::store($filename, self::compileScript())){

				/**
				 * Log this incident
				 * script file could not
				 * be written to
				 */
				CoreError::error("Unable to write to script file [" . $filename . "]");
			
			}
			
		}
		
		//return path name
		return CoreResources::getPath($filename);
	}
	
	/**
	 * Minify javascript string
	 * @param String $string
	 * @return String Javascript string
	 */ 
	public static function minifyJavascriptString($string = null){

		//attempt to minify
		try {

			/**
			 * Include js min
			 */
			if(!class_exists('JSMin')){ require('inc/JSMin.class.php'); }
			
			/**
			 * Append minified
			 */
			return JSMin::minify($string);
		
		} catch (Exception $e) {
			
			/**
			 * Log this incident
			 */
			CoreLog::error("Unable to minify js. Info: " . $e->getMessage());

		}	
		
		//return default string
		return $string;	

	}

	private static function wrap($source = null, $script = null){
		return
			'
try {
	' . $script .
'} catch(e) {
	if(typeof console.log !== \'undefined\'){
		console.log(\'Uncaught error: \', \'' . str_ireplace(DOCUMENT_ROOT, self::EMPTY_STRING, $source) . '\');
		console.log(\'run-time error\', e);
	}
};' . "\n";
	}
	
	/**
	 * Compile less
	 */
	public static function compileScript(){
		
		/**
		 * Quick sanity check
		 */
		if(empty(self::$scriptStack)){
			return false;
		}
		
		/**
		 * script string holder
		 */
		$script = null;

		/**
		 * Compile less files
		 */
		foreach(self::$scriptStack as $scriptFile){
				
			/**
			 * Attempt to compile less file
			 */
			try {
			
				/**
				 * Script string
				 */
				$scriptString = null;
			
				/**
				 * Add label
				 */
				$script .= "\n\n" . '/* source:' . str_ireplace(DOCUMENT_ROOT, CoreStringUtils::EMPTY_STRING, $scriptFile) . ' */' . "\n";
				
				/**
				 * If minification is requested
				 */
				if(MINIFY_JAVASCRIPT){
				
					//attempt to minify
					try {
	
						/**
						 * Append minified
						 */
						$script .= self::wrap($scriptFile, JSMin::minify(file_get_contents($scriptFile)));
					
					} catch (Exception $e) {
						
						/**
						 * Log this incident
						 */
						CoreLog::error("Unable to minify js. Info: " . $e->getMessage());
						
						/**
						 * Append script string
						 */			 
						$scriptString .= self::wrap($scriptFile, file_get_contents($scriptFile));
					
					}
		
				}else{

					/**
					 * Append script string
					 */			 
					$scriptString .= self::wrap($scriptFile, file_get_contents($scriptFile));
										
				}
		
				/**
				 * Stack to combined script string
				 */
				$script .= $scriptString . "\n\n";
			
			} catch (exception $e){
			
				/**
				 * Stack this error
				 */
				CoreLog::error($e->getMessage());
			
			}
		
		}
		
		/**
		 * Return script
		 */
		return $script;
	
	}
		
}