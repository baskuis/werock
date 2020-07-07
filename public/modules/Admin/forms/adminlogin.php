<?php

$method = 'post';

$fields = array();

/**
 * Create Username Field
 */
$FormField = new FormField();
$FormField->setName('username');
$FormField->setLabel('Username');
$FormField->setType('forminputtext');
$FormField->setTemplate('formfieldnaked');
$FormField->setCondition('/^[^\s^\t]+$/');
$FormField->setHelper('Please enter a valid username');
$FormField->setPlaceholder('id: john.parker');
$FormField->setValue(null);
array_push($fields, $FormField);

/**
 * Create Password Field
 */
$FormField = new FormField();
$FormField->setName('password');
$FormField->setLabel('Password');
$FormField->setType('forminputpassword');
$FormField->setTemplate('formfieldnaked');
$FormField->setCondition('password');
$FormField->setHelper('Your password is required');
$FormField->setPlaceholder(null);
$FormField->setValue(null);
array_push($fields, $FormField);

/**
 * Remember me
 */
$FormField = new FormField();
$FormField->setName('login_rememberme');
$FormField->setLabel(null);
$FormField->setType('formcheckbox');
$FormField->setTemplate('formfieldnaked');
$FormField->setCondition(null);
$FormField->setHelper('Remember Me');
$FormField->setPlaceholder(null);
$FormField->setValue('yes');
array_push($fields, $FormField);

/**
 * Create Submit Button
 */
$FormField = new FormField();
$FormField->setName('login_submit');
$FormField->setLabel(null);
$FormField->setType('formbuttonlarge');
$FormField->setTemplate('formfieldnaked');
$FormField->setCondition(null);
$FormField->setHelper(null);
$FormField->setPlaceholder('Sign In');
$FormField->setValue(null);
array_push($fields, $FormField);
