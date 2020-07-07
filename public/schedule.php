<?php

/**
 * Set passed env
 */
if(!isset($argv[1])) die('No env passed as 1st param');
putenv('WEROCK_ENVIRONMENT=' . $argv[1]);

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
 * Only allow cli
 */
if(!CoreSecUtils::isCli()) exit(0);

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
 * Reflection phase is over
 */
CoreInit::$reflection = false;

/**
 * Execute scheduled jobs
 */
CoreSchedule::execute();

/**
 * Unload page, analytics etc
 */
CoreInit::unload();