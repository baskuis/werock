<?php

/**
 * OpenSocialFacebookService
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class OpenSocialFacebookService implements OpenSocialFacebookServiceInterface {

    /** @var OpenSocialFacebookProcedure $OpenSocialFacebookProcedure */
    private $OpenSocialFacebookProcedure;

    /**
     * OpenSocialFacebookService constructor.
     */
    function __construct(){
        $this->OpenSocialFacebookProcedure = CoreLogic::getProcedure('OpenSocialFacebookProcedure');
    }

    /**
     * Is enabled
     *
     * @return bool
     */
    public function isEnabled(){
        return $this->OpenSocialFacebookProcedure->isEnabled();
    }

    /**
     * Get connect url
     *
     * @param null $url
     * @return string
     */
    public function getConnectUrl($url = null){
        try {
            return $this->OpenSocialFacebookProcedure->getConnectUrl($url);
        } catch (Exception $e) {
            CoreNotification::set('Unable to generate Facebook login url. Info: ' . $e->getMessage(), CoreNotification::ERROR);
        }
    }

    /**
     * Connect callback handler
     * Takes care of creating/authenticating as a user
     * and populating the user profile
     *
     * @param bool $createAccount
     * @throws OpenSocialFacebookException will redirect to /login with message
     */
    public function connectCallback($createAccount){
        return $this->OpenSocialFacebookProcedure->connectCallback($createAccount);
    }

}