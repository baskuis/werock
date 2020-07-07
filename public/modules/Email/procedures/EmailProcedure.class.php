<?php

/**
 * Email procedure
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class EmailProcedure {

    /** @var EmailRepository $EmailRepository */
    private $EmailRepository;

    function __construct(){
        $this->EmailRepository = CoreLogic::getRepository('EmailRepository');
    }

    /**
     * Un-subscribe email
     *
     * @param null $email
     * @param null $token
     * @return bool
     * @throws Exception
     */
    public function unsubscribeEmail($email = null, $token = null){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new Exception('Invalid email: ' . $email);
        }
        if($token !== CoreStringUtils::saltString($email, SHORT_SALT)){
            throw new Exception('Invalid token');
        }
        if(false === $this->EmailRepository->getUnsubscribedEmail($email)){
            $this->EmailRepository->insertUnsubscribedEmail($email);
            return true;
        }
        throw new Exception('Already un-subscribed');
    }

    /**
     * Email us un-subscribed?
     *
     * @param null $email
     * @return bool
     * @throws Exception
     */
    public function emailUnsubscribed($email = null){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new Exception('Invalid email: ' . $email);
        }
        if(false !== $this->EmailRepository->getUnsubscribedEmail($email)){
            return true;
        }
        return false;
    }

    /**
     * Re-subscribe email
     *
     * @param null $email
     * @param null $token
     * @return bool
     * @throws Exception
     */
    public function reSubscribeEmail($email = null, $token = null){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new Exception('Invalid email: ' . $email);
        }
        if($token !== CoreStringUtils::saltString($email, SHORT_SALT)){
            throw new Exception('Invalid token');
        }
        if(false !== $this->EmailRepository->getUnsubscribedEmail($email)){
            $this->EmailRepository->deleteUnsubscribedEmail($email);
            return true;
        }
        throw new Exception('Email already subscribed');
    }

}