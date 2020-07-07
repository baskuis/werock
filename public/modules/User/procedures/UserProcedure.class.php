<?php

/**
 * User procedure
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserProcedure {

    /**
     * Proxy constants
     */
    const CACHE_KEY_USER_DATA = 'user:data';
    const CACHE_DURATION_USER_DATA = 84000;

    const DATA_KEY_FIRST_NAME = 'firstname';
    const DATA_KEY_LAST_NAME = 'lastname';

    const VISITOR_DATA_USERID = 'userid';
    const VISITOR_DATA_FIRST_NAME = 'firstname';
    const VISITOR_DATA_LAST_NAME = 'lastname';
    const VISITOR_DATA_USERNAME = 'username';
    const VISITOR_DATA_EMAIL = 'email';

    const OPEN_SQUARE_BRACKET = '[';
    const CLOSE_SQUARE_BRACKET = ']';
    
    /** @var UserRepository $UserRepository */
    private $UserRepository;

    /** @var EmailService $EmailService */
    private $EmailService;

    function __construct(){
        $this->UserRepository = CoreLogic::getRepository('UserRepository');
        $this->EmailService = CoreLogic::getService('EmailService');
    }

    /**
     * Generate user access token
     *
     * @return String
     * @throws Exception
     * @throws UserNoUserSessionException
     */
    public function generateAccessToken(){
        return $this->UserRepository->generateAccessToken();
    }

    /**
     * Update the current user password
     *
     * @param null $oldPassword
     * @param null $newPassword
     * @return bool
     * @throws Exception
     * @throws UserUnauthorizedException
     */
    public function updatePassword($oldPassword = null, $newPassword = null){
        if(CoreUser::$anonymous){
            throw new UserUnauthorizedException('You need to be logged in to update your password');
        }
        $updated = $this->UserRepository->updatePassword($oldPassword, $newPassword);
        if(!$updated){
            throw new Exception('Unable to update password, current password invalid?');
        }
        return true;
    }

	/**
	 * Find existing user
	 *
	 * @param UserAuthenticationObject $UserAuthenticationObject
	 * @return UserObject
	 */	
	public function authenticate($UserAuthenticationObject){
		
		/** @var UserObject $UserObject */
		$UserObject =  $this->UserRepository->authenticate($UserAuthenticationObject);

        /** populate user object */
        $UserObject->setFirstname($this->getData(self::DATA_KEY_FIRST_NAME, $UserObject));
        $UserObject->setLastname($this->getData(self::DATA_KEY_LAST_NAME, $UserObject));

        /** Capture remember me */
        self::captureRememberMe($UserObject);

        return $UserObject;
		
	}

	/**
	 * Create user
	 *
	 * @param UserTemplateObject $UserTemplateObject
	 * @return UserObject Returns user
	 */
	public function create($UserTemplateObject = null){

        /** @var UserObject $UserObject */
		$UserObject = $this->UserRepository->create($UserTemplateObject);

        /** Capture remember me */
        self::captureRememberMe($UserObject);

        return $UserObject;
		
	}

    /**
     * Capture remember me
     *
     * @param UserObject $userObject
     * @return bool
     */
    public function captureRememberMe(UserObject $userObject){
        try {
            CoreVisitor::store(self::VISITOR_DATA_USERID, $userObject->getId());
            CoreVisitor::store(self::VISITOR_DATA_USERNAME, $userObject->getUsername());
            CoreVisitor::store(self::VISITOR_DATA_EMAIL, $userObject->getEmail());
            CoreVisitor::store(self::VISITOR_DATA_FIRST_NAME, $userObject->getFirstname());
            CoreVisitor::store(self::VISITOR_DATA_LAST_NAME, $userObject->getLastname());
            return true;
        } catch(Exception $e){
            CoreLog::debug('Unable to store visitor data. Info: ' . $e->getMessage());
        }
        return false;
    }

    /**
     * Get user
     *
     * @param null $userId
     * @return UserObject
     * @throws UserNotFoundException
     */
	public function getUser($userId = null){

		/** @var UserObject $UserObject */
		$UserObject = $this->UserRepository->getUser($userId);
		if(!$UserObject) throw new UserNotFoundException('No user found for id: ' . $userId);

        /** populate user object */
        $UserObject->setFirstname($this->getData(self::DATA_KEY_FIRST_NAME, $UserObject));
        $UserObject->setLastname($this->getData(self::DATA_KEY_LAST_NAME, $UserObject));

        return $UserObject;

	}

    /**
     * Update user access token
     *
     * @param null $userId
     * @param null $accessToken
     * @throws Exception
     */
    public function updateUserAccessToken($userId = null, $accessToken = null){
        if(false === $this->UserRepository->updateUserAccessToken($userId, $accessToken)){
            throw new Exception('Unable to set access token');
        }
    }

    /**
     * Get user by access token
     *
     * @param null $accessToken
     * @return UserObject
     * @throws UserNotFoundException
     */
    public function getUserByAccessToken($accessToken = null){

        /** @var UserObject $UserObject */
        $UserObject = $this->UserRepository->getUserByAccessToken($accessToken);
        if(!$UserObject) throw new UserNotFoundException('Invalid access token');

        /** populate user object */
        $UserObject->setFirstname($this->getData(self::DATA_KEY_FIRST_NAME, $UserObject));
        $UserObject->setLastname($this->getData(self::DATA_KEY_LAST_NAME, $UserObject));

        return $UserObject;

    }

    /**
     * Request password reset
     * for email
     *
     * @param bool $email
     * @throws Exception
     */
    public function requestPasswordReset($email = false){

        /**
         * Assertions
         */
        if(!$email || !is_string($email)){
            throw new Exception('No valid email provided');
        }

        /**
         * Lookup user
         */
        $UserObject = $this->getUserByEmail($email);

        /**
         * Get existing key - or create and store new one on account
         */
        if (false === ($key = $this->UserRepository->getUserResetKey($UserObject->getId(), strtotime('-12 hours')))) {
            $key = uniqid(md5($UserObject->getEmail()));
            $this->UserRepository->setUserResetKey($UserObject->getId(), $key);
        }

        /** @var EmailObject $EmailObject */
        $EmailObject = CoreLogic::getObject('EmailObject');

        /** Set subject */
        $EmailObject->setSubject('Reset Your Password');

        /** @var EmailAddresseeObject $EmailAddresseeObject */
        $EmailAddresseeObject = CoreLogic::getObject('EmailAddresseeObject');
        $EmailAddresseeObject->setName($UserObject->getFirstname());
        $EmailAddresseeObject->setEmail($UserObject->getEmail());

        $EmailObject->addAddressee($EmailAddresseeObject);

        $emailHtml = CoreTemplate::render('resetpasswordemail', array(
            'resetKey' => $key,
            'firstName' => $UserObject->getFirstname()
        ));

        $EmailObject->setHtmlBody($emailHtml);
        $EmailObject->setTextBody(strip_tags($emailHtml));

        $this->EmailService->sendSmtp($EmailObject);

    }

    /**
     * Reset password
     *
     * @param null $key
     * @param null $password
     * @return bool
     * @throws Exception
     * @throws UserPasswordResetKeyInvalidException
     */
    public function resetPassword($key = null, $password = null){
        if(!$password) throw new Exception('No password provided');
        if(false !== ($this->UserRepository->resetUserPassword($key, $password))){
            return true;
        }
        throw new UserPasswordResetKeyInvalidException();
    }

    /**
     * Is reset key valid
     *
     * @param null $key
     * @return bool
     * @throws Exception
     */
    public function resetKeyValid($key = null){
        if(!$key) throw new Exception('No key provided');
        if(false !== ($this->UserRepository->resetKeyValid($key))){
            return true;
        }
        return false;
    }

    /**
     * Is email activated?
     *
     * @param UserObject $userObject
     * @return mixed
     * @throws Exception
     */
    public function activatedEmail(UserObject $userObject){
        if(empty($userObject)) throw new Exception('User must be passed');
        return $this->UserRepository->activatedEmail($userObject->getEmail());
    }

    /**
     * Get user by email
     *
     * @param null $email
     * @return UserObject
     * @throws UserNotFoundException
     */
    public function getUserByEmail($email = null){
        $UserObject = $this->UserRepository->getUserByEmail($email);
        $UserObject->setFirstname($this->getData(self::DATA_KEY_FIRST_NAME, $UserObject));
        $UserObject->setLastname($this->getData(self::DATA_KEY_LAST_NAME, $UserObject));
        return $UserObject;
    }

    /**
     * Get user by username
     *
     * @param null $username
     * @return UserObject
     * @throws UserNotFoundException
     */
    public function getUserByUsername($username = null){

        $row = $this->UserRepository->getUserByUsername($username);

        /** @var UserObject $UserObject */
        $UserObject = CoreLogic::getObject('UserObject');

        if(!isset($row['werock_user_id'])){
            throw new UserNotFoundException();
        }

        /** populate user object */
        $UserObject->setId((int) $row['werock_user_id']);
        $UserObject->setUsername($row['werock_user_username']);
        $UserObject->setEmail($row['werock_user_email_value']);
        $UserObject->setFirstname($this->getData(self::DATA_KEY_FIRST_NAME, $UserObject));
        $UserObject->setLastname($this->getData(self::DATA_KEY_LAST_NAME, $UserObject));


        return $UserObject;

    }

    /**
     * Get Users
     *
     * @param UserSearchObject $UserSearchObject
     * @return UserObject Returns user
     */
    public function getUsers(UserSearchObject $UserSearchObject){

        //find user
        return $this->UserRepository->getUsers($UserSearchObject);

    }

    /**
     * Activate email
     *
     * @param String $key
     * @return UserObject Returns user
     */
    public function activateEmail($key = null){

        /**
         * Return result from DAO
         */
        return $this->UserRepository->activateEmail($key);

    }

    /**
     * Set User Data
     *
     * @param null $key
     * @param null $value
     * @param UserObject $UserObject
     * @return mixed
     */
    public function setData($key = null, $value = null, $UserObject = null){

        //invalidate cache
        CoreCache::deleteCache(self::CACHE_KEY_USER_DATA . self::OPEN_SQUARE_BRACKET . $UserObject->getId() . self::CLOSE_SQUARE_BRACKET . $key);

        //set user data
        return $this->UserRepository->setData($key, $value, $UserObject);

    }

    /**
     * Get User Data
     *
     * @param null $key
     * @param UserObject $UserObject
     * @return mixed
     */
    public function getData($key = null, $UserObject = null){

        /**
         * Generate cache key
         */
        $cacheKey = self::CACHE_KEY_USER_DATA . self::OPEN_SQUARE_BRACKET . $UserObject->getId() . self::CLOSE_SQUARE_BRACKET . $key;

        /**
         * Attempt to retrieve from cache
         */
        $value = CoreCache::getCache($cacheKey);

        /**
         * Or request it from the DAO
         */
        if(!$value){

            //get it from the DAO
            $value = $this->UserRepository->getData($key, $UserObject);

            //store in cache
            CoreCache::saveCache($cacheKey, $value, self::CACHE_DURATION_USER_DATA);

        }

        //set data
        return trim($value);

    }

    /**
     * Send email activation reminder
     */
    public function sendEmailActivationReminders(){
        $rows = $this->UserRepository->getUnactivatedEmailsOlderThan(1);
        if(!empty($rows)){
            foreach($rows as $row){
                try {

                    /** safety first */
                    if (!isset($row['werock_user_email_id']) || empty($row['werock_user_email_id'])) continue;
                    if (!isset($row['werock_user_id']) || empty($row['werock_user_id'])) continue;
                    if (!isset($row['werock_user_email_key']) || empty($row['werock_user_email_key'])) continue;

                    /** @var UserObject $UserObject */
                    $UserObject = $this->getUser($row['werock_user_id']);

                    /** send activation email reminder */
                    self::sendEmailActivationReminder($UserObject, (int) $row['werock_user_email_id'], $row['werock_user_email_value']);

                } catch(Exception $e){

                    /** Log error */
                    CoreLog::error('Issue when attempting to sendEmailActivationReminders. Info: ' . $e->getMessage(), $e);

                    /** If user is not found let's update the unactivated email reminded flag - user could have been administratively deleted */
                    if (isset($row['werock_user_email_id'])) {
                        $this->UserRepository->updateUnactivatedEmailReminded($row['werock_user_email_id']);
                    }

                }
            }
        }
    }

    /**
     * Get activation key by email
     *
     * @param $email
     * @return bool
     * @throws Exception
     */
    private function getActivationKeyByEmail($email = null){
        if(empty($email)) throw new Exception('Email required');
        $row = $this->UserRepository->getActivationKeyByEmail($email);
        return isset($row['werock_user_email_key']) ? $row['werock_user_email_key'] : false;
    }

    /**
     * Get email id by email
     *
     * @param null $email
     * @return bool
     * @throws Exception
     */
    private function getEmailIdByEmail($email = null){
        if(empty($email)) throw new Exception('Email required');
        $row = $this->UserRepository->getEmailIdByEmail($email);
        return isset($row['werock_user_email_id']) ? $row['werock_user_email_id'] : false;
    }

    /**
     * Send email activation reminder
     *
     * @param UserObject $UserObject
     * @param null $emailId
     * @param null $email
     * @return bool
     * @throws Exception
     */
    public function sendEmailActivationReminder(UserObject $UserObject, $emailId = null, $email = null){

        /** Get email id if needed */
        if(empty($emailId)){
            $r = $this->UserRepository->getEmailIdByEmail($UserObject->getEmail());
            $emailId = isset($r['werock_user_email_id']) ? (int) $r['werock_user_email_id'] : null;
        }

        /** Unable to find email Id */
        if(empty($emailId)){ throw new Exception('Unable to find emailId'); }

        /** @var string $useEmail */
        $useEmail = !empty($UserObject->getEmail()) ? $UserObject->getEmail() : $email;

        /** @var string $key */
        $key = $this->getActivationKeyByEmail($useEmail);
        if(!empty($key)) {

            /** @var EmailAddresseeObject $EmailAddresseeObject */
            $EmailAddresseeObject = CoreLogic::getObject('EmailAddresseeObject');
            $EmailAddresseeObject->setName($UserObject->getFirstname());
            $EmailAddresseeObject->setEmail($UserObject->getEmail());

            /** @var EmailAddresseeObject $SentFromObject */
            $SentFromObject = CoreLogic::getObject('EmailAddresseeObject');

            $SentFromObject->setName(SITE_NAME);
            $SentFromObject->setEmail(SITE_EMAIL);

            /** @var EmailObject $EmailObject */
            $EmailObject = CoreLogic::getObject('EmailObject');
            $EmailObject->setSentfrom($SentFromObject);
            $EmailObject->setAddressees(array($EmailAddresseeObject));
            $EmailObject->setSubject('Please Confirm Your Email');
            $EmailObject->setHtmlBody(CoreTemplate::render('emailactivatereminder', array(
                'key' => $key,
                'name' => trim(ucfirst(strtolower($UserObject->getFirstname())))
            )));

        }

        /** update user reminded */
        if (false === $this->UserRepository->updateUnactivatedEmailReminded($emailId)) {
            throw new Exception('Unable to update unactivated email id:' . $emailId);
        }

        /** send the email */
        return $this->EmailService->sendSmtp($EmailObject);

    }
	
}