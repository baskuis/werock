<?php

//view
$view = '
<div class="write-message-form">
    <h1>{{title}}</h1>
    <p>{{description}}</p>
    ' . CoreTemplate::getView('formnotifications') . '
    ' . CoreTemplate::getView('writemessageform') . '
</div>
';