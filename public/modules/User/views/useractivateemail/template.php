<?php
$view = '
<h1>Email Activation</h1>
' . CoreTemplate::getView('formnotifications') . '
{{#activated}}
    Email Activated! You can now
    <a href="/login">Login</a>.
{{/activated}}
';