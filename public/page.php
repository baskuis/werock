<?php

/**
 * Render Page
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
 * Initial include
 */
require(__DIR__ . '/core/init.php');

/**
 * Legal entry point
 * Other php files .. not to be web viewable .. could simply
 * not have this declaration..
 */
CoreSecurity::setEntry(true);

/**
 * Stop requests here that are intended to validate
 * if cache needs to be busted on the client side
 */
CoreCache::blockCacheRequests();

/**
 * Start reflection phase
 */
CoreInit::$reflection = true;

/**
 * Load application, modules, plugins, and theme
 * Init can be a static class .. since there
 * will only be one instance
 *
 * Plugins will need to register first since they will add listeners
 * on modules and core. Modules can add listeners to core but not
 * depend on plugins. Somewhere we will need to honor dependency
 */
CoreInit::loadCore();
CoreInit::loadModules();
CoreInit::loadPlugins();

/**
 * Load theme assets
 * this could be dependant on the
 * identity of the user/visitor
 */
CoreInit::loadTheme();

/**
 * Reflection phase is over
 */
CoreInit::$reflection = false;

/**
 * Identify visitor, user etc
 */
CoreInit::identify();

/**
 * Instantiate actions
 */
CoreLogic::instantiateActions();

/**
 * Route request
 */
CoreInit::routeRequest(CoreControllerObject::GROUP_PAGE);

/**
 * Render page, from template, elements, widgets
 * All data to live in db
 */
CoreInit::setHeaders();
if(CoreHeaders::needBody()){
    CoreInit::render();
}

/**
 * Unload page, analytics etc
 */
CoreInit::unload();