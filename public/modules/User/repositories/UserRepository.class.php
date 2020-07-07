<?php

/**
 * User repository
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserRepository {
	
	/**
	 * Constants
	 * Keys etc
	 */

	/**
	 * Queries
	 */
	const ACTIVATE_USER_UPDATE_QUERY = " 
		UPDATE 
			`werock_users` 
		SET 
			`werock_user_activated` = NOW() 
		WHERE 
			MD5(CONCAT(:shortsalt, userid)) = :key; 
	";
	const SELECT_USER_USERNAME_PASSWORD_QUERY = " 
		SELECT 
			`werock_user_id`, 
			`werock_user_username`
		FROM 
			`werock_users` 
		WHERE 
			`werock_user_username` = :uniqueid
		AND 
			`werock_user_password` = :password 
		AND 
			`werock_user_password` IS NOT NULL
	";
	const SELECT_USER_EMAIL_PASSWORD_QUERY = "
		SELECT
			`werock_users`.`werock_user_id`,
			`werock_users`.`werock_user_username`,
			`werock_user_emails`.`werock_user_email_value`,
			(`werock_user_email_activated` != '1000-01-01 00:00:00') AS email_active
		FROM
			`werock_users`
		LEFT JOIN
			`werock_user_emails`
		ON
			`werock_users`.`werock_user_id` = `werock_user_emails`.`werock_user_id`
		WHERE
			`werock_user_emails`.`werock_user_email_value` = :uniqueid
		AND
			`werock_user_password` = :password
		AND
			`werock_user_password` IS NOT NULL
	";
	const SELECT_USER_BY_USERNAME_QUERY = "
		SELECT 
			*
		FROM 
			`werock_users`
		LEFT JOIN
			`werock_user_emails`
		ON
			`werock_users`.`werock_user_id` = `werock_user_emails`.`werock_user_id`
		WHERE
			`werock_user_username` = :username
		LIMIT
			1
	";
	const INSERT_USER_QUERY = "
		INSERT INTO 
			`werock_users`
		(
			`werock_user_username`,
			`werock_user_password`,
			`werock_user_date_added`
		) VALUES (
			:username,
			:userpassword,
			NOW()
		)
	";
	const SELECT_USER_BY_ID_QUERY = "
		SELECT 
			*
		FROM 
			`werock_users`
		LEFT JOIN
			`werock_user_emails`
		ON
			`werock_users`.`werock_user_id` = `werock_user_emails`.`werock_user_id`
		WHERE 
			`werock_users`.`werock_user_id` = :userid
		LIMIT
			1
	";
	const SELECT_USER_BY_EMAIL_QUERY = "
		SELECT
			*
		FROM
			`werock_users`
		LEFT JOIN
			`werock_user_emails`
		ON
			`werock_users`.`werock_user_id` = `werock_user_emails`.`werock_user_id`
		WHERE
			`werock_user_emails`.`werock_user_email_value` = :email
		LIMIT
			1
	";
	const INSERT_USER_EMAIL_QUERY = "
		INSERT INTO 
			`werock_user_emails`
		(
			`werock_user_id`,
			`werock_user_email_key`,
			`werock_user_email_value`,
			`werock_user_email_activated`,
			`werock_user_email_date_added`
		) VALUES (
			:userid,
			:useremailkey,
			:useremailvalue,
			'1000-01-01 00:00:00',
			NOW()
		) ON DUPLICATE KEY UPDATE
			`werock_user_email_key` = :useremailkey
	";
	const SELECT_USER_EMAIL_BY_KEY_QUERY = "
		SELECT
			*
		FROM
			`werock_user_emails`
		WHERE
			`werock_user_email_key` = :useremailkey
	";
	const ACTIVATE_USER_EMAIL_QUERY = "
		UPDATE
			`werock_user_emails`
		SET 
			`werock_user_email_activated` = NOW(),
			`werock_user_email_key` = ''
		WHERE 
			`werock_user_email_key` = :useremailkey
	";
	const SELECT_ACTIVATED_USER_EMAIL_QUERY = "
		SELECT 
			*
		FROM 
			`werock_user_emails`
		WHERE 
			`werock_user_email_value` = :useremailvalue
		AND 
			`werock_user_email_key` = ''
	";
	const GET_EMAIL_KEY_VIA_EMAIL_QUERY = "
		SELECT
			`werock_user_email_key`
		FROM
			`werock_user_emails`
	  	WHERE
	  		`werock_user_email_value` = :email
	";
	const GET_EMAIL_ID_VIA_EMAIL_QUERY = "
		SELECT
			`werock_user_email_id`
		FROM
			`werock_user_emails`
	  	WHERE
	  		`werock_user_email_value` = :email
	";
	const SELECT_USER_DATA_QUERY = "
		SELECT 
			`werock_user_data_id`
		FROM 
			`werock_user_data`
		WHERE
			`werock_user_data_key` = :userdatakey
	";
	const INSERT_USER_DATA_QUERY = "
		INSERT INTO 
			`werock_user_data`
		(
			`werock_user_data_key`,
			`werock_user_data_date_added`
		) VALUES (
			:userdatakey,
			NOW()
		)
	";
	const SELECT_USER_VALUE_QUERY = "
		SELECT 
			`werock_user_data_value_id`
		FROM 
			`werock_user_data_values`
		WHERE
			`werock_user_data_id` = :userdataid
		AND 
			`werock_user_id` = :userid
		LIMIT 
			1
	";
	const RESET_USER_PW_GET_KEY_QUERY = "
		SELECT
			`werock_user_pw_reset_key`
		FROM
			`werock_users`
	  	WHERE
	  		`werock_user_id` = :userId
	  	AND
	  		LENGTH(`werock_user_pw_reset_key`) > 0
	  	AND
	  		`werock_user_pw_reset_timestamp` > :oldestResetTimestamp
	";
	const RESET_USER_PW_SET_KEY_QUERY = "
		UPDATE
			`werock_users`
		SET
			`werock_user_pw_reset_key` = :resetKey,
			`werock_user_pw_reset_timestamp` = NOW()
		WHERE
			`werock_user_id` = :userId
	";
	const RESET_USER_PW_RESET_QUERY = "
		UPDATE
			`werock_users`
		SET
			`werock_user_password` = :password,
			`werock_user_pw_reset_key` = NULL
		WHERE
			`werock_user_pw_reset_key` = :resetKey
	";
	const RESET_VALID_KEY_QUERY = "
		SELECT
			COUNT(*)
		FROM
			`werock_users`
		WHERE
			`werock_user_pw_reset_key` = :resetKey
	";
	const INSERT_USER_VALUE_QUERY = "
		INSERT INTO
			`werock_user_data_values`
		(
			`werock_user_data_id`,
			`werock_user_id`,
			`werock_user_data_value_string`,
			`werock_user_data_value_date_added`
		) VALUES (
			:userdataid,
			:userid,
			:uservaluestring,
			NOW()
		)
	";
	const UPDATE_USER_VALUE_QUERY = "
		UPDATE 
			`werock_user_data_values`
		SET
		    `werock_user_data_value_string` = :uservaluestring,
		    `werock_user_data_value_last_modified` = NOW()
		WHERE 
			`werock_user_data_value_id` = :uservalueid
	";
	const SELECT_USER_DATA_VALUE_QUERY = "
		SELECT 
			`werock_user_data_value_string`
		FROM 
			`werock_user_data_values`
		LEFT JOIN
			`werock_user_data` 
		USING 
			( `werock_user_data_id` )
		WHERE 
			`werock_user_data_values`.`werock_user_id` = :userid
		AND 
			`werock_user_data_key` = :userdatakey
		ORDER BY
			`werock_user_data_date_added` DESC
		LIMIT 
			1	
	";
    const SELECT_USERS_SEARCH_QUERY = "
        SELECT
	        `werock_users`.`werock_user_id` as werock_user_id,
            `werock_users`.`werock_user_username` as werock_user_username
        FROM
            `werock_users`
        LEFT JOIN
            `werock_user_emails`
        ON
            `werock_users`.`werock_user_id` = `werock_user_emails`.`werock_user_id`
        WHERE
            `werock_users`.`werock_user_username` LIKE :query
        OR
            `werock_user_emails`.`werock_user_email_value` LIKE :query
        GROUP BY
            `werock_users`.`werock_user_id`
        LIMIT
            :limit OFFSET :start
    ";

	const UPDATE_USER_PASSWORD_SQL = "
		UPDATE
		  	werock_users
		SET
		  	werock_users.werock_user_password = :newPassword
		WHERE
		  	werock_users.werock_user_password = :password
		AND
			werock_users.werock_user_id = :userId
	";

	const SELECT_UNACTIVATED_EMAILS_QUERY = "
		SELECT
			*
		FROM
			werock_user_emails
		LEFT JOIN
			werock_users
		ON
			werock_user_emails.werock_user_id = werock_users.werock_user_id
		WHERE
			werock_user_emails.werock_user_email_id IS NOT NULL
	  	AND
	  		LENGTH(werock_user_emails.werock_user_email_value) > 0
	  	AND
			werock_users.werock_user_id IS NOT NULL
		AND
			werock_users.werock_user_id > 0
		AND
			werock_user_email_activation_reminded != 1
		AND
			werock_user_email_date_added < DATE_ADD(NOW(), INTERVAL -:hours HOUR)
		AND
			werock_user_email_activated = '1000-01-01 00:00:00'
		ORDER BY
			werock_user_email_date_added ASC
		LIMIT
			40
	";

	const UPDATE_UNACTIVATED_EMAIL_REMINDED_QUERY = "
		UPDATE
			werock_user_emails
		SET
			werock_user_email_activation_reminded = 1,
	  		werock_user_email_last_modified = NOW()
		WHERE
			werock_user_email_id = :id
	";

	const UPDATE_USER_ACCESS_TOKEN_QUERY = "
		UPDATE
			werock_users
		SET
			werock_user_access_token = :accessToken
		WHERE
			werock_user_id = :userId
	";

	const SELECT_USER_BY_ACCESS_TOKEN_QUERY = "
		SELECT
			*
		FROM
			werock_users
		WHERE
			werock_user_access_token = :accessToken
	";


	/** @var EmailService $EmailService */
	private $EmailService;

	function __construct(){
		$this->EmailService = CoreLogic::getService('EmailService');
	}

	/**
	 * Select email by activation key
	 *
	 * @param null $activationKey
	 * @return array
	 */
	public function getEmailByActivationKey($activationKey = null){
		$row = CoreSqlUtils::row(self::SELECT_USER_EMAIL_BY_KEY_QUERY, array(
			':useremailkey' => $activationKey
		));
		return isset($row['werock_user_email_value']) ? $row['werock_user_email_value'] : false;
	}

	/**
	 * Update user access token
	 *
	 * @param null $userId
	 * @param null $accessToken
	 * @return bool
	 */
	public function updateUserAccessToken($userId = null, $accessToken = null){
		return CoreSqlUtils::update(self::UPDATE_USER_ACCESS_TOKEN_QUERY, array(
			':userId' => (int) $userId,
			':accessToken' => $accessToken
		));
	}

	/**
	 * Generate access token
	 *
	 * @return String
	 * @throws Exception
	 * @throws UserNoUserSessionException
	 */
	public function generateAccessToken(){
		$userId =  CoreUser::getId();
		if(empty($userId)) throw new UserNoUserSessionException();
		$accessToken = CoreSecUtils::generateKey();
		if(false !== self::updateUserAccessToken($userId, $accessToken)){
			return $accessToken;
		}
		throw new Exception('Unable to generate access token');
	}

	/**
	 * Get User By AccessToken
	 *
	 * @param null $accessToken
	 * @return UserObject
	 * @throws UserNotFoundException
	 */
	public function getUserByAccessToken($accessToken = null){

		/** @var array $row */
		$row = CoreSqlUtils::row(self::SELECT_USER_BY_ACCESS_TOKEN_QUERY, array(
			':accessToken' => $accessToken
		));

		if(!$row){
			throw new UserNotFoundException('Access token invalid');
		}

		/** @var UserObject $UserObject */
		$UserObject = CoreLogic::getObject('UserObject');
		$UserObject->setId($row['werock_user_id']);
		$UserObject->setUsername($row['werock_user_username']);
		$UserObject->setEmail($row['werock_user_email_value']);

		//return user
		return $UserObject;
	}

	/**
	 * Update password
	 *
	 * @param string $currentPassword
	 * @param string $newPassword
	 * @return True
	 */
	public function updatePassword($currentPassword = null, $newPassword = null){
		return CoreSqlUtils::update(self::UPDATE_USER_PASSWORD_SQL, array(
			':userId' => (int) CoreUser::getId(),
			':password' => CoreSecUtils::preparePassword($currentPassword),
			':newPassword' => CoreSecUtils::preparePassword($newPassword)
		));
	}

	/**
	 * Get user by id
	 *
	 * @param null $userId
	 * @return bool|mixed|void
	 * @throws UserNotFoundException
	 */
	public function getUser($userId = null){
		
		//find row in database
		$row = CoreSqlUtils::row(self::SELECT_USER_BY_ID_QUERY, array(':userid' => (int)$userId));

        //no row
		if(!$row){
			throw new UserNotFoundException('User not found by id:' . (int) $userId);
		}

       	/** @var UserObject $UserObject */
		$UserObject = CoreLogic::getObject('UserObject');
		$UserObject->setId($row['werock_user_id']);
		$UserObject->setUsername($row['werock_user_username']);
		$UserObject->setEmail($row['werock_user_email_value']);

		//return user 
		return $UserObject;
		
	}

	/**
	 * Get user reset key
	 *
	 * @param null $userId
	 * @param int $oldestResetTimestap
	 * @return array
	 */
	public function getUserResetKey($userId = null, $oldestResetTimestap = 0){
		$row = CoreSqlUtils::row(self::RESET_USER_PW_GET_KEY_QUERY, array(
			':userId' => (int) $userId,
			':oldestResetTimestamp' => $oldestResetTimestap
		));
		return isset($row['werock_user_pw_reset_key']) ? $row['werock_user_pw_reset_key'] : false;
	}

	/**
	 * Set user reset key
	 *
	 * @param null $userId
	 * @param null $resetKey
	 * @return True
	 */
	public function setUserResetKey($userId = null, $resetKey = null){
		return CoreSqlUtils::update(self::RESET_USER_PW_SET_KEY_QUERY, array(
			':userId' => (int) $userId,
			':resetKey' => $resetKey
		));
	}

	/**
	 * Rest user password
	 *
	 * @param null $resetKey
	 * @param null $password
	 * @return True
	 */
	public function resetUserPassword($resetKey = null, $password = null){
		return CoreSqlUtils::update(self::RESET_USER_PW_RESET_QUERY, array(
			':password' => CoreSecUtils::preparePassword($password),
			':resetKey' => $resetKey
		));
	}

	/**
	 * Get user by reset key
	 *
	 * @param null $resetKey
	 * @return array
	 */
	public function resetKeyValid($resetKey = null){
		return CoreSqlUtils::row(self::RESET_VALID_KEY_QUERY, array(
			':resetKey' => $resetKey
		));
	}

    /**
     * Search for users
     *
     * @param UserSearchObject $UserSearchObject
     * @return array|null
     */
    public function getUsers(UserSearchObject $UserSearchObject){

        //run query
        $rows = CoreSqlUtils::rows(self::SELECT_USERS_SEARCH_QUERY, array(
            ':query' => $UserSearchObject->getQuery() . '%',
            ':start' => (int)$UserSearchObject->getStart(),
            ':limit' => (int)$UserSearchObject->getLimit()
        ));

        //check sql results
        if(empty($rows)){
            return null;
        }

        //holder for users
        $users = array();

        //build return
        foreach($rows as $row){

            //user object
            $UserObject = CoreLogic::getObject('UserObject');
            $UserObject->setId($row['werock_user_id']);
            $UserObject->setUsername($row['werock_user_username']);

            //user object
            array_push($users, $UserObject);

        }

        //return users
        return $users;

    }

	/**
	 * Authenticate existing user
	 *
	 * @param UserAuthenticationObject $UserAuthenticationObject
	 * @return bool|mixed|void
	 * @throws UserNotFoundException
	 * @throws UserEmailNotActivatedException
	 */
	public function authenticate(UserAuthenticationObject $UserAuthenticationObject){

		/**
		 * Lookup using email or
		 */
		$email = $UserAuthenticationObject->getEmail();
		$sql = (!empty($email)) ? self::SELECT_USER_EMAIL_PASSWORD_QUERY : self::SELECT_USER_USERNAME_PASSWORD_QUERY;

		//get user row with credentials
		$row = CoreSqlUtils::row($sql, array(
			':uniqueid' => (!empty($email)) ? $UserAuthenticationObject->getEmail() : $UserAuthenticationObject->getUsername(),
			':password' => CoreSecUtils::preparePassword($UserAuthenticationObject->getPassword())
		));

		/**
		 * Unable to find user
		 */
		if(empty($row)){
            throw new UserNotFoundException();
		}

		/**
		 * Throw Exception when email is not yet activated
		 */
		if(UserModule::$requireUserEmailActive && isset($row['email_active']) && $row['email_active'] != 1){
			throw new UserEmailNotActivatedException();
		}

		/** @var UserObject $UserObject */
		$UserObject = CoreLogic::getObject('UserObject');
		$UserObject->setId($row['werock_user_id']);
		$UserObject->setUsername($row['werock_user_username']);
		$UserObject->setEmail(isset($row['werock_user_email_value']) ? $row['werock_user_email_value'] : null);
				
		//return user 
		return $UserObject;
		
	}

	/**
	 * Assign email
	 *
	 * @param UserObject $UserObject
	 * @param String $email
	 * @return UserObject Returns user
	 */	
	public function assignEmail(UserObject $UserObject, $email = null){
		
		//see if email has already been used and activated
		if(!self::activatedEmail($email)){
			
			//generateKey
			$generatedKey = CoreSecUtils::generateKey();
			
			//attempt to insert user email
			$inserted = CoreSqlUtils::insert(self::INSERT_USER_EMAIL_QUERY, array(
				':userid' => $UserObject->getId(),
				':useremailvalue' => $email,
				':useremailkey' => $generatedKey
			));
			
			//check for success
			if(false !== $inserted){

                //create addressee
                $EmailAddresseeObject = CoreLogic::getObject('EmailAddresseeObject');
                $EmailAddresseeObject->setName($UserObject->getFirstname());
                $EmailAddresseeObject->setEmail($email);

                //create sent from
                $SentFromObject = CoreLogic::getObject('EmailAddresseeObject');
                $SentFromObject->setName(SITE_NAME);
                $SentFromObject->setEmail(SITE_EMAIL);

                /**
                 * Create email html
                 */
                $emailHtml = CoreTemplate::render('emailactivate', array(
                    'key' => $generatedKey,
					'name' => ucfirst($UserObject->getFirstname())
                ));

                //email object
                $EmailObject = CoreLogic::getObject('EmailObject');
                $EmailObject->setSubject('Confirm Email');
                $EmailObject->setSentfrom($SentFromObject);
                $EmailObject->addAddressee($EmailAddresseeObject);
                $EmailObject->setHtmlBody($emailHtml);
                $EmailObject->setTextBody(strip_tags($emailHtml));

                //send the email
				$this->EmailService->sendSmtp($EmailObject);

				//return 
				return true;
					
			}else{

				//unable to assign email
				CoreLog::error('Unable to insert user email: ' . $email);
				
				//unable to insert
				return false;			
				
			}
			
		}
					
		//throw notification
		CoreNotification::set('This email already belongs to another user.', CoreNotification::ERROR);		
		
		//something went wrong
		return false;
		
	}

	/**
	 * Get user by email
	 *
	 * @param null $email
	 * @return UserObject
	 * @throws UserNotFoundException
	 */
	public function getUserByEmail($email = null){
		if(false !== ($row = CoreSqlUtils::row(self::SELECT_USER_BY_EMAIL_QUERY, array(
			':email' => $email
		)))){
			/** @var UserObject $UserObject */
			$UserObject = CoreLogic::getObject('UserObject');
			$UserObject->setId($row['werock_user_id']);
			$UserObject->setUsername($row['werock_user_username']);
			$UserObject->setEmail($email);
			return $UserObject;
		}
		throw new UserNotFoundException();
	}

	/**
	 * Check for for existing email
	 *
	 * @param String $email
	 * @return UserObject Returns user
	 */	
	public function activatedEmail($email = null){
		
		//get rows
		$rows = CoreSqlUtils::rows(self::SELECT_ACTIVATED_USER_EMAIL_QUERY, array(
			':useremailvalue' => $email
		));
		
		//see if this email has already been taken
		return !empty($rows);
		
	}

	/**
	 * Login user by email
	 *
	 * @param null $email
	 * @throws UserNotFoundException
	 */
	private function loginUserByEmail($email = null){

		//TODO: Consolidate this with the UserService::setUserSession method

		//Get UserObject by email
		$UserObject = self::getUserByEmail($email);

		//Populate first and last name
		$UserObject->setFirstname(self::getData(UserProcedure::DATA_KEY_FIRST_NAME, $UserObject));
		$UserObject->setLastname(self::getData(UserProcedure::DATA_KEY_LAST_NAME, $UserObject));

		//Set session
		$_SESSION[UserService::USER_SESSION_KEY] = $UserObject->getId();

		//Set last login timestamp TODO: set in central location
		self::setData('last login', time(), $UserObject);

		//Set UserObject to static user pointer
		CoreUser::setUser($UserObject);

		//Dispatch any - login success events
		CoreObserver::dispatch(UserModule::USER_EVENT_LOGIN_SUCCESS, $this);

		//TODO: Consolidate this with the UserService::setUserSession method

	}

	/**
	 * Activate email
	 *
	 * @param null $key
	 * @return bool
	 * @throws UserEmailActivationFailedException
	 */
	public function activateEmail($key = null){

		/** @var string $email */
		$email = self::getEmailByActivationKey($key);

		if(!empty($email) && false !== CoreSqlUtils::update(self::ACTIVATE_USER_EMAIL_QUERY, array(
			':useremailkey' => $key
		))){

			/**
			 * Try to send magic link - but don't fail if we run into an issue
			 *
			 */
			try {

				//Login user via email
				self::loginUserByEmail($email);

				//Send magic link
				self::sendMagicLink($email);

			} catch(Exception $e){
				CoreLog::error('Unable to send magic link to ' . $email . ' Message:' . $e->getMessage());
			}

			//Core notifications
			CoreNotification::set('Email has been activated', CoreNotification::SUCCESS);

			//return true
			return true;
			
		}

        /**
         * Unable to activate email
         */
        throw new UserEmailActivationFailedException();

	}

	/**
	 * Send magic link
	 *
	 * @param null $email
	 * @throws Exception
	 * @throws UserNoUserSessionException
	 */
	public function sendMagicLink($email = null){

		$UserObject = CoreUser::getUser();

		/**
		 * Generate access token
		 * send to user - with permanent login link
		 */
		$accessToken = self::generateAccessToken();
		CoreLog::info('Generated accessToken length:' . strlen($accessToken));
		if(!empty($accessToken) && !empty($UserObject)){

			CoreLog::info('Sending magic link email');

			//create addressee
			$EmailAddresseeObject = CoreLogic::getObject('EmailAddresseeObject');
			$EmailAddresseeObject->setName($UserObject->getFirstname());
			$EmailAddresseeObject->setEmail($email);

			//create sent from
			$SentFromObject = CoreLogic::getObject('EmailAddresseeObject');
			$SentFromObject->setName(SITE_NAME);
			$SentFromObject->setEmail(SITE_EMAIL);

			/**
			 * Create email html
			 */
			$emailHtml = CoreTemplate::render('magiclinkemail', array(
				'accessToken' => $accessToken,
				'name' => ucfirst($UserObject->getFirstname())
			));

			//email object
			$EmailObject = CoreLogic::getObject('EmailObject');
			$EmailObject->setSubject('Magic Link');
			$EmailObject->setSentfrom($SentFromObject);
			$EmailObject->addAddressee($EmailAddresseeObject);
			$EmailObject->setHtmlBody($emailHtml);
			$EmailObject->setTextBody(strip_tags($emailHtml));

			//send the email
			$this->EmailService->sendSmtp($EmailObject);

			return;

		}

		CoreLog::error('Unable to send magic link email');

	}

	/**
	 * Create a user
	 *
	 * @param UserTemplateObject $UserTemplateObject
	 * @return bool|UserObject
	 * @throws Exception
	 * @throws UserEmailAlreadyClaimedException
	 * @throws UsernameAlreadyClaimedException
	 */
	public function create(UserTemplateObject $UserTemplateObject){
		if(!$UserTemplateObject){
			CoreLog::error('No UserTemplateObject');
			return false;
		}
		if(self::usernameExists($UserTemplateObject)){
            throw new UsernameAlreadyClaimedException();
		}
		if(empty($UserTemplateObject->getEmail())){
			throw new Exception('Need email');
		}
		if(empty($UserTemplateObject->getUsername())){
			throw new Exception('Need username');
		}
		if(empty($UserTemplateObject->getFirstName())){
			throw new Exception('Need first name');
		}
		if(self::activatedEmail($UserTemplateObject->getEmail())){
            throw new UserEmailAlreadyClaimedException();
		}

		//attempt to insert user
		$userId = CoreSqlUtils::insert(self::INSERT_USER_QUERY, array(
			':username' => $UserTemplateObject->getUserName(),
			':userpassword' => CoreSecUtils::preparePassword($UserTemplateObject->getPassword())
		));

		//cleanup first name and last name
		$UserTemplateObject->setFirstName(trim(ucfirst($UserTemplateObject->getFirstName())));
		$UserTemplateObject->setLastName(trim(ucfirst($UserTemplateObject->getLastName())));
		
		//check user id
		if($userId > 0){
			
			/** @var UserObject $UserObject */
			$UserObject = CoreLogic::getObject('UserObject');
			$UserObject->setId((int)$userId);
			$UserObject->setUserName($UserTemplateObject->getUserName());
			$UserObject->setEmail($UserTemplateObject->getEmail());
			$UserObject->setFirstname($UserTemplateObject->getFirstName());
			$UserObject->setLastname($UserTemplateObject->getLastName());
							
			//handle email
			self::assignEmail($UserObject, $UserTemplateObject->getEmail());

			//return the user
			return $UserObject;
		
		}
		
		//User created
		CoreNotification::set('User creation failed!');		
		
		//something went wrong
		return false;
		
	}

	/**
	 * Return row by username
	 *
	 * @param null $username
	 * @return array
	 */
	public function getUserByUsername($username = null){
		return CoreSqlUtils::row(self::SELECT_USER_BY_USERNAME_QUERY, array(
			':username' => $username
		));
	}
	
	/**
	 * Check username
	 *
	 * @param UserTemplateObject $UserTemplateObject
	 * @return Boolean Returns true or false
	 */	
	public function usernameExists($UserTemplateObject = null){
		
		//get user row by username
		$userrow = CoreSqlUtils::rows(self::SELECT_USER_BY_USERNAME_QUERY, array(
			':username' => $UserTemplateObject->getUsername()
		));
		
		//true when not empty
		return !empty($userrow);
		
	}

	/**
	 * Set data
	 *
	 * @param String $key
	 * @param String $value
	 * @param UserObject $UserObject
	 * @return Boolean Returns true or false
	 */	
	public function setData($key = null, $value = null, $UserObject = null){

        //begin transaction
        CoreSqlUtils::beginTransaction();

        try {

            //check valid user object
            if(!$UserObject){
                CoreLog::error('Unable to set data for user without UserObject');
                return false;
            }

            //get the row
            $datarow = CoreSqlUtils::row(self::SELECT_USER_DATA_QUERY, array(
                ':userdatakey' => $key
            ));

            //or create the reference
            if(!isset($datarow['werock_user_data_id'])){
                $datarow['werock_user_data_id'] = CoreSqlUtils::insert(self::INSERT_USER_DATA_QUERY, array(
                    ':userdatakey' => $key
                ));
            }

            //check for data row id
            if(empty($datarow['werock_user_data_id'])){
                CoreLog::error('Unable to create user data reference');
                return false;
            }

            //get value
            $valuerow = CoreSqlUtils::row(self::SELECT_USER_VALUE_QUERY, array(
                ':userdataid' => (int) $datarow['werock_user_data_id'],
                ':userid' => $UserObject->getId()
            ));

            //or insert
            if(!isset($valuerow['werock_user_data_value_id'])){

                //insert value
                CoreSqlUtils::insert(self::INSERT_USER_VALUE_QUERY, array(
                    ':userdataid' => (int) $datarow['werock_user_data_id'],
                    ':uservaluestring' => $value,
                    ':userid' => $UserObject->getId()
                ));

            }else{

                //also update
                CoreSqlUtils::update(self::UPDATE_USER_VALUE_QUERY, array(
                    ':uservalueid' => $valuerow['werock_user_data_value_id'],
                    ':uservaluestring' => $value
                ));

            }

            /**
             * Commit database updates
             */
            CoreSqlUtils::commitTransaction();

        } catch(Exception $e){

            /**
             * Rollback transaction
             */
            CoreSqlUtils::rollbackTransaction();

            /**
             * Rethrow error
             */
            CoreLog::error('An exception ocurred. Info: ' . $e->getMessage());

        }

		//all good
		return true;
		
	}
	
	/**
	 * Get data
	 *
	 * @param String $key
	 * @param UserObject $UserObject
	 * @return Mixed Returns String or false
	 */	
	public function getData($key = null, $UserObject = null){
		
		//need user object
		if(!$UserObject){
			return false;
		}
		
		//get user data row
		$userdatarow = CoreSqlUtils::row(self::SELECT_USER_DATA_VALUE_QUERY, array(
			':userid' => $UserObject->getId(),
			':userdatakey' => $key
		));
		
		//return value or false
		return isset($userdatarow['werock_user_data_value_string']) ? $userdatarow['werock_user_data_value_string'] : false;
		
	}

	/**
	 * Get unactivated emails older than
	 *
	 * @param int $hours
	 * @return array
	 */
	public function getUnactivatedEmailsOlderThan($hours = 2){
		return CoreSqlUtils::rows(self::SELECT_UNACTIVATED_EMAILS_QUERY, array(
			':hours' => (int) $hours
		));
	}

	/**
	 * Update unactivated email reminded
	 *
	 * @param null $id
	 * @return Boolean
	 */
	public function updateUnactivatedEmailReminded($id = null){
		return CoreSqlUtils::update(self::UPDATE_UNACTIVATED_EMAIL_REMINDED_QUERY, array(
			':id' => (int) $id
		));
	}

	/**
	 * Get activation key via email
	 *
	 * @param null $email
	 * @return array
	 */
	public function getActivationKeyByEmail($email = null){
		return CoreSqlUtils::row(self::GET_EMAIL_KEY_VIA_EMAIL_QUERY, array(
			':email' => $email
		));
	}

	/**
	 * Get email id via email
	 *
	 * @param null $email
	 * @return array
	 */
	public function getEmailIdByEmail($email = null){
		return CoreSqlUtils::row(self::GET_EMAIL_ID_VIA_EMAIL_QUERY, array(
			':email' => $email
		));
	}
		
}