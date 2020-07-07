<?php

/**
 * Core Template
 * This class allows registration and overloading of templates and template data
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreTemplate {

    /**
     * Allow reflection
     */
    use ClassReflectionTrait;

    /**
     * Allow interceptors
     */
    use CoreInterceptorTrait;

    /**
     * Types
     */
    const TYPE_PLAIN = 'plain';

	/**
	 * Keys
	 */
	const TEMPLATE_KEY = 'template';
	const TEMPLATE_DATA = 'data';
	const TEMPLATE_FILE = 'file';
	const TEMPLATE_INDEX = 'index';
	const TEMPLATE_NAMESPACE = 'namespace';
    const TEMPLATE_CRUTCHES = 'crutches';
	const TEMPLATES_INDEX = 'index';
    const TEMPLATE_TYPE = 'type';

    /**
     * Constants
     */
    const SLASH = '/';
    
	/**
	 * Template folder
	 */
	const TEMPLATES_FOLDER = 'views';
	const TEMPLATES_TEMPLATE_FILE = 'template.php';
    const TEMPLATES_CRUTCHES_FILE = 'crutches.php';
    const TEMPLATES_INDEX_FILE = 'index.php';
    const TEMPLATES_TYPE_FILE = 'type.php';
    const TEMPLATES_SCRIPT_FILE = 'script.js';

    /**
     * Cache keys
     */
    const CACHE_TEMPLATES_NS = 'templates';
    const CACHE_TEMPLATES_KEY = 'templates';

    /**
     * Current model
     */
    public static $flashModel = array();

    /**
     * Cached
     *
     * @var bool
     */
    public static $cached = false;

    /**
	 * Core templates
	 */
	public static $coreTemplates = array();
	
	/**
	 * Mustage_Engine
	 */
	private static $Mustache = null;

    /**
     * Core Template Data holder
     * holds data for consumption for a template
     *
     * @var array
     */
    public static $coreTemplateData = array();

	/**
	 * Getter for core templates
	 * @return array templates
	 */
	public static function getCoreTemplates(){
		return self::$coreTemplates;
	}
	
	/**
	 * Get template by namespace
     * Allow interception
     *
	 * @param String $namespace Template namespace
	 * @return CoreTemplateObject
	 */
	public static function _getByNamespace($namespace = null){

		/**
		 * No templates
		 */
		if(empty(self::$coreTemplates)){
			return false;
		}

		/**
		 * See if we already have one
		 * with identical or higher zindex
         * @var CoreTemplateObject $CoreTemplateObject
		 */
		foreach(self::$coreTemplates as &$CoreTemplateObject){
			if($CoreTemplateObject->getNamespace() == $namespace){
				return $CoreTemplateObject;
			}
		}
		
		/**
		 * Template not found
		 */
		return false;
		
	}

	/**
	 * Render template
	 * @return Boolean loaded or not
	 */	
	public static function loadMustache(){
		
		//see if $Mustache is already loaded
		if(self::$Mustache instanceof Mustache_Engine){
			return true;
		}
		
		//load Mustache
		try {
			
			//include Mustache autoloader
			require("inc/Mustache/Autoloader.php");
			
			//register autoloader
			Mustache_Autoloader::register();
			
			//load Mustage_Engine
			self::$Mustache = new Mustache_Engine();
			
			//mustache loaded ok
			return true;
			
		} catch (Exception $e){
			
			//handle exception
			CoreLog::error("Unable to load Mustache Template Engine. Info " . $e->getMessage());
			
		}
		
		//something went wrong
		return false;

	}

	/**
	 * Render template
	 * @param String $namespace Template namespace
	 * @return String Html string or false
	 */	
	public static function _getView($namespace = null){

        /** @var CoreTemplateObject $CoreTemplateObject */
		if(false !== ($CoreTemplateObject = self::getByNamespace($namespace))){

            /**
             * Apply template data
             */
            if(is_file($CoreTemplateObject->getTemplatePath())){

                $view = null;
                $script = null;
                $head = null;

                /** @var array $requires */
                $requires = array();

                /** @var array $data */
                $data = self::getData($CoreTemplateObject, self::$flashModel);

                //handle template parents
                $parents = self::resolveParents($CoreTemplateObject, array());
                if(!empty($parents)){
                    foreach($parents as $parent){

                        //load the parents in order
                        require $parent->getTemplatePath();

                    }
                }

                //stack request scoped javascript
                $embeddedScript = $CoreTemplateObject->getScript();
                if(!empty($embeddedScript)){
                    CoreScript::appendRequestScopedScript($embeddedScript, $CoreTemplateObject->getNamespace(), true);
                }

                //load the template
                require $CoreTemplateObject->getTemplatePath();

                if(!empty($script)) {
                    CoreScript::appendRequestScopedScript(self::$Mustache->render($script, $data), $CoreTemplateObject->getNamespace());
                }
                if(!empty($head)) {
                    CoreRender::appendRequestScopedHead(self::$Mustache->render($head, $data), $CoreTemplateObject->getNamespace());
                }

                //handle template object
                self::handleTemplateObject($CoreTemplateObject, $requires, $data);

				//return view
				return $view;
			
			}	
					
		}

        //lets report this incident
        CoreLog::error("Unable to find valid template with namespace: " . $namespace);

        //return false
        return false;

	}

    /**
     * Resolve parents
     *
     * @param CoreTemplateObject $CoreTemplateObject
     * @param array $reference
     * @return array
     */
    private static function resolveParents(CoreTemplateObject $CoreTemplateObject, $reference = array()){

        /** @var CoreTemplateObject $ParentCoreTemplateObject */
        $ParentCoreTemplateObject = $CoreTemplateObject->getParent();

        /**
         * Keep looking as long as we find parents
         */
        if(!empty($ParentCoreTemplateObject)){
            if(is_file($ParentCoreTemplateObject->getTemplatePath())){

                $crutches = $ParentCoreTemplateObject->getCrutches();
                if(!empty($crutches) && !CoreInit::$reflection){
                    foreach($crutches as $crutch){
                        CoreCrutches::useCrutch($crutch);
                    }
                }

                array_push($reference, $ParentCoreTemplateObject);

                $reference = self::resolveParents($ParentCoreTemplateObject, $reference);

            }
        }

        /** oldest first */
        $reference = array_reverse($reference);

        return $reference;

    }

    /**
     * Handle template object
     *
     * @param CoreTemplateObject $CoreTemplateObject
     * @param null $requires
     * @param array $data
     */
    public static function _handleTemplateObject(CoreTemplateObject $CoreTemplateObject, $requires = null, $data = array()){

        /**
         * Check required data
         */
        if(!empty($requires)){
            foreach($requires as &$value){
                if(!isset($data[$value]))
                    CoreLog::error('Template data by key: ' . $value . ' is required in template: ' . $CoreTemplateObject->getNamespace());
            }
        }

        //Use these crutches
        $crutches = $CoreTemplateObject->getCrutches();
        if(!empty($crutches) && !CoreInit::$reflection){
            foreach($crutches as &$crutch){
                CoreCrutches::useCrutch($crutch);
            }
        }

    }

	/**
	 * Render template
     *
	 * @param String $namespace Template namespace
	 * @param Mixed $data Template data
	 * @return String Html string or false
	 */
	public static function _render($namespace = null, $data = null){

        /** @var CoreTemplateObject $CoreTemplateObject */
		if(false !== ($CoreTemplateObject = self::getByNamespace($namespace))){

			/**
			 * Apply template data
			 */
			if(is_file($CoreTemplateObject->getTemplatePath())){

                /**
                 * Expected template components
                 */
                $view = null;
                $script = null;
                $head = null;
                $requires = array();

                /**
                 * Some templates reference data
                 */
                self::$flashModel = $data = self::getData($CoreTemplateObject, $data);

                //handle template parents
                $parents = self::resolveParents($CoreTemplateObject, array());
                if(!empty($parents)){
                    /** @var CoreTemplateObject $parent */
                    foreach($parents as &$parent){

                        //load the parents in order
                        require $parent->getTemplatePath();

                    }
                }

                //handle template object
                self::handleTemplateObject($CoreTemplateObject, $requires, $data);

                /**
                 * No rendering needed if TYPE_PLAIN
                 */
                $types = $CoreTemplateObject->getType();
                if(is_array($types) && in_array(self::TYPE_PLAIN, $types)){
                    $embeddedScript = $CoreTemplateObject->getScript();
                    if(!empty($embeddedScript)){
                        CoreScript::appendRequestScopedScript($embeddedScript, $CoreTemplateObject->getNamespace(), true);
                    }

                    //load the template
                    require $CoreTemplateObject->getTemplatePath();

                    if(!empty($script)) {
                        CoreScript::appendRequestScopedScript($script, $CoreTemplateObject->getNamespace());
                    }
                    if(!empty($head)) {
                        CoreRender::appendRequestScopedHead($head, $CoreTemplateObject->getNamespace());
                    }

                    return $view;
                }

                /**
                 * Render and append script contents if needed
                 */
                $embeddedScript = $CoreTemplateObject->getScript();
                if(!empty($embeddedScript)){
                    $embeddedScript = CoreScript::minifyJavascriptString($embeddedScript);
                    CoreScript::appendRequestScopedScript($embeddedScript, $CoreTemplateObject->getNamespace(), true);
                }

                //load the template
                require $CoreTemplateObject->getTemplatePath();

                if(!empty($script)) {
                    CoreScript::appendRequestScopedScript(self::$Mustache->render($script, $data), $CoreTemplateObject->getNamespace());
                }
                if(!empty($head)) {
                    CoreRender::appendRequestScopedHead(self::$Mustache->render($head, $data), $CoreTemplateObject->getNamespace());
                }

                /**
                 * Render template and return
                 */
                return self::$Mustache->render($view, $data);
			
			}	
			
		}

		//lets report this incident
		CoreLog::debug("Unable to find valid template with namespace: " . $namespace);

		//return false
		return false;
		
	}

    /**
     * Load templates for module
     *
     * @param CoreModuleObject $coreModuleObject
     * @return array|void
     */
	public static function loadTemplates(CoreModuleObject $coreModuleObject){

        /**
         * The templates to return
         */
        $theTemplates = array();

        /**
		 * Get template folders
		 */
		$templates = CoreFilesystemUtils::readFolders($coreModuleObject->getPath() . self::SLASH . self::TEMPLATES_FOLDER);

        /**
		 * Quick sanity check
		 */
		if(empty($templates)){
			return false;
		}
			
		/**
		 * Template
		 */
		foreach($templates as $template){
			
			//template file
            $templateBasePath = $coreModuleObject->getPath() . self::SLASH . self::TEMPLATES_FOLDER . self::SLASH . $template;
			$templateFile = $templateBasePath . self::SLASH . self::TEMPLATES_TEMPLATE_FILE;
			$crutchesFile = $templateBasePath . self::SLASH . self::TEMPLATES_CRUTCHES_FILE;
            $indexFile = $templateBasePath . self::SLASH . self::TEMPLATES_INDEX_FILE;
            $typeFile = $templateBasePath . self::SLASH . self::TEMPLATES_TYPE_FILE;
            $scriptFile = $templateBasePath . self::SLASH . self::TEMPLATES_SCRIPT_FILE;

			//make sure template exists
			if(!is_file($templateFile)){
				CoreLog::error("Unable to find template at " . $templateFile);
				continue;
			}

            //Define core template object
            $coreTemplateObject = new CoreTemplateObject();
            $coreTemplateObject->setNamespace($template);
            $coreTemplateObject->setBasePath($templateBasePath);
            $coreTemplateObject->setTemplatePath($templateFile);

			//load the crutches
			if(is_file($crutchesFile)){

                //load crutches file
                require($crutchesFile);

                //check for crutches
                if(isset($crutches) && !empty($crutches)){

                    //set crutches path
                    $coreTemplateObject->setCrutches($crutches);
                    $coreTemplateObject->setCrutchesPath($crutchesFile);

                }

            }

            //load the index
            if(is_file($indexFile)){

                //load index file
                require($indexFile);

                //check for index
                if(isset($zindex) && is_numeric($zindex)){

                    $coreTemplateObject->setIndex($zindex);
                    $coreTemplateObject->setIndexPath($indexFile);

                }

            }

            //load the type
            if(is_file($typeFile)){

                //load index file
                require($typeFile);

                //check for index
                if(isset($type) && !empty($type)){

                    $coreTemplateObject->setType($type);
                    $coreTemplateObject->setTypePath($typeFile);

                }

            }

            //capture script.js
            if(is_file($scriptFile)){
                $coreTemplateObject->setScript(file_get_contents($scriptFile));
            }

            /**
             * Loads assets and set as reference for this template
             */
            $coreTemplateObject->setCoreAssetsReferenceObject(CoreInit::loadTemplateAssets($coreTemplateObject));

            /**
             * Stack in return
             */
            array_push($theTemplates, $coreTemplateObject);

		}

        /**
         * Return for reference
         */
        return $theTemplates;

	}

    /**
     * Register views for modules
     *
     */
    public static function registerViews(){

        self::$coreTemplates = CoreCache::getCache(static::CACHE_TEMPLATES_KEY, true, array(self::CACHE_TEMPLATES_NS), false);

        if(!empty(self::$coreTemplates)) return;

        self::$coreTemplates = array();

        /** @var CoreModuleObject $coreModuleObject */
        foreach(CoreModule::$modules as $coreModuleObject){
            $views = $coreModuleObject->getViews();
            /** @var CoreTemplateObject $coreTemplateObject */
            if(!empty($views)){
                foreach($views as $coreTemplateObject) {
                    self::registerView($coreTemplateObject);
                }
            }
        }

        CoreCache::saveCache(static::CACHE_TEMPLATES_KEY, static::$coreTemplates, 86400, true, array(self::CACHE_TEMPLATES_NS), false);

    }

    /**
     * Register view
     *
     * @param CoreTemplateObject|null $CoreTemplateObject
     * @return bool
     */
	public static function registerView(CoreTemplateObject $CoreTemplateObject = null){
		
		/** Overload template if z-index */
        if(isset(self::$coreTemplates[$CoreTemplateObject->getNamespace()])){
            if($CoreTemplateObject->getIndex() >= self::$coreTemplates[$CoreTemplateObject->getNamespace()]->getIndex()){

                CoreLog::debug('Overloading template ' . $CoreTemplateObject->getNamespace());

                /**
                 * Set parent reference
                 */
                $CoreTemplateObject->setParent(self::$coreTemplates[$CoreTemplateObject->getNamespace()]);

                /**
                 * Load template reference
                 */
                self::$coreTemplates[$CoreTemplateObject->getNamespace()] = $CoreTemplateObject;

            }else{
                CoreLog::debug('Not overloading template ' . $CoreTemplateObject->getNamespace());
            }
        }else{
            self::$coreTemplates[$CoreTemplateObject->getNamespace()] = $CoreTemplateObject;
        }

        //return true
        return true;

	}

    /**
     * Set data for template
     *
     * @param null $namespace
     * @param null $key
     * @param null $value
     * @return bool
     * @throws Exception
     */
    public static function setData($namespace = null, $key = null, $value = null){

        /** @var CoreTemplateObject $CoreTemplateObject */
        $CoreTemplateObject = CoreTemplate::getByNamespace($namespace);

        // assure template is registered
        if(!$CoreTemplateObject) throw new Exception('Unable to find registered template by namespace: ' . $namespace);

        // other assertions
        if(!$key) throw new Exception('Need key when setting data for template');

        /**
         * Make data available
         */
        self::$coreTemplateData[$CoreTemplateObject->getNamespace()][$key] = $value;

        return true;

    }

    /**
     * Get data registered for a certain template
     *
     * @param CoreTemplateObject $CoreTemplateObject
     * @param array $data
     * @return array
     */
    private static function getData(CoreTemplateObject $CoreTemplateObject, $data = array()){

        /**
         * Return original data when
         * no data is set for this template
         */
        if(!isset(self::$coreTemplateData[$CoreTemplateObject->getNamespace()])) return $data;

        /**
         * If we have data for this template
         * lets merge it into the available model
         */
        if(!empty(self::$coreTemplateData[$CoreTemplateObject->getNamespace()])){
            foreach(self::$coreTemplateData[$CoreTemplateObject->getNamespace()] as $key => $value){

                /**
                 * Show debug message when overloading data in original model
                 */
                if(isset($data[$key])) CoreLog::debug('Overloading data by key: ' . $key);

                /**
                 * Set data
                 */
                $data[$key] = $value;

            }
        }

        return $data;

    }
	
}