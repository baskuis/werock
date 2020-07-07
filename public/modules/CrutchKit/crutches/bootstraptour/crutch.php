<?php

/**
 * This defines what needs to be loaded
 * Note: path inclusion is not necessary
 */
$crutch = array();

/**
 * Define the crutch
 */
$CoreCrutchObject = new CoreCrutchObject();
$CoreCrutchObject->setVersion('1.1.0');
$CoreCrutchObject->setFile('0.11.0/css/bootstrap-tour.min.css');
$CoreCrutchObject->setType(CoreCrutches::DOCUMENT_HEAD);
$CoreCrutchObject->setTag('link');
$CoreCrutchObject->setAttr(array(
    'rel' => 'stylesheet',
    'type' => 'text/css',
    'media' => 'screen',
    'href' => CoreCrutches::CRUTCH_FILE_PLACEHOLDER
));
$CoreCrutchObject->setDependencies(array('bootstrap'));

//and stack it
array_push($crutch, $CoreCrutchObject);

/**
 * Define the crutch
 */
$CoreCrutchObject = new CoreCrutchObject();
$CoreCrutchObject->setVersion('0.11.0');
$CoreCrutchObject->setFile('0.11.0/js/bootstrap-tour.min.js');
$CoreCrutchObject->setType(CoreCrutches::DOCUMENT_BODY);
$CoreCrutchObject->setTag('script');
$CoreCrutchObject->setAttr(array(
    'type' => 'text/javascript',
    'src' => CoreCrutches::CRUTCH_FILE_PLACEHOLDER
));
$CoreCrutchObject->setDependencies(array('jquery', 'bootstrap'));

//and stack it
array_push($crutch, $CoreCrutchObject);