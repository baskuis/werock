<?php

/**
 * This template requires
 */
$requires = array('table');

/**
 * Template definition
 */
$view = '
<div class="form-column-simple">
    <div class="form-header">
        <span class="title">{{title}}</span>
        <span class="record-id">#{{MapTableContextObject.primaryValue}}</span>
    </div>
    <div class="content">
        ' . CoreTemplate::getView('formnotifications') . '
        ' . CoreForm::getForm($data['table'])->getFullForm() . '
    </div>
</div>';