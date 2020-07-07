<?php

$view = '
<div class="content">
    <h1>{{title}}</h1>
    {{#description}}<p>{{description}}</p>{{/description}}
    ' . CoreTemplate::getView('adminsessionsform') . '
</div>';