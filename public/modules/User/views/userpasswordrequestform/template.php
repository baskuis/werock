<?php

$view = '
{{#linkRequested}}
<div class="row">
    <div class="col-lg-12" style="position: inherit;">
        <p class="lead">A message with a reset link has been sent to your email. Please click on the link in the email to choose your new password.</p>
    </div>
</div>
{{/linkRequested}}
{{^linkRequested}}
<div class="row">
    ' . CoreForm::buildFormHeader('resetpassword') . '
    <div class="col-lg-12" style="position: inherit;">' . CoreForm::grabField('resetpassword', 'email') . '</div>
    <div class="col-lg-12" style="position: inherit;">' . CoreForm::grabField('resetpassword', 'altcaptcha') . '</div>
    <div class="col-xs-12 col-md-6">' . CoreForm::grabField('resetpassword', 'update_submit'). '</div>
    ' . CoreForm::buildFormFooter('resetpassword') . '
</div>
{{/linkRequested}}
';