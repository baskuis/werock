<?php

/**
 * Print form and form notifications
 */
$view =
CoreTemplate::getView('formnotifications') .
CoreForm::getForm('adminlogin')->getFullForm();