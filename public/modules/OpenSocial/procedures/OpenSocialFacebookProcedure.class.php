<?php

/**
 * OpenSocialFacebookProcedure
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class OpenSocialFacebookProcedure {

    const FACEBOOK_DATA_KEY_ID = 'facebook.id';
    const FACEBOOK_DATA_KEY_PICTURE_URL = 'facebook.profile.picture.url';

    /** @var OpenSocialFacebookRepository $OpenSocialFacebookRepository */
    private $OpenSocialFacebookRepository;

    /** @var UserProcedure $UserProcedure */
    private $UserProcedure;

    /** @var UserService $UserService */
    private $UserService;

    function __construct(){
        $this->OpenSocialFacebookRepository = CoreLogic::getRepository('OpenSocialFacebookRepository');
        $this->UserProcedure = CoreLogic::getProcedure('UserProcedure');
        $this->UserService = CoreLogic::getService('UserService');
    }

    /**
     * Is enabled
     *
     * @return bool
     */
    public function isEnabled(){
        return $this->OpenSocialFacebookRepository->isEnabled();
    }

    /**
     * Get Facebook login url
     * including some assertions
     *
     * @param null $url
     * @return string
     */
    public function getConnectUrl($url = null){
        if(empty($url)) CoreLog::error('No url passed');
        if(false === stripos($url, DOMAIN_NAME)) CoreLog::error('Url needs to contain ' . DOMAIN_NAME);
        return $this->OpenSocialFacebookRepository->getConnectUrl($url);
    }

    /**
     * Facebook connect callback
     * 
     * @param bool $createAccount
     * @throws Exception
     */
    public function connectCallback($createAccount = false){

        /** @var OpenSocialFacebookCallbackUserObject $OpenSocialFacebookCallbackUserObject */
        $OpenSocialFacebookCallbackUserObject = $this->OpenSocialFacebookRepository->connectCallback();

        /** @var UserObject $UserObject */
        if(false !== ($UserObject = $this->UserService->getUserByEmail($OpenSocialFacebookCallbackUserObject->getEmail()))){
            $this->UserService->authenticateUser($UserObject);
            $this->UserService->setData(UserProcedure::DATA_KEY_FIRST_NAME, $OpenSocialFacebookCallbackUserObject->getFirstName(), $UserObject);
            $this->UserService->setData(UserProcedure::DATA_KEY_LAST_NAME, $OpenSocialFacebookCallbackUserObject->getLastName(), $UserObject);
            $this->UserService->setData(self::FACEBOOK_DATA_KEY_ID, $OpenSocialFacebookCallbackUserObject->getId(), $UserObject);
            $this->UserService->setData(self::FACEBOOK_DATA_KEY_PICTURE_URL, $OpenSocialFacebookCallbackUserObject->getPicture(), $UserObject);
            return;
        }

        /** If we need to create an account */
		if($createAccount){	
			
			/** @var string $email */
			$email = $OpenSocialFacebookCallbackUserObject->getEmail();
			if(empty($email)){
				CoreNotification::set('Unable to create account using facebook. We did not receive an email from facebook to create your account.', CoreNotification::ERROR);
	           	CoreHeaders::setRedirect('/register');
	           	return;
			}
			
			/** @var string $username */
			$username = self::generateUniqueUsername($email);
			
	        /** @var UserTemplateObject $UserTemplateObject */
	        $UserTemplateObject = CoreLogic::getObject('UserTemplateObject');
	        $UserTemplateObject->setFirstName($OpenSocialFacebookCallbackUserObject->getFirstName());
	        $UserTemplateObject->setLastName($OpenSocialFacebookCallbackUserObject->getLastName());
	        $UserTemplateObject->setEmail($OpenSocialFacebookCallbackUserObject->getEmail());
	        $UserTemplateObject->setUsername($username);
	        $UserTemplateObject->setPassword(CoreStringUtils::saltString($OpenSocialFacebookCallbackUserObject->getEmail(), SHORT_SALT));
	
	        /** in case of failure show error and redirect to register */
	        if(false === ($UserObject = $this->UserService->create($UserTemplateObject))){
	           	CoreNotification::set('Unable to create account using facebook.', CoreNotification::ERROR);
	           	CoreHeaders::setRedirect('/register');
	           	return;
	        }
        
        }else{
            CoreNotification::set('Unable to login with facebook using email: ' . $OpenSocialFacebookCallbackUserObject->getEmail() . '<br /> Do you want to <a href="/register">sign up</a> instead?', CoreNotification::ERROR);
            CoreHeaders::setRedirect('/login');
            return;
        }

    }

    /**
     * Generate unique username
     *
     * @param null $email
     * @return string
     */
    private function generateUniqueUsername($email = null){
	    $base = substr($email, 0, strpos($email, '@'));
	    while(false !== $this->haveUserByUsername($base)){
            $base = $base . rand(1,9);
        }
        return $base;
    }

    /**
     * Get user by username which suppresses thrown not found exceptions
     *
     * @param null $username
     * @return bool
     */
    private function haveUserByUsername($username = null){
        try {
            $this->UserProcedure->getUserByUsername($username);
            return true;
        } catch(UserNotFoundException $e){
            //ignore
        }
        return false;
    }


}