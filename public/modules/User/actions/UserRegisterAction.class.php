<?php 

/**
 * User Registration Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserRegisterAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

	public $title = 'Register';

	public $description = '';

	/**
	 * Set main template
	 */
	public $template = "userregister";
	
	/**
	 * Define register fields
	 */
	public $fields = array();

	/**
	 * Get menu's for this action
	 *
	 * @return array
	 */
	public function getMenus()
	{

	}

	/**
	 * Get routes for this action
	 *
	 * @return array
	 */
	public function getRoutes()
	{

		$routes = array();

		array_push($routes, CoreControllerObject::buildAction('/register', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));

		return $routes;

	}

	/**
     * Register action
     *
     * @return mixed|void
     */
    public function register(){

    }

	/**
	 * Catch params
	 */
	public function build($params = array()){

		//redirect
		if(CoreLogic::getService('UserService')->activeUser()){
			CoreHeaders::setRedirect('/');
		}

		/**
		 * Define register form fields
		 */
		 $this->fields[] = array(
			'name' => 'first_name',
			'label' => CoreLanguage::get('form:label:firstname'),
			'type' => 'forminputtext',
			'template' => 'formfieldflexible',
			'condition' => '*',
			'placeholder' => CoreLanguage::get('form:label:firstname:placeholder'),
			'helper' => CoreLanguage::get('form:label:firstname:helper'),
			'value' => null
		);
		$this->fields[] = array(
			'name' => 'last_name',
			'label' => CoreLanguage::get('form:label:lastname'),
			'type' => 'forminputtext',
			'template' => 'formfieldflexible',
			'condition' => '*',
			'placeholder' => CoreLanguage::get('form:label:lastname:placeholder'),
			'helper' => CoreLanguage::get('form:label:lastname:helper'),
			'value' => null
		);
		$this->fields[] = array(
			'name' => 'username',
			'label' => CoreLanguage::get('form:label:username'),
			'type' => 'forminputnewusername',
			'template' => 'formfieldflexible',
			'condition' => '/^[a-z0-9\\-\\_\\.]{4,30}$/i',
			'placeholder' => CoreLanguage::get('form:label:username:placeholder'),
			'helper' => CoreLanguage::get('form:label:username:helper'),
			'value' => null
		);
		$this->fields[] = array(
			'name' => 'email',
			'label' => CoreLanguage::get('form:label:email'),
			'type' => 'forminputnewemail',
			'template' => 'formfieldflexible',
			'condition' => 'email',
			'placeholder' => CoreLanguage::get('form:label:email:placeholder'),
			'helper' => CoreLanguage::get('form:label:email:helper'),
			'value' => null
		);
		$this->fields[] = array(
			'name' => 'password',
			'label' => CoreLanguage::get('form:label:password'),
			'type' => 'forminputpassword',
			'template' => 'formfieldflexible',
			'condition' => 'password',
			'helper' => CoreLanguage::get('form:label:password:helper'),
			'placeholder' => '',
			'value' => null
		);
		$this->fields[] = array(
			'name' => 'password_repeat',
			'label' => CoreLanguage::get('form:label:passwordrepeat'),
			'type' => 'forminputpassword',
			'template' => 'formfieldflexible',
			'condition' => 'password',
			'helper' => CoreLanguage::get('form:label:passwordrepeat:helper'),
			'placeholder' => '',
			'value' => null
		);
		$this->fields[] = array(
			'name' => 'altcaptcha',
			'label' => '',
			'type' => 'forminputaltcaptcha',
			'template' => 'formfieldnaked',
			'condition' => '*',
			'helper' => '',
			'placeholder' => '',
			'value' => ''
		);
		$this->fields[] = array(
			'name' => 'login_link',
			'label' => '',
			'href' => '/login',
			'type' => 'forminputlink',
			'template' => 'formfieldflexible',
			'placeholder' => '',
			'value' => CoreLanguage::get('form:link:login')
		);
		$this->fields[] = array(
			'name' => 'register_submit',
			'label' => '',
			'type' => 'forminputsubmit',
			'template' => 'formfieldflexible',
			'placeholder' => '',
			'value' => CoreLanguage::get('form:link:register')
		);

		/**
		 * Set form data
		 */
		CoreForm::register('register', array('method' => 'post', 'action' => ''), $this->fields);
		
		/**
		 * Set template
		 */
		self::setTemplate($this->template);

		//handle submission
		if(CoreForm::getForm('register')->validFormSubmitted()){

			//track user data
			CoreVisitor::setData('attempted:registration', 1);
			
			//validate submission
			if(CoreForm::getForm('register')->validateSubmission()){
				
				/** @var UserTemplateObject $UserTemplateObject */
				$UserTemplateObject = CoreLogic::getObject('UserTemplateObject');
				$UserTemplateObject->setUsername(CoreForm::getForm('register')->grabFieldValue('username'));
				$UserTemplateObject->setEmail(CoreForm::getForm('register')->grabFieldValue('email'));
				$UserTemplateObject->setPassword(CoreForm::getForm('register')->grabFieldValue('password'));
				$UserTemplateObject->setFirstName(CoreForm::getForm('register')->grabFieldValue('first_name'));
				$UserTemplateObject->setLastName(CoreForm::getForm('register')->grabFieldValue('last_name'));

				//create user
				$UserObject = CoreLogic::getService('UserService')->create($UserTemplateObject);

				if($UserObject)
					CoreHeaders::setFallbackRedirect('/');

			} else {

                CoreNotification::set('Invalid submission', CoreNotification::ERROR);

            }
			
		}

	}
		
	/**
	 * First name
	 */
	public $firstName = null;

	/**
	 * First name
	 */
	public $lastName = null;
		
	/**
	 * Set first name
	 */
	public function setFirstName($value = ''){
		$this->firstName = $value;
	}
	
	/**
	 * Set last name
	 */
	public function setLastName($value = ''){
		$this->lastName = $value;
	}
	
}