<?php

$view = '
<div class="container">
    <h1>{{title}}</h1>
	' . CoreTemplate::getView('formnotifications') . '
    ' . CoreTemplate::getView('userpasswordresetform') . '
</div>
';
