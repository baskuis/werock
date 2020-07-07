<?php

$view = '
<div class="row">
    ' . CoreForm::buildFormHeader('updatepassword') . '
    <div class="col-lg-12" style="position: inherit;">' . CoreForm::grabField('updatepassword', 'current') . '</div>
    <div class="col-xs-12 col-md-6">' . CoreForm::grabField('updatepassword', 'password') . '</div>
    <div class="col-xs-12 col-md-6">' . CoreForm::grabField('updatepassword', 'password_repeat') . '</div>
    <div class="col-xs-12 col-md-6">' . CoreForm::grabField('updatepassword', 'update_submit'). '</div>
    ' . CoreForm::buildFormFooter('updatepassword') . '
</div>
';