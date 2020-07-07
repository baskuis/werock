<?php

/**
 * Core Module Object
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreModuleObject {

    public $name;
    public $logic;
    public $path;
    public $file;
    public $moduleName;
    public $description;
    public $version;

    /** @var boolean $enabled */
    public $enabled;

    /** @var array $dependencies */
    public $dependencies;

    /** @var boolean $loaded */
    public $loaded;

    /** @var boolean $instantiated */
    public $instantiated;

    /** @var boolean $discovered */
    public $discovered;

    /** @var array $languages */
    public $languages;

    /** @var array $views */
    public $views;

    /** @var array $interfaces */
    public $interfaces;

    /** @var array $services */
    public $services;

    /** @var array $repositories */
    public $repositories;

    /** @var array $procedures */
    public $procedures;

    /** @var array $actions */
    public $actions;

    /** @var array $exceptions */
    public $exceptions;

    /** @var array $objects */
    public $objects;

    /** @var array $crutches */
    public $crutches;

    /** @var CoreInitAssetsReferenceObject $coreInitAssetsReferenceObject */
    public $coreInitAssetsReferenceObject;

    /** @var array $menus */
    public $menus;

    /** @var array $routes */
    public $routes;

    /** @var array $interceptors */
    public $interceptors;

    /** @var array $observers */
    public $observers;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLogic()
    {
        return $this->logic;
    }

    /**
     * @param mixed $logic
     */
    public function setLogic($logic)
    {
        $this->logic = $logic;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return mixed
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @param mixed $moduleName
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @param array $dependencies
     */
    public function setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @return boolean
     */
    public function isLoaded()
    {
        return $this->loaded;
    }

    /**
     * @param boolean $loaded
     */
    public function setLoaded($loaded)
    {
        $this->loaded = $loaded;
    }

    /**
     * @return boolean
     */
    public function isInstantiated()
    {
        return $this->instantiated;
    }

    /**
     * @param boolean $instantiated
     */
    public function setInstantiated($instantiated)
    {
        $this->instantiated = $instantiated;
    }

    /**
     * @return boolean
     */
    public function isDiscovered()
    {
        return $this->discovered;
    }

    /**
     * @param boolean $discovered
     */
    public function setDiscovered($discovered)
    {
        $this->discovered = $discovered;
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param array $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return array
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param array $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param array $services
     */
    public function setServices($services)
    {
        $this->services = $services;
    }

    /**
     * @return array
     */
    public function getRepositories()
    {
        return $this->repositories;
    }

    /**
     * @param array $repositories
     */
    public function setRepositories($repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * @return array
     */
    public function getProcedures()
    {
        return $this->procedures;
    }

    /**
     * @param array $procedures
     */
    public function setProcedures($procedures)
    {
        $this->procedures = $procedures;
    }

    /**
     * @return array
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * @param array $interfaces
     */
    public function setInterfaces($interfaces)
    {
        $this->interfaces = $interfaces;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * @return array
     */
    public function getCrutches()
    {
        return $this->crutches;
    }

    /**
     * @param array $crutches
     */
    public function setCrutches($crutches)
    {
        $this->crutches = $crutches;
    }

    /**
     * @return array
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * @param array $exceptions
     */
    public function setExceptions($exceptions)
    {
        $this->exceptions = $exceptions;
    }

    /**
     * @return array
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * @param array $objects
     */
    public function setObjects($objects)
    {
        $this->objects = $objects;
    }

    /**
     * @return CoreInitAssetsReferenceObject
     */
    public function getCoreInitAssetsReferenceObject()
    {
        return $this->coreInitAssetsReferenceObject;
    }

    /**
     * @param CoreInitAssetsReferenceObject $coreInitAssetsReferenceObject
     */
    public function setCoreInitAssetsReferenceObject($coreInitAssetsReferenceObject)
    {
        $this->coreInitAssetsReferenceObject = $coreInitAssetsReferenceObject;
    }

    /**
     * @return array
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * @param array $menus
     */
    public function setMenus($menus)
    {
        $this->menus = $menus;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param array $routes
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }

    /**
     * @return array
     */
    public function getInterceptors()
    {
        return $this->interceptors;
    }

    /**
     * @param array $interceptors
     */
    public function setInterceptors($interceptors)
    {
        $this->interceptors = $interceptors;
    }

    /**
     * @return array
     */
    public function getObservers()
    {
        return $this->observers;
    }

    /**
     * @param array $observers
     */
    public function setObservers($observers)
    {
        $this->observers = $observers;
    }

}