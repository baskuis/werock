<?php

/**
 * Security service
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class SecurityService implements SecurityServiceInterface {

    /* @var SecurityProcedure $SecurityProcedure */
    private $SecurityProcedure;

    /**
     * Construct security service
     */
    function __construct(){
        $this->SecurityProcedure =  CoreLogic::getProcedure('SecurityProcedure');
    }

    /**
     * Capture failed login
     *
     * @param UserAuthenticationObject $UserAuthenticationObject
     * @return Array
     */
    public function captureFailedLogin(UserAuthenticationObject $UserAuthenticationObject){
        return $this->SecurityProcedure->failedLogin($UserAuthenticationObject);
    }

    public function updateRemoteReputation(CoreLogObject $coreLogObject){
        return $this->SecurityProcedure->updateRemoteReputation($coreLogObject);
    }

}