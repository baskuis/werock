<?php

/*
* This defines what needs to be loaded
* Note: path inclusion is not necesary
*/
$crutch = array();

/**
 * Define the crutch
 */
$CoreCrutchObject = new CoreCrutchObject();
$CoreCrutchObject->setVersion('2.66.0-2013.10.09');
$CoreCrutchObject->setFile('blockui.js');
$CoreCrutchObject->setType(CoreCrutches::DOCUMENT_BODY);
$CoreCrutchObject->setTag('script');
$CoreCrutchObject->setAttr(array(
    'type' => 'text/javascript',
    'src' => CoreCrutches::CRUTCH_FILE_PLACEHOLDER
));

//stack it
array_push($crutch, $CoreCrutchObject);