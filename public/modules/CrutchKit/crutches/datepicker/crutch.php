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
$CoreCrutchObject->setVersion('1.4.0');
$CoreCrutchObject->setFile('bootstrap-datepicker-master/dist/css/bootstrap-datepicker3.min.css');
$CoreCrutchObject->setType(CoreCrutches::DOCUMENT_HEAD);
$CoreCrutchObject->setTag('link');
$CoreCrutchObject->setAttr(array(
    'rel' => 'stylesheet',
    'type' => 'text/css',
    'media' => 'screen',
    'href' => CoreCrutches::CRUTCH_FILE_PLACEHOLDER
));

//and stack it
array_push($crutch, $CoreCrutchObject);

/**
 * Define the crutch
 */
$CoreCrutchObject = new CoreCrutchObject();
$CoreCrutchObject->setVersion('1.4.0');
$CoreCrutchObject->setFile('bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js');
$CoreCrutchObject->setType(CoreCrutches::DOCUMENT_BODY);
$CoreCrutchObject->setTag('script');
$CoreCrutchObject->setAttr(array(
    'type' => 'text/javascript',
    'src' => CoreCrutches::CRUTCH_FILE_PLACEHOLDER
));

//and stack it
array_push($crutch, $CoreCrutchObject);