<?php

/*
* This defines what needs to be loaded
* Note: path inclusion is not necessary
*/
$crutch = array();

/**
 * Define the crutch
 */
$CoreCrutchObject = new CoreCrutchObject();
$CoreCrutchObject->setVersion('4.5.0');
$CoreCrutchObject->setFile('font-awesome-4.5.0/css/font-awesome.min.css');
$CoreCrutchObject->setType(CoreCrutches::DOCUMENT_HEAD);
$CoreCrutchObject->setTag('link');
$CoreCrutchObject->setAttr(array(
   'rel' => 'stylesheet',
    'href' => CoreCrutches::CRUTCH_FILE_PLACEHOLDER
));

//stack it
array_push($crutch, $CoreCrutchObject);
