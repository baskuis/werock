<?php

$view = '
<div class="content">
    <h1>{{title}}</h1>
    <p>{{description}}</p>
    ' . CoreTemplate::getView('formnotifications') . '
    ' . CoreForm::getForm('performanceFormSortRoutes')->getFullForm() . '
</div>
';