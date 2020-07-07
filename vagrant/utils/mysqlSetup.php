<?php

/**
 * This is the location of the environment configuration file
 * it's passed in by parameter
 *
 */
$LocationOfConfigurationFile = isset($argv[1]) ? $argv[1] : null;

/**
 * If the parameter isn't passed stop here
 *
 */
if(!$LocationOfConfigurationFile) die('echo "no passed path"');

/**
 * If the path isn't valid
 *
 */
if(!is_file($LocationOfConfigurationFile)) die('echo "' . $LocationOfConfigurationFile . ' not a valid path"');

/**
 * Load the configuration file
 *
 */
require $LocationOfConfigurationFile;

/**
 * Build mysql statements
 *
 */
echo 'echo "CREATE DATABASE \`' . MYSQL_DATABASE . '\`; GRANT ALL PRIVILEGES ON \`' . MYSQL_DATABASE . '\`.* TO \'' . MYSQL_USER . '\'@\'localhost\' IDENTIFIED BY \'' . MYSQL_PASSWORD . '\' WITH GRANT OPTION;" | mysql -u root --password=root';