<?php

$view = '
<div class="row">
    ' . CoreForm::buildFormHeader('login') . '
    <div class="col-lg-12" style="position: inherit;">' . CoreForm::grabField('login', 'email') . '</div>
    <div class="col-lg-12" style="position: inherit;">' . CoreForm::grabField('login', 'password') . '</div>
    <div class="col-xs-6">' . CoreForm::grabField('login', 'login_submit') . '</div>
    <div class="col-xs-6">' . CoreForm::grabField('login', 'register_link') . '</div>
    <div class="col-lg-12" style="position: inherit;">
        <p>Forgot your password? <a href="' . HTTP_PROTOCOL . DOMAIN_NAME . '/user/password/request">Click here to reset it.</a></p>
    </div>
    ' . CoreForm::buildFormFooter('login') . '
</div>';

/** @var string $script Sets focus on password - if email is already filled - otherwise focus on email */
$script = '
;(function($){
    $(function(){
        if($("#wrapper_for_email input[name=email]").length > 0){
            if($("#wrapper_for_email input[name=email]").val().length > 0){
                $("#wrapper_for_password input[name=password]").focus();
            }else{
                $("#wrapper_for_email input[name=email]").focus();
            }
        }
    });
})(jQuery);
';