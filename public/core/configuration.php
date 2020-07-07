<?php

/**
 * Get
 */
define('WEROCK_ENVIRONMENT', getenv('WEROCK_ENVIRONMENT'));

/**
 * Check which environment to load
 */
if(!WEROCK_ENVIRONMENT){
    die('Need to set WEROCK_ENVIRONMENT environment variable');
}
if(!is_file(__DIR__ . '/../../environments/configuration.' . WEROCK_ENVIRONMENT . '.php')){
    die('need configuration file at ' . __DIR__ . '/../../environments/configuration.' . WEROCK_ENVIRONMENT . '.php');
}

/**
 * Load configuration
 */
require __DIR__ . '/../../environments/configuration.' . WEROCK_ENVIRONMENT . '.php';

/**
 * Assurances
 * assure directories are set correctly
 */
if(!is_dir(DOCUMENT_ROOT)){
    die('
        <html>
            <body style="background: #ccc;">
                <div style="padding: 30px; font-size: 18px; font-family: Verdana; background: #ffffff; border: 1px solid #999; box-shadow: -3px 3px 6px #666;">
                    <h1 style="margin: 0; padding: 0;">' . WEROCK_ENVIRONMENT . '</h1>
                    <hr />
                    <strong>DOCUMENT_ROOT:</strong> ' . DOCUMENT_ROOT . ' <span style="color: red;">does not point to a directory</span><br />
                    <hr />
                    <strong>HOSTS_ROOT:</strong> ' . HOSTS_ROOT . '<br />
                    <strong>HOST_NAME:</strong> ' . HOST_NAME . '<br />
                    <strong>PUBLIC_FOLDER:</strong> ' . PUBLIC_FOLDER . '
                </div>
            </body>
        </html>');
}