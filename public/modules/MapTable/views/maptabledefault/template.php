<?php

/**
 * This template requires
 */
$requires = array('table', 'MapTableContextObject');

/**
 * Template definition
 */
$view = '
' . CoreTemplate::getView('maptablelisting') . '
<div class="form-column">
    <div class="form-header">
        <span class="title">' . $data['title'] . '</span>
        <span class="record-id">#' . $data['MapTableContextObject']->primaryValue . '</span>
    </div>
    <div class="content">
        ' . CoreTemplate::render('formnotifications', $data) . '
        ' . CoreForm::getForm($data['table'])->getFullForm() . '
    </div>
</div>';