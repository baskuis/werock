<?php

$view = '
{{#passwordReset}}
<div class="row">
    <div class="col-lg-12" style="position: inherit;">
        <p class="lead">Your password has been reset! You can now <a href="/login">login</a> with your new password.</p>
    </div>
</div>
{{/passwordReset}}
{{^passwordReset}}
<div class="row">
    ' . CoreForm::buildFormHeader('resetpassword') . '
    <div class="col-xs-12 col-md-6">' . CoreForm::grabField('resetpassword', 'password') . '</div>
    <div class="col-xs-12 col-md-6">' . CoreForm::grabField('resetpassword', 'password_repeat') . '</div>
    <div class="col-lg-12" style="position: inherit;">' . CoreForm::grabField('resetpassword', 'altcaptcha') . '</div>
    <div class="col-xs-12 col-md-6">' . CoreForm::grabField('resetpassword', 'update_submit'). '</div>
    ' . CoreForm::buildFormFooter('resetpassword') . '
</div>
{{/passwordReset}}
';