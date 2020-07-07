<?php

interface SecurityServiceInterface {

    public function captureFailedLogin(UserAuthenticationObject $UserAuthenticationObject);

}