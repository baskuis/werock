<?php

$view =
'<div class="content">
    <h1>' . $data['title'] . '</h1>
    <p>' . $data['description'] . '</p>' .
    CoreTemplate::render('formnotifications', $data) .
    CoreForm::getForm('admingoogletools')->getFullForm() . '
</div>';