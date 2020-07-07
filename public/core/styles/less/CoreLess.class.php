<?php

/**
 * Less stack compiler
 * Provides logic that stacks, compiles and cached less/css
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreLess {

    /**
     * Allow reflection
     */
    use ClassReflectionTrait;

    /**
     * Allow interceptors
     */
    use CoreInterceptorTrait;

	/**
	 * Core less constants
	 */
	const LESS_PREPEND = 'styles-';
	const LESS_FOLDER = 'less';
	const LESS_IMAGES = 'images/';

	/**
	 * Constants
	 */
	const SLASH = '/';
	
    /**
     * Events
     */
    const EVENT_COMPILE_LESS_FILE_BEFORE = 'less:compile:before';

    /**
	 * Styles files
	 */
	const ALL_STYLES = 'styles.less'; //styles.less
	const IE_8_STYLES = 'styles-ie8.less'; //styles-ie8.less
	const IE_7_STYLES = 'styles-ie7.less'; //styles-ie7.less
	const LT_IE_7_STYLES = 'styles-lt-ie7.less'; //styles-lt-ie7.less
	const GLOBAL_STYLES = 'styles-global.less'; //styles-global.less

    /**
     * Cache keys
     */
	const CACHE_LESS_NS = 'less';
    const CACHE_LESS_FILE_NAME_KEY = 'lessFileName';
    const CACHE_LESS_STACK_KEY = 'lessstack';

    /**
	 * Less stack
	 */
	public static $lessStack = array();

	/**
	 * Cached
	 *
	 * @var bool
	 */
	public static $cached = false;

    /**
     * Less CSS string
     */
    public static $lessString = null;

    /**
	 * Latest modification date
	 */
	private static $latestModificationDate = 0;
	
	/**
	 * Less compiler
	 */
	private static $lessCompiler = null;

	/**
	 * Register less
	 */
	public static function registerLess(){

		self::$lessStack = CoreCache::getCache(static::CACHE_LESS_STACK_KEY, true, array(self::CACHE_LESS_NS), false);

		if(!empty(self::$lessStack)) return;

		self::$lessStack = array();

		/** @var CoreModuleObject $coreModuleObject */
		foreach(CoreModule::$modules as $coreModuleObject){

			/** @var CSSAsset $less */
			if(null != $coreModuleObject->getCoreInitAssetsReferenceObject()) {
				foreach ($coreModuleObject->getCoreInitAssetsReferenceObject()->getLess() as $less) {
					self::register($less);
				}
			}

			/** @var CoreTemplateObject $view */
			$views = $coreModuleObject->getViews();
			if(!empty($views)) {
				foreach ($views as $view) {
					/** @var CSSAsset $less */
					if (null != $view->getCoreAssetsReferenceObject()) {
						foreach ($view->getCoreAssetsReferenceObject()->getLess() as $less) {
							self::register($less);
						}
					}
				}
			}

		}

		CoreCache::saveCache(static::CACHE_LESS_STACK_KEY, static::$lessStack, 86400, true, array(self::CACHE_LESS_NS), false);

	}

	/**
	 * Load less for module
	 *
	 * @param CoreModuleObject $coreModuleObject
	 * @return array
	 */
	public static function loadModuleLess(CoreModuleObject $coreModuleObject){
		return self::loadLess($coreModuleObject->getPath(), null);
	}

	/**
	 * Load less for template
	 *
	 * @param CoreTemplateObject $coreTemplateObject
	 * @return array
	 */
	public static function loadTemplateLess(CoreTemplateObject $coreTemplateObject){
        return self::loadLess($coreTemplateObject->getBasePath(), $coreTemplateObject->getNamespace());
	}

	/**
	 * Load less file
	 *
	 * @param null $path
	 * @param null $namespace
	 * @return array
	 */
	private static function loadLess($path = null, $namespace = null){

		/**
		 * return found less
		 */
		$foundLess = array();

		/**
		 * Less base path
		 */
		$lessFolder = $path . self::SLASH . self::LESS_FOLDER;

		/**
		 * Get less files
		 */
		$lessFiles = CoreFilesystemUtils::readFiles($lessFolder);

		/**
		 * UserRegisterAction less files
		 */
		if(!empty($lessFiles)){
			foreach($lessFiles as $lessFile){

				/**
				 * Get crutch base path
				 */
				$lessFilePath = $lessFolder . self::SLASH . $lessFile;

				/**
				 * Lets make sure the crutch exists
				 */
				if(is_file($lessFilePath)){

					/**
					 * Create instance of CSS assets
					 */
					$CssAsset = new CSSAsset($lessFilePath, $namespace, CSSAsset::TYPE_LESS);

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
	 * Add less file
	 * @param CSSAsset $CSSAsset
	 * @return void
	 */
	private static function register($CSSAsset = null){

		/**
		 * Add less file to stack
		 */
		array_push(self::$lessStack, $CSSAsset);
		
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
	 *
	 * @param $lessMatch
	 * @return string
	 */
	private static function findLatestModified($lessMatch = null){

		/**
		 * Quick sanity check
		 */
		if(empty(self::$lessStack)){
			return 0;
		}

		/**
		 * Step through less files
		 */
		foreach(self::$lessStack as $CSSAsset){

            /* @var CSSAsset $CSSAsset */

			/**
			 * Update latest modified
			 */
			if(($latestModDate = filemtime($CSSAsset->getPath())) > self::getLatestModified() && stripos($CSSAsset->getPath(), $lessMatch) > -1){
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
	public static function haveLessMatch($lessMatch = null){

		/**
		 * Quick sanity check
		 */
		if(empty(self::$lessStack)){
			return false;
		}
		
		/**
		 * Empty match
		 */
		if(empty($lessMatch) && !empty(self::$lessStack)){
			return true;
		}

		/**
		 * Compile less files
		 */
		foreach(self::$lessStack as $CSSAsset){

            /* @var CSSAsset $CSSAsset */
            if(strpos($CSSAsset->getPath(), $lessMatch) !== false){
				return true;
			}

		}

		/**
		 * Did not find match
		 */
		return false;
		
	}
	
	/**
	 * Get css filename
	 * @param $lessMatch
	 * @return string
	 */
	private static function getCssFileName($lessMatch = null){

		$cacheKey = static::CACHE_LESS_FILE_NAME_KEY . ':' . $lessMatch;

		$lessFileName = CoreCache::getCache($cacheKey, true);

        //generate if needed
        if(!isset($lessFileName) || !$lessFileName || !STATIC_ASSET_CACHING_ENABLED){

            //generate less file
            $lessFileName = self::LESS_PREPEND . md5(self::LESS_FOLDER . $lessMatch . self::findLatestModified($lessMatch) . serialize(self::$lessStack)) . '.css';

            //store cached
            CoreCache::saveCache($cacheKey, $lessFileName, 0, true);

        }

        //restore less file name
        return $lessFileName;

	}
	
	/**
	 * Get css file
	 * @param string $lessMatch
	 * @return String css file
	 */
	public static function getCssFile($lessMatch = null){
		
		//generate filename
		$filename = self::getCssFileName($lessMatch);
		
		//see if file exists
		if(!CoreResources::exists($filename) || !STATIC_ASSET_CACHING_ENABLED){
			
			//compile and store the css
			if(false === CoreResources::store($filename, self::compileLess($lessMatch))){

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
	 *
	 * @param $lessMatch
	 * @return string
	 */
	public static function compileLess($lessMatch = null){
		
		/**
		 * Quick sanity check
		 */
		if(empty(self::$lessStack)){
			return false;
		}
		
		/**
		 * css string holder
		 */
		$css = null;

        /**
         * Dispatch listeners
         * prior to compiling less file
         */
        CoreObserver::dispatch(self::EVENT_COMPILE_LESS_FILE_BEFORE);

		//load less compiler if needed
		if(empty(self::$lessCompiler)){

			/**
			 * Load less compiler
			 */
			require('inc/lessc.php');
			
			/**
			 * Less compiler
			 */
			self::$lessCompiler = new lessc;
			
		}
		
		/**
		 * Compile less files
		 */
		foreach(self::$lessStack as $CSSAsset){

            /* @var CSSAsset $CSSAsset */

            //less string
            $less = null;

			/**
			 * Check for match
			 */
			if(!empty($lessMatch) && stripos($CSSAsset->getPath(), $lessMatch) == -1){
				continue;
			}

			/**
			 * Add label
			 */
			$less .= "\n\n" . '/* source:' . $CSSAsset->getPath() . ' */' . "\n";

			//read file to string
			$less .= self::getLessContentsFromFile($CSSAsset);
			
			/**
			 * Stack to combined css string
			 */
			$less .= "\n\n";
			
			//stack less string
			self::$lessString .= $less;
			
		}

		/**
		 * Attempt to compile less file
		 */
		try {
		
			/**
			 * Styles string
			 */
			$css = self::$lessCompiler->compile(self::$lessString);
			
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

    /**
     * Get less contents from file
     * Allows interception
     *
     * @param CSSAsset $CSSAsset
     * @return String LESS file
     */
    private static function _getLessContentsFromFile(CSSAsset $CSSAsset) {

        //assertion
        if(!is_file($CSSAsset->getPath())){
            CoreLog::debug('Unable to read css assets. Path: ' . $CSSAsset->getPath());
			return '';
        }

        return file_get_contents($CSSAsset->getPath());

    }
	
}