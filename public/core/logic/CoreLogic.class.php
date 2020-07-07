<?php

/**
 * Core Logic Component
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreLogic {

    /**
     * Allow reflection
     */
    use ClassReflectionTrait;

    /**
     * Allow interceptors
     */
    use CoreInterceptorTrait;

	/**
	 * Logical structure
     * Folder names
	 */
	const LOGIC_SERVICES_FOLDER = "services";
	const LOGIC_PROCEDURES_FOLDER = "procedures";
	const LOGIC_REPOSITORIES_FOLDER = "repositories";
	const LOGIC_OBJECTS_FOLDER = "objects";
    const LOGIC_EXCEPTIONS_FOLDER = "exceptions";
    const LOGIC_ACTIONS_FOLDER = "actions";

    /**
     * Cache
     */
    const CACHE_LOGIC_NS = 'logic';
    const CACHE_ACTIONS_KEY = 'logic.actions';
    const CACHE_INTERFACES_KEY = 'logic.interfaces';
    const CACHE_SERVICES_KEY = 'logic.services';
    const CACHE_PROCEDURES_KEY = 'logic.procedures';
    const CACHE_REPOSITORIES_KEY = 'logic.repositories';
    const CACHE_OBJECTS_KEY = 'logic.objects';
    const CACHE_EXCEPTIONS_KEY = 'logic.exceptions';

    /**
     * File append
     */
    const LOGIC_SERVICE_APPEND = "Service";
	const LOGIC_PROCEDURE_APPEND = "Procedure";
	const LOGIC_REPOSITORY_APPEND = "Repository";
	const LOGIC_OBJECT_APPEND = "Object";
    const LOGIC_EXCEPTION_APPEND = "Exception";
    const LOGIC_ACTION_APPEND = "Action";
    const LOGIC_INTERFACE_APPEND = "Interface";
	const LOGIC_CLASS_APPEND = ".class.php";
	const LOGIC_CLASS_INTERFACE_APPEND = ".interface.php";

    /**
     * Keys
     */
    const PATH_KEY = 'path';
    const CLASS_KEY = 'class';
    const SLASH = '/';
    const EMPTY_STRING = '';

	/**
	 * Reference
	 */
	public static $interfaces = array();
	public static $services = array();
	public static $procedures = array();
	public static $repositories = array();
	public static $objects = array();
	public static $exceptions = array();
    public static $actions = array();

    /**
     * Register logic class loader
     */
    public static function registerClassLoader(){
        spl_autoload_register(array(__CLASS__, 'classLoader'));
    }

    /**
     * Class loader -- allows for referencing non instantiated classes
     *
     * @param null $name
     */
    public static function classLoader($name = null){
        switch(true){
            case (substr($name, -strlen(self::LOGIC_SERVICE_APPEND)) == self::LOGIC_SERVICE_APPEND):
                self::getService($name, false);
                break;
            case (substr($name, -strlen(self::LOGIC_PROCEDURE_APPEND)) == self::LOGIC_PROCEDURE_APPEND):
                self::getProcedure($name, false);
                break;
            case (substr($name, -strlen(self::LOGIC_REPOSITORY_APPEND)) == self::LOGIC_REPOSITORY_APPEND):
                self::getRepository($name, false);
                break;
            case (substr($name, -strlen(self::LOGIC_OBJECT_APPEND)) == self::LOGIC_OBJECT_APPEND):
                self::getObject($name, false);
                break;
            case (substr($name, -strlen(self::LOGIC_ACTION_APPEND)) == self::LOGIC_ACTION_APPEND):
                self::getAction($name, false);
                break;
            case (substr($name, -strlen(self::LOGIC_EXCEPTION_APPEND)) == self::LOGIC_EXCEPTION_APPEND):
                self::getException($name);
                break;
        }
    }

    /**
     * Get exception
     *
     * @param null $name
     * @param bool $skip_error Suppress error when not found allows other class loaders to continue processing
     * @return bool|void
     */
    private static function getException($name = null, $skip_error = true){

        if(isset(CoreLogic::$exceptions[$name])){

            /** @var CoreLogicObject $exception */
            $exception = CoreLogic::$exceptions[$name];

            /** prevent double loading */
            if(null != $exception->getLogic()) return;
            $exception->setLogic(true);

            /** load object */
            require $exception->getPath() . self::SLASH . $exception->getClass();

            return;

        }

        if(!$skip_error) {
            CoreLog::error('Unable to locate exception [' . $name . ']');
            return false;
        }

    }

    /**
     * Get a service
     *
     * @param null $name
     * @param bool $provide
     * @return bool|mixed|void
     */
	public static function getService($name = null, $provide = true){

        if(isset(CoreLogic::$services[$name])){

            /** @var CoreLogicObject $service */
            $service = CoreLogic::$services[$name];

            /** @var string $Name */
            $Name = $service->getName();

            /** return existing */
            if(is_object($service->getLogic())){
                return $service->getLogic();
            }

            /** load interface */
            if(isset(self::$interfaces[$Name])){
                /** @var CoreLogicObject $Interface */
                $Interface = self::$interfaces[$Name];
                if(!interface_exists($Interface->getName() . self::LOGIC_INTERFACE_APPEND)) {
                    require $Interface->getPath() . self::SLASH . $Interface->getClass();
                }
            }

            /** load object */
            if(!class_exists($service->getName())) {
                require $service->getPath() . self::SLASH . $service->getClass();
            }

            /** set reference */
            $service->setLogic(new $Name());

            /** just load */
            if(!$provide) return;

            /** return object */
            return $service->getLogic();

        }

        CoreLog::error('Unable to locate service [' . $name . ']');

        return false;

	}

    /**
     * Get a procedure
     *
     * @param null $name
     * @param bool $provide
     * @return bool|mixed|void
     */
	public static function getProcedure($name = null, $provide = true){

        if(isset(CoreLogic::$procedures[$name])){

            /** @var CoreLogicObject $procedure */
            $procedure = CoreLogic::$procedures[$name];

            /** @var string $Name */
            $Name = $procedure->getName();

            /** return existing */
            if(is_object($procedure->getLogic())){
                return $procedure->getLogic();
            }

            /** load object */
            if(!class_exists($procedure->getName())) {
                require $procedure->getPath() . self::SLASH . $procedure->getClass();
            }

            /** set reference */
            $procedure->setLogic(new $Name());

            /** just load */
            if(!$provide) return;

            /** return object */
            return $procedure->getLogic();

        }

        CoreLog::error('Unable to locate procedure [' . $name . ']');
        return false;

	}

    /**
     * Get a repository
     *
     * @param null $name
     * @param bool $provide
     * @return bool|mixed|void
     */
	public static function getRepository($name = null, $provide = true){

        if(isset(CoreLogic::$repositories[$name])){

            /** @var CoreLogicObject $repository */
            $repository = CoreLogic::$repositories[$name];

            /** @var string $Name */
            $Name = $repository->getName();

            /** return existing */
            if(is_object($repository->getLogic())){
                return $repository->getLogic();
            }

            /** load object */
            if(!class_exists($repository->getName())) {
                require $repository->getPath() . self::SLASH . $repository->getClass();
            }

            /** set reference */
            $repository->setLogic(new $Name());

            /** just load */
            if(!$provide) return;

            /** return object */
            return $repository->getLogic();

        }

        CoreLog::error('Unable to locate repository [' . $name . ']');
        return false;

	}

    /**
     * Get an object
     *
     * @param null $name
     * @param bool $provide
     * @return bool|mixed|void
     */
	public static function getObject($name = null, $provide = true){

		if(isset(CoreLogic::$objects[$name])){

            /** @var CoreLogicObject $object */
            $object = CoreLogic::$objects[$name];

            /** return existing */
            if(is_object($object->getLogic())) return clone $object->getLogic();

            /** load object */
            if(!class_exists($object->getName())) {
                require $object->getPath() . self::SLASH . $object->getClass();
            }

            /** @var string $Name */
            $Name = $object->getName();

            /** set reference */
            $object->setLogic(new $Name());

            /** just load */
            if(!$provide) return;

            /** return object */
            return clone $object->getLogic();

        }

		CoreLog::error('Unable to locate object [' . $name . ']');
		return false;

	}

    /**
     * Get Action
     *
     * @param null $name
     * @param bool $provide
     * @return object or false
     */
    public static function getAction($name = null, $provide = true){

        //return the object
        if(isset(CoreLogic::$actions[$name])){

            /**
             * @var CoreLogicObject $Action
             */
            $Action = CoreLogic::$actions[$name];

            /** return existing */
            if(is_object($Action->getLogic())) return  $Action->getLogic();

            /** load object */
            if(!class_exists($Action->getName())) {
                require $Action->getPath() . self::SLASH . $Action->getClass();
            }

            /** @var string $Name */
            $Name = $Action->getName();

            /** set reference */
            $Action->setLogic(new $Name());

            /** just load */
            if(!$provide) return;

            //return the action class
            return $Action->getLogic();

        }

        //handle error
        CoreLog::error('Unable to locate action [' . $name . ']');
        return false;

    }

    public static function buildReferences(){

        /**
         * Get cached references
         */
        CoreLogic::$actions = CoreCache::getCache(self::CACHE_ACTIONS_KEY, true, array(self::CACHE_LOGIC_NS), false);
        CoreLogic::$interfaces = CoreCache::getCache(self::CACHE_INTERFACES_KEY, true, array(self::CACHE_LOGIC_NS), false);
        CoreLogic::$services = CoreCache::getCache(self::CACHE_SERVICES_KEY, true, array(self::CACHE_LOGIC_NS), false);
        CoreLogic::$procedures = CoreCache::getCache(self::CACHE_PROCEDURES_KEY, true, array(self::CACHE_LOGIC_NS), false);
        CoreLogic::$repositories = CoreCache::getCache(self::CACHE_REPOSITORIES_KEY, true, array(self::CACHE_LOGIC_NS), false);
        CoreLogic::$objects = CoreCache::getCache(self::CACHE_OBJECTS_KEY, true, array(self::CACHE_LOGIC_NS), false);
        CoreLogic::$exceptions = CoreCache::getCache(self::CACHE_EXCEPTIONS_KEY, true, array(self::CACHE_LOGIC_NS), false);

        /**
         * Done?
         */
        if(
            !empty(CoreLogic::$actions) &&
            !empty(CoreLogic::$interfaces) &&
            !empty(CoreLogic::$services) &&
            !empty(CoreLogic::$procedures) &&
            !empty(CoreLogic::$repositories) &&
            !empty(CoreLogic::$objects) &&
            !empty(CoreLogic::$exceptions)
        ) {
            return;
        }

        if(empty(CoreModule::$modules)) CoreLog::error('No modules!');

        CoreLogic::$actions = array();
        CoreLogic::$interfaces = array();
        CoreLogic::$services = array();
        CoreLogic::$procedures = array();
        CoreLogic::$repositories = array();
        CoreLogic::$objects = array();
        CoreLogic::$exceptions = array();

        /** @var CoreModuleObject $coreModuleObject */
        foreach(CoreModule::$modules as $coreModuleObject){

            $actions = $coreModuleObject->getActions();
            /** @var CoreLogicObject $action */
            if(!empty($actions)) foreach($actions as $action){ self::$actions[$action->getName()] = $action; }

            $interfaces = $coreModuleObject->getInterfaces();
            /** @var CoreLogicObject $interface */
            if(!empty($interfaces)) foreach($interfaces as $interface){ self::$interfaces[$interface->getName()] = $interface; }

            $services = $coreModuleObject->getServices();
            /** @var CoreLogicObject $service */
            if(!empty($services)) foreach($services as $service){ self::$services[$service->getName()] = $service; }

            $procedures = $coreModuleObject->getProcedures();
            /** @var CoreLogicObject $procedure */
            if(!empty($procedures)) foreach($procedures as $procedure){ self::$procedures[$procedure->getName()] = $procedure; }

            $repositories = $coreModuleObject->getRepositories();
            /** @var CoreLogicObject $repository */
            if(!empty($repositories)) foreach($repositories as $repository){ self::$repositories[$repository->getName()] = $repository; }

            $objects = $coreModuleObject->getObjects();
            /** @var CoreLogicObject $object */
            if(!empty($objects)) foreach($objects as $object){ self::$objects[$object->getName()] = $object; }

            $exceptions = $coreModuleObject->getExceptions();
            /** @var CoreLogicObject $exception */
            if(!empty($exceptions)) foreach($exceptions as $exception){ self::$exceptions[$exception->getName()] = $exception; }

        }

        /** Store references */
        CoreCache::saveCache(self::CACHE_ACTIONS_KEY, static::$actions, 86400, true, array(self::CACHE_LOGIC_NS), false);
        CoreCache::saveCache(self::CACHE_INTERFACES_KEY, static::$interfaces, 86400, true, array(self::CACHE_LOGIC_NS), false);
        CoreCache::saveCache(self::CACHE_SERVICES_KEY, static::$services, 86400, true, array(self::CACHE_LOGIC_NS), false);
        CoreCache::saveCache(self::CACHE_PROCEDURES_KEY, static::$procedures, 86400, true, array(self::CACHE_LOGIC_NS), false);
        CoreCache::saveCache(self::CACHE_REPOSITORIES_KEY, static::$repositories, 86400, true, array(self::CACHE_LOGIC_NS), false);
        CoreCache::saveCache(self::CACHE_OBJECTS_KEY, static::$objects, 86400, true, array(self::CACHE_LOGIC_NS), false);
        CoreCache::saveCache(self::CACHE_EXCEPTIONS_KEY, static::$exceptions, 86400, true, array(self::CACHE_LOGIC_NS), false);

    }

	/**
	 * Load all logical elements
	 */
	public static function loadAll(CoreModuleObject $moduleObject){

		//load managers
		self::loadServices($moduleObject);

        //load objects
		self::loadObjects($moduleObject);

        //load objects
        self::loadActions($moduleObject);

        //load exceptions
        self::loadExceptions($moduleObject);

	}

    /**
     * Load Services
     *
     * @param CoreModuleObject $moduleObject
     * @return array|bool
     */
    public static function loadServices(CoreModuleObject $moduleObject){

        $path = $moduleObject->getPath();

		$services = CoreFilesystemUtils::readFiles($path . self::SLASH . self::LOGIC_SERVICES_FOLDER);

		if(empty($services)) return false;

        $response = array();

		foreach($services as $service){

			try {

				//check for service
				if(false === strpos($service, self::LOGIC_SERVICE_APPEND . self::LOGIC_CLASS_APPEND)){
					continue; //not a service
				}

                //assure correct prepend is used
                if($moduleObject->getName() != substr($service, 0, strlen($moduleObject->getName()))){
                    CoreLog::error('Service: ' . $service . ' must start with module name: ' . $moduleObject->getName() . ' to prevent namespace conflicts');
                }

				//get service class
				$serviceClass = str_replace(self::LOGIC_CLASS_APPEND, null, $service);

                //create logic object instance
                $coreLogicObject = new CoreLogicObject();
                $coreLogicObject->setName($serviceClass);
                $coreLogicObject->setPath($path . self::SLASH . self::LOGIC_SERVICES_FOLDER);
                $coreLogicObject->setClass($service);
                $coreLogicObject->setLogic(null);

                $response[$serviceClass] = $coreLogicObject;

			} catch (Exception $e){
				CoreLog::error('Unable to load manager [' . $service . ']. Info: ' . $e->getMessage());
			}

		}

		return $response;

	}

    /**
     * Load interfaces
     *
     * @param CoreModuleObject $moduleObject
     * @return array|bool
     */
    public static function loadInterfaces(CoreModuleObject $moduleObject){

        //path pointer
        $path = $moduleObject->getPath();

        //get services
        $services = CoreFilesystemUtils::readFiles($path . self::SLASH . self::LOGIC_SERVICES_FOLDER);

        //check managers
        if(empty($services)){
            return false;
        }

        //services
        $response = array();

        //load services
        foreach($services as $service){

            try {

                //skip if not an interface
                if(substr($service, -strlen(self::LOGIC_CLASS_INTERFACE_APPEND)) != self::LOGIC_CLASS_INTERFACE_APPEND) continue;

                //get manager class
                $Interface = str_replace(self::LOGIC_CLASS_INTERFACE_APPEND, null, $service);

                //create logic object instance
                $CoreLogicObject = new CoreLogicObject();
                $CoreLogicObject->setName($Interface);
                $CoreLogicObject->setPath($path . self::SLASH . self::LOGIC_SERVICES_FOLDER);
                $CoreLogicObject->setClass($service);
                $CoreLogicObject->setLogic(null);

                array_push($response, $CoreLogicObject);

            } catch(Exception $e){
                CoreLog::error($e->getMessage());
            }

        }

		return $response;

	}

    /**
     * Load procedures
     *
     * @param CoreModuleObject $moduleObject
     * @return array|bool
     */
    public static function loadProcedures(CoreModuleObject $moduleObject){

        //path pointer
        $path = $moduleObject->getPath();

        if(!file_exists($path)) return false;

        //get procedures
        $procedures = CoreFilesystemUtils::readFiles($path . self::SLASH . self::LOGIC_PROCEDURES_FOLDER);

        //check managers
        if(empty($procedures)){
            return false;
        }

        //services
        $response = array();

        //load procedures
        foreach($procedures as $procedure){

            try {

                //get manager class
                $ProcedureClass = str_replace(self::LOGIC_CLASS_APPEND, null, $procedure);

                //create logic object instance
                $CoreLogicObject = new CoreLogicObject();
                $CoreLogicObject->setName($ProcedureClass);
                $CoreLogicObject->setPath($path . self::SLASH . self::LOGIC_PROCEDURES_FOLDER);
                $CoreLogicObject->setClass($procedure);
                $CoreLogicObject->setLogic(null);

                array_push($response, $CoreLogicObject);

            } catch(Exception $e){
                CoreLog::error($e->getMessage());
            }

        }

		return $response;

	}

    /**
     * Load repositories
     *
     * @param CoreModuleObject $moduleObject
     * @return array|bool
     */
    public static function loadRepositories(CoreModuleObject $moduleObject){

        //path pointer
        $path = $moduleObject->getPath();

        if(!file_exists($path)) return false;

        //get procedures
        $repositories = CoreFilesystemUtils::readFiles($path . self::SLASH . self::LOGIC_REPOSITORIES_FOLDER);

        //check managers
        if(empty($repositories)){
            return false;
        }

        //services
        $response = array();

        //load repositories
        foreach($repositories as $repository) {

            try {

                //get manager class
                $RepositoryClass = str_replace(self::LOGIC_CLASS_APPEND, null, $repository);

                //create logic object instance
                $CoreLogicObject = new CoreLogicObject();
                $CoreLogicObject->setName($RepositoryClass);
                $CoreLogicObject->setPath($path . self::SLASH . self::LOGIC_REPOSITORIES_FOLDER);
                $CoreLogicObject->setClass($repository);
                $CoreLogicObject->setLogic(null);

                array_push($response, $CoreLogicObject);

            } catch (Exception $e) {
                CoreLog::error($e->getMessage());
            }

        }

		return $response;

	}

    /**
     * Load objects
     *
     * @param CoreModuleObject $moduleObject
     * @return array|bool
     */
    public static function loadObjects(CoreModuleObject $moduleObject){

        $path = $moduleObject->getPath();

		//get objects
		$objects = CoreFilesystemUtils::readFiles($path . self::SLASH . self::LOGIC_OBJECTS_FOLDER);

		//check
		if(empty($objects)){
			return false;
		}

        $response = array();

		//load objects
		foreach($objects as $objectFile){

			//attempt to load this object
			try {

				//check for object
				if(false === strpos($objectFile, self::LOGIC_OBJECT_APPEND . self::LOGIC_CLASS_APPEND)){
                    continue; //not an object
				}

                //assure correct prepend is used
                if($moduleObject->getName() != substr($objectFile, 0, strlen($moduleObject->getName()))){
                    CoreLog::error('Object: ' . $objectFile . ' must start with module name: ' . $moduleObject->getName() . ' to prevent namespace conflicts');
                }

				//get object name
				$Object = str_replace(self::LOGIC_CLASS_APPEND, null, $objectFile);

                //create logic object instance
                $CoreLogicObject = new CoreLogicObject();
                $CoreLogicObject->setName($Object);
                $CoreLogicObject->setPath($path . self::SLASH . self::LOGIC_OBJECTS_FOLDER);
                $CoreLogicObject->setClass($objectFile);
                $CoreLogicObject->setLogic(null);

                array_push($response, $CoreLogicObject);

			} catch (Exception $e){
				CoreLog::error('Unable to load object. Info: ' . $e->getMessage());
			}

		}

        return $response;

	}

    /**
     * Load actions
     *
     * @param CoreModuleObject $moduleObject
     * @return array|bool
     */
    public static function loadActions(CoreModuleObject $moduleObject){

        $path = $moduleObject->getPath();

        //get actions
        $actions = CoreFilesystemUtils::readFiles($path . self::SLASH . self::LOGIC_ACTIONS_FOLDER);

        //check managers
        if(empty($actions)){
            return false;
        }

        $response = array();

        foreach($actions as $file){

            //attempt to load this object
            try {

                //check for action
                if(false === strpos($file, self::LOGIC_ACTION_APPEND . self::LOGIC_CLASS_APPEND)){
                    continue; //not an action
                }

                //assure correct prepend is used
                if($moduleObject->getName() != substr($file, 0, strlen($moduleObject->getName()))){
                    CoreLog::error('Action: ' . $file . ' must start with module name: ' . $moduleObject->getName() . ' to prevent namespace conflicts');
                }

                //get action class
                $Action = str_replace(self::LOGIC_CLASS_APPEND, null, $file);

                //create logic object instance
                $CoreLogicObject = new CoreLogicObject();
                $CoreLogicObject->setName($Action);
                $CoreLogicObject->setPath($path . self::SLASH . self::LOGIC_ACTIONS_FOLDER);
                $CoreLogicObject->setClass($file);
                $CoreLogicObject->setLogic(null);

                array_push($response, $CoreLogicObject);

            } catch (Exception $e){
                CoreLog::error('Unable to load object. Info: ' . $e->getMessage());
            }

        }

        return $response;

    }

    /**
     * Load exceptions
     *
     * @param CoreModuleObject $moduleObject
     * @return array|bool
     */
    public static function loadExceptions(CoreModuleObject $moduleObject){

        //get path
        $path = $moduleObject->getPath();

        //get objects
        $exceptions = CoreFilesystemUtils::readFiles($path . self::SLASH . self::LOGIC_EXCEPTIONS_FOLDER);

        //check managers
        if(empty($exceptions)){
            return false;
        }

        $response = array();

        foreach($exceptions as $file){

            //attempt to load this object
            try {

                //check for object
                if(false === strpos($file, self::LOGIC_EXCEPTION_APPEND . self::LOGIC_CLASS_APPEND)){
                    continue; //not an object
                }

                //assure correct prepend is used
                if($moduleObject->getName() != substr($file, 0, strlen($moduleObject->getName()))){
                    CoreLog::error('Exception: ' . $file . ' must start with module name: ' . $moduleObject->getName() . ' to prevent namespace conflicts');
                }

                //get manager class
                $Exception = str_replace(self::LOGIC_CLASS_APPEND, null, $file);

                //create logic object instance
                $coreLogicObject = new CoreLogicObject();
                $coreLogicObject->setName($Exception);
                $coreLogicObject->setPath($path . self::SLASH . self::LOGIC_EXCEPTIONS_FOLDER);
                $coreLogicObject->setClass($file);
                $coreLogicObject->setLogic(null);

                array_push($response, $coreLogicObject);

            } catch (Exception $e) {
                CoreLog::error('Unable to load exception. Info: ' . $e->getMessage());
            }

        }

        return $response;

    }

    /**
     * Instantiate actions
     *
     * @return bool
     */
    public static function instantiateActions(){

        //assertion
        if(empty(CoreLogic::$actions)) return false;

        foreach(CoreLogic::$actions as &$action){

            /**
             * @var \CoreLogicObject $action
             */
            if(!class_exists($action->getName())){
                require $action->getPath() . self::SLASH . $action->getClass();
            }

            /**
             * Class name
             */
            $Name = $action->getName();

            /**
             * @var CoreRenderTemplateInterface $ActionClass
             */
            $ActionClass = new $Name();

            /**
             * UserRegisterAction the template
             */
            $ActionClass->register();

            /**
             * Set logic
             */
            $action->setLogic($ActionClass);

        }

    }

    /**
     * Instantiate exceptions
     *
     * @return bool
     */
    private static function instantiateExceptions(){
        if(empty(CoreLogic::$exceptions)) return false;
        foreach(CoreLogic::$exceptions as &$exception){

            /**
             * @var \CoreLogicObject $exception
             */
            require $exception->getPath() . self::SLASH . $exception->getClass();

            //no need to instantiate exceptions

        }
    }

}