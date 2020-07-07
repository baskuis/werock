<?php

$view = '
<div class="container">
    <div class="form-signin">
        <h2 class="form-signin-heading">{{title}}</h2>
        {{#description}}<p>{{description}}</p>{{/description}}
        ' . CoreTemplate::getView('adminloginform') . '
    </div>
</div>
';