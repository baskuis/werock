<?php

$view = '
<div class="row">
    ' . CoreForm::buildFormHeader('register') . '
    <div class="col-xs-6">
        ' . CoreForm::grabField('register', 'first_name') . '
    </div>
    <div class="col-xs-6">
        ' . CoreForm::grabField('register', 'last_name') . '
    </div>
    <div class="col-lg-12" style="position: inherit;">' . CoreForm::grabField('register', 'username') . '</div>
    <div class="col-lg-12" style="position: inherit;">' . CoreForm::grabField('register', 'email') . '</div>
    <div class="col-xs-6">' . CoreForm::grabField('register', 'password') . '</div>
    <div class="col-xs-6">' . CoreForm::grabField('register', 'password_repeat') . '</div>
    <div class="col-lg-12" style="position: inherit;">' . CoreForm::grabField('register', 'altcaptcha') . '</div>
    <div class="col-xs-6">' . CoreForm::grabField('register', 'register_submit') . '</div>
    <div class="col-xs-6">' . CoreForm::grabField('register', 'login_link') . '</div>
    ' . CoreForm::buildFormFooter('register') . '
</div>
';

$script = '
;(function($){
    $(function(){
        if($("#wrapper_for_first_name input[name=first_name]:visible").length > 0){
            if($("#wrapper_for_first_name input[name=first_name]").val().length == 0){
                $("#wrapper_for_first_name input[name=first_name]").focus();
            }
        }
    });
})(jQuery);        
';