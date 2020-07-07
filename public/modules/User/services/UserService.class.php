<?php

/**
 * User service
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserService implements UserServiceInterface {

	use CoreInterceptorTrait;
	use ClassReflectionTrait;
	
	/**
	 * User Keys
	 */
	const USER_SESSION_KEY = 'werock_user';
	
	/**
	 * Other Configuration
	 */
	const SHORT_SALT = 'Lkd*()8jf9!8';
	
	/**
	 * Current User
	 */
	public $currentUser = null;

	/** @var UserProcedure $UserProcedure */
	private $UserProcedure;

	function __construct(){
		$this->UserProcedure = CoreLogic::getProcedure('UserProcedure');
	}

    /**
     * Get User by ID
     *
     * @param null $userId
     * @return UserObject
     */
    public function _getUser($userId = null){
        try {
            return $this->UserProcedure->getUser($userId);
        } catch (UserUnauthorizedException $e){
            CoreNotification::set(CoreLanguage::get('notification.unauthorized.mssg'), CoreNotification::ERROR);
        } catch (UserNotFoundException $e){
            CoreNotification::set(CoreLanguage::get('notification.user.not.found.mssg'), CoreNotification::ERROR);
        } catch (Exception $e){
            CoreNotification::set(CoreLanguage::get('notification.error.mssg'), CoreNotification::ERROR);
        }
        return false;
    }

	/**
	 * Request password reset email
	 *
	 * @param bool $email
	 * @return bool
	 */
	public function requestPasswordReset($email = false){
		try {
			$this->UserProcedure->requestPasswordReset($email);
			CoreNotification::set('Reset link sent', CoreNotification::SUCCESS);
			return true;
		} catch(UserEmailNotActivatedException $e){
			CoreNotification::set('Please first activate your email.', CoreNotification::ERROR);
			try {
				/** @var UserObject $UserObject */
				$UserObject = $this->UserProcedure->getUserByEmail($email);
				$this->UserProcedure->sendEmailActivationReminder($UserObject);
				CoreNotification::set('Confirmation email resent!', CoreNotification::SUCCESS);
			} catch(Exception $e){
				//ignore
			}
		} catch(UserNotFoundException $e){
			CoreNotification::set('No user found at this address', CoreNotification::ERROR);
		} catch(Exception $e){
			CoreNotification::set('Unable to reset password due to error. Info: ' . $e->getMessage());
		}
		return false;
	}

	/**
	 * Is this a valid reset key
	 *
	 * @param null $key
	 * @return bool
	 */
	public function resetKeyValid($key = null){
		try {
			return $this->UserProcedure->resetKeyValid($key);
		} catch(Exception $e){
			CoreNotification::set($e->getMessage(), CoreNotification::ERROR);
		}
		return false;
	}

	/**
	 * Use key provided in email to reset a users password
	 *
	 * @param null $key
	 * @param null $password
	 * @return bool
	 */
	public function resetPassword($key = null, $password = null){
		try {
			$this->UserProcedure->resetPassword($key, $password);
			CoreNotification::set('Password updated! Please login with your new password.', CoreNotification::SUCCESS);
			return true;
		} catch(UserPasswordResetKeyInvalidException $e){
			CoreNotification::set('Invalid reset key!', CoreNotification::ERROR);
		} catch(Exception $e){
			CoreNotification::set('Unable to reset password due to error: Info: ' . $e->getMessage(), CoreNotification::ERROR);
		}
		return false;
	}

	/**
	 * Update password
	 *
	 * @param $currentPassword
	 * @param $newPassword
	 * @return bool
	 */
	public function updatePassword($currentPassword, $newPassword){
		try {
			$this->UserProcedure->updatePassword($currentPassword, $newPassword);
			CoreNotification::set('Password Updated', CoreNotification::SUCCESS);
			return true;
		} catch(UserUnauthorizedException $e){
			CoreNotification::set('You have to be logged in to update your password', CoreNotification::ERROR);
		} catch(Exception $e){
			CoreNotification::set('Unable to update password! Info: ' . $e->getMessage(), CoreNotification::ERROR);
		}
		return false;
	}

	/**
	 * Login user using access token
	 *
	 * @param null $accessToken
	 * @return bool
	 */
	public function loginWithAccessToken($accessToken = null){
		try {
			$UserObject = $this->UserProcedure->getUserByAccessToken($accessToken);
			self::setUserSession($UserObject);
			return true;
		} catch(UserNotFoundException $e){
			CoreNotification::set('Unable to log user in using access token. Info: ' . $e->getMessage());
		}
		return false;
	}

	/**
	 * Activate email
	 *
	 * @param UserObject $userObject
	 * @return mixed
	 */
	public function activatedEmail(UserObject $userObject){
		try {
			return $this->UserProcedure->activatedEmail($userObject);
		} catch (Exception $e){
			CoreNotification::set('An error occurred when checking if users email has been activated. Info: ' . $e->getMessage());
		}
		return false;
	}

	/**
	 * Get user by email
	 *
	 * @param null $email
	 * @return bool|UserObject
	 */
	public function getUserByEmail($email = null){
		try {
			return $this->UserProcedure->getUserByEmail($email);
		} catch (UserNotFoundException $e){
			return false;
		} catch (Exception $e){
			CoreNotification::set('An error occurred when getting user by email: ' . $email . ' Info: ' . $e->getMessage());
		}
	}

    /**
     * Get Users
     *
     * @return array
     */
    public function _getUsers(UserSearchObject $UserSearchObject){

        /**
         * Attempt to get users
         */
        try {

            //get current user
            $UserObject = self::getCurrentUser();

            //throw unauthorized exception
            if(!$UserObject){
                throw new UserUnauthorizedException();
            }

            /**
             * @var array(UserObject) $users
             */
            $users = $this->UserProcedure->getUsers($UserSearchObject);

            //return users
            return $users;

        } catch(UserUnauthorizedException $e){

            //set notification
            CoreNotification::set(CoreLanguage::get('notification.unauthorized.mssg'), CoreNotification::ERROR);

        } catch (Exception $e){

            //set notification
            CoreNotification::set(CoreLanguage::get('notification.error.mssg'), CoreNotification::ERROR);

        }

        //none found
        return false;

    }

    /**
	 * Set current user
	 *
	 * @return UserObject
	 */
	public function setCurrentUser(){
		
		/** @var UserObject $UserObject */
		$UserObject = self::getCurrentUser();

		/** Assertion */
		if(empty($UserObject) || !get_class($UserObject) == UserObject::class){
			return false;
		}

		/** Set user in application context */
		CoreUser::setAnonymous(false);
		CoreUser::setId($UserObject->getId());
		CoreUser::setUser($UserObject);
		
		//set current user
		return $this->currentUser = $UserObject;
		
	}
	
	/**
	 * Get user session
	 * @return UserObject
	 */
	public function _getCurrentUser(){

		//assure there is a session
		CoreSessionUtils::assureSession();		

		//check if session key exists
		if(!isset($_SESSION[self::USER_SESSION_KEY])){
			return false;
		}

        //return current user if lookup already happened
        if(UserObject::class == get_class($this->currentUser)){
            return $this->currentUser;
        }

        //or do the lookup
        try {

            //find existing user
            $UserObject = $this->UserProcedure->getUser((int) $_SESSION[self::USER_SESSION_KEY]);

            //return user session
            return $UserObject;

        } catch(UserNotFoundException $e){

            //set notification
            CoreNotification::set('Unable to find user', CoreNotification::ERROR);

        }

        //dispatch listeners
        CoreObserver::dispatch(UserModule::USER_EVENT_CURRENT_USER_NOT_FOUND, null);

        //return false
        return false;

	}
	
	/**
	 * Logout now
	 */
	public function logout(){

		/** Assure session started */
		CoreSessionUtils::assureSession();		

		/** Unset user session */
		if(isset($_SESSION[self::USER_SESSION_KEY])) unset($_SESSION[self::USER_SESSION_KEY]);

		/** Dispatch listeners */
		CoreObserver::dispatch(UserModule::USER_EVENT_LOGOUT, null);

		return true;
			
	}
	
	/**
	 * Check if a user is currently logged in
	 * @return bool Is a user logged in or not
	 */
	public function _activeUser(){
		
		//assure there is a session
		CoreSessionUtils::assureSession();
				
		//check && return
		return (isset($_SESSION[self::USER_SESSION_KEY]) && !empty($_SESSION[self::USER_SESSION_KEY]));
	
	}

	/**
	 * WARNING: Authenticates as provided user
	 *
	 * @param UserObject $userObject
	 * @return bool|UserObject
	 */
	public function _authenticateUser(UserObject $userObject){

		try {

			CoreSessionUtils::assureSession();

			/** Capture remember me */
			$this->UserProcedure->captureRememberMe($userObject);

			self::setUserSession($userObject);

			return true;

		} catch(Exception $e){
			CoreNotification::set('Unable to authenticate as user due to error. Info: ' . $e->getMessage(), CoreNotification::ERROR);
		}

		$UserAuthenticationObject = new UserAuthenticationObject();
		$UserAuthenticationObject->setEmail($userObject->getEmail());
		$UserAuthenticationObject->setUsername($userObject->getUsername());

		//dispatch listeners
		CoreObserver::dispatch(UserModule::USER_EVENT_LOGIN_FAILED, $UserAuthenticationObject);

		return false;

	}

	/**
	 * Set user session
	 *
	 * @param UserObject $UserObject
	 * @return UserObject
	 */
	private function setUserSession(UserObject $UserObject){

		if(!$UserObject) CoreLog::error('No UserObject!');

		//set current user reference
		$this->currentUser = $UserObject;

		//set user id in session
		$_SESSION[self::USER_SESSION_KEY] = $UserObject->getId();

		/**
		 * Save last login time
		 */
		$this->UserProcedure->setData('last login', time(), $UserObject);

		//all good
		CoreObserver::dispatch(UserModule::USER_EVENT_LOGIN_SUCCESS, $UserObject);

		//return user
		return $UserObject;

	}

	/**
	 * Find existing user
	 * 
	 * @param UserAuthenticationObject $UserAuthenticationObject
	 * @return bool Returns success
	 */
	public function _authenticate($UserAuthenticationObject = null){

        try {

            //assure there is a session
            CoreSessionUtils::assureSession();

            $UserObject = $this->UserProcedure->authenticate($UserAuthenticationObject);

            //check for success
            if(false !== $UserObject){

                //return user
                return self::setUserSession($UserObject);

            }

        } catch(UserNotFoundException $e) {
			CoreNotification::set(CoreLanguage::get('notification.login.failed.mssg'), CoreNotification::ERROR);
		} catch(UserEmailNotActivatedException $e){
			CoreNotification::set(CoreLanguage::get('notification.email.not.activated.mssg'), CoreNotification::ERROR);
			try {
				/** @var UserObject $UserObject */
				$UserObject = $this->UserProcedure->getUserByEmail($UserAuthenticationObject->getEmail());
				$this->UserProcedure->sendEmailActivationReminder($UserObject);
				CoreNotification::set('Confirmation email resent!', CoreNotification::SUCCESS);
			} catch(Exception $e){
				CoreLog::debug('Unable to resend confirmation email. Info: ' . $e->getMessage());
			}
		} catch(Exception $e){
            CoreNotification::set('Unable to authenticate user due to error. Info: ' . $e->getMessage(), CoreNotification::ERROR);
        }

		//dispatch listeners
		CoreObserver::dispatch(UserModule::USER_EVENT_LOGIN_FAILED, $UserAuthenticationObject);
		
		//return false		
		return false;

	}

	/**
	 * Create user
	 *
	 * @param UserTemplateObject $UserTemplateObject
	 * @return UserObject Returns user
	 */
	public function _create($UserTemplateObject = null){

        //assure there is a session
        CoreSessionUtils::assureSession();

        //begin transaction
        CoreSqlUtils::beginTransaction();

        //attempt login
        try {

            //pass on to procedure
            $UserObject = $this->UserProcedure->create($UserTemplateObject);

            //check for success
            if(false !== $UserObject){

				//set current user reference
				$this->currentUser = $UserObject;

				//set user id in session
                $_SESSION[self::USER_SESSION_KEY] = $UserObject->getId();

				//store first name
				$this->setData('firstname', $UserTemplateObject->getFirstName(), $UserObject);

				//store last name
				$this->setData('lastname', $UserTemplateObject->getLastName(), $UserObject);

                //commit
                CoreSqlUtils::commitTransaction();

				//populate request scoped user reference
				CoreUser::setUser($UserObject);
				CoreUser::setId($UserObject->getId());

				//all good
				CoreObserver::dispatch(UserModule::USER_EVENT_CREATE_SUCCESS, $UserObject);

                //return user
                return $UserObject;

            }
		
		} catch (UserUnauthorizedException $e){
			
			//set core notification
			CoreNotification::set('Unauthorized request');
			
        } catch (UsernameAlreadyClaimedException $e){

            //set notification
            CoreNotification::set('Username: ' . $UserTemplateObject->getUsername() . ' already belongs to an account', CoreNotification::ERROR);

        } catch (UserEmailAlreadyClaimedException $e){

            //set notification
            CoreNotification::set('Email: ' . $UserTemplateObject->getEmail() . ' already belongs to an account', CoreNotification::ERROR);

        } catch (Exception $e){

            //error occurred
            CoreNotification::set('An error occurred when attempting to create a user', CoreNotification::ERROR);

        }

        //rollback
        CoreSqlUtils::rollbackTransaction();

		//dispatch listeners
		CoreObserver::dispatch(UserModule::USER_EVENT_CREATE_FAILED, null);
		
		//return false		
		return false;

	}

	/**
	 * Get user by username
	 *
	 * @param null $username
	 * @return bool|UserObject
	 */
	public function getUserByUsername($username = null){
		try {
			return $this->UserProcedure->getUserByUsername($username);
		} catch (UserNotFoundException $e){
			CoreNotification::set('User could not be found for username: ' . $username, CoreNotification::ERROR);
		} catch (Exception $e){
			CoreNotification::set('An unknown error occurred. Info: ' . $e->getMessage(), CoreNotification::ERROR);
		}
		return false;
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

        //assertion
        if(!$UserObject){
            return false;
        }

        //set data
        return $this->UserProcedure->setData($key, $value, $UserObject);

    }

    /**
     * Get User Data
     *
     * @param null $key
     * @param UserObject $UserObject
     * @return mixed
     */
    public function getData($key = null, $UserObject = null){

        //assertion
        if(!$UserObject){
            return false;
        }

        //get data
        return $this->UserProcedure->getData($key, $UserObject);

    }

    /**
     * Activate email
     *
     * @param String $key
     * @return UserObject Returns user
     */
    public function activateEmail($key = null){

        /**
         * Try to activate email
         */
        try {

            /**
             * Return result from DAO
             */
            if(true === $this->UserProcedure->activateEmail($key)){

                /**
                 * Set notification
                 */
                CoreNotification::set('Email has been successfully activated');

                return true;

            }

        } catch(UserEmailActivationFailedException $e){

            /**
             * Set notification
             */
            CoreNotification::set('Email confirmation failed. Already activated? Try <a href="/login">logging in</a>.', CoreNotification::ERROR);

        } catch(UserNoUserSessionException $e){

            /**
             * Set notification
             */
            CoreNotification::set('Please login and retry. We need a valid user session');

        } catch(Exception $e){

            /**
             * Unexpected error
             */
            CoreNotification::set('An error occurred when trying to activate this email');

        }

        return false;

    }

	/**
	 * Resend activation email
	 *
	 * @param UserObject $userObject Optional - defaults to current user when null
	 * @return bool
	 */
	public function resendActivationEmail($userObject = null){
		try {
			if(empty($userObject) || get_class($userObject) != UserObject::class){
				$userObject = $this->getCurrentUser();
			}
			$sent = $this->UserProcedure->sendEmailActivationReminder($userObject);
			if(!$sent) throw new Exception();
			CoreNotification::set('Confirmation email resent. Please check your email!', CoreNotification::SUCCESS);
			return true;
		} catch(Exception $e){
			CoreNotification::set('Unable to resend confirmation email. Info: ' . $e->getMessage(), CoreNotification::ERROR);
		}
	}

	/**
	 * Send activate email reminder
	 *
	 * @return bool
	 */
	public function sendEmailActivationReminders(){
		try {
			return $this->UserProcedure->sendEmailActivationReminders();
		} catch(Exception $e){
			CoreLog::error($e->getMessage());
		}
		return false;
	}

}