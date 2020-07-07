<?php

/**
 * Security Proxy
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class SecurityProcedure {

    /** @var SecurityRepository $SecurityRepository */
    private $SecurityRepository;

    /**
     * Security procedure constructor
     */
    function __construct(){
        $this->SecurityRepository = CoreLogic::getRepository('SecurityRepository');
    }

    /**
     * Failed login
     *
     * @param UserAuthenticationObject $UserAuthenticationObject
     * @return Array
     */
    public function failedLogin(UserAuthenticationObject $UserAuthenticationObject){
        return $this->SecurityRepository->addFailedLoginAttempt($UserAuthenticationObject);
    }

    /**
     * Update remote reputation
     *
     * @param CoreLogObject $coreLogObject
     */
    public function updateRemoteReputation(CoreLogObject $coreLogObject){
        if(empty($coreLogObject) || get_class($coreLogObject) != CoreLogObject::class) return;
        if($coreLogObject->getType() != CoreLog::REMOTE) return;
        $this->SecurityRepository->upsertRemoteReputation(
            $coreLogObject->getIp(),
            $coreLogObject->getLevel() == CoreLog::LEVEL_FATAL,
            $coreLogObject->getLevel() == CoreLog::LEVEL_ERROR,
            $coreLogObject->getLevel() == CoreLog::LEVEL_WARN,
            $coreLogObject->getLevel() == CoreLog::LEVEL_INFO
        );
    }

}