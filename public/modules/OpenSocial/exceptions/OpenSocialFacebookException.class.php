<?php

class OpenSocialFacebookException extends Exception implements ControllerException {

    use CoreInterceptorTrait;

    public function _handle(){
        CoreNotification::set('Something went wrong when connecting to facebook. Please refresh your browser and try again?', CoreNotification::ERROR);
        CoreHeaders::setRedirect('/login');
    }

}