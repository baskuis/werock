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
$CoreCrutchObject->setVersion('2.2');
$CoreCrutchObject->setFile('swfobject.js');
$CoreCrutchObject->setType(CoreCrutches::DOCUMENT_BODY);
$CoreCrutchObject->setTag('script');
$CoreCrutchObject->setAttr(array(
   'type' => 'text/javascript',
   'src' => CoreCrutches::CRUTCH_FILE_PLACEHOLDER
));

//and stack it
array_push($crutch, $CoreCrutchObject);

