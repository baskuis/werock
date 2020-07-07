<?php

/** @var EmailService $EmailService */
$EmailService = CoreLogic::getService('EmailService');

$view = '
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h1>Un-subscribe Email</h1>
            <p class="lead">We\'re sorry you do not see value in our email correspondence. Once un-subscribed you will no longer receive email from us. Thanks for your interest in myregistryhub!</p>
            ' . CoreTemplate::getView('formnotifications');
if(false === $EmailService->unsubscribed(isset($_GET['email']) ? $_GET['email'] : null)) {
    $view .= CoreForm::getForm('unsubscribe_form')->getFullForm();
}else{
    $view .= '
            <p>
                <a href="/" class="btn btn-lg btn-primary">Continue</a>
            </p>';
}
$view .= '
        </div>
    </div>
</div>
';