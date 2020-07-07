<?php 

/**
 * User UserLoginAction Action
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserLoginAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {
	
	/**
	 * Set main template
	 */
	public $template = 'userlogin';
	
	/**
	 * Define register fields
	 */
	public $fields = array();

    /**
     * @var strong Title
     */
    public $title = 'Login';

    /**
     * @var string Description
     */
    public $description = CoreStringUtils::EMPTY_STRING;

    /**
     * Form reference
     *
     * @var string
     */
    private $form_reference = 'login';

	/**
	 * @var UserService $UserService
	 */
	private $UserService;

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
		array_push($routes, CoreControllerObject::buildAction('/login', __CLASS__, CoreControllerObject::MATCH_TYPE_STRING));
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
	 * Build login
	 *
	 * @param array $params
	 * @return null
	 */
    public function build($params = array()){

		$this->UserService = CoreLogic::getService('UserService');

		//redirect
		if($this->UserService->activeUser()){
			CoreHeaders::setRedirect('/');
		}

		/**
		 * Define register form fields
		 */
		$this->fields[] = array(
			'name' => 'email',
			'label' => 'Email',
			'type' => 'forminputemail',
			'template' => 'formfieldflexible',
			'condition' => '*',
			'placeholder' => CoreLanguage::get('form:label:email:placeholder'),
			'helper' => 'Please enter the email you signed up with',
			'value' => CoreVisitor::retrieve(UserProcedure::VISITOR_DATA_EMAIL)
		);
		$this->fields[] = array(
			'name' => 'password',
			'label' => 'Password',
			'type' => 'forminputpassword',
			'template' => 'formfieldflexible',
			'condition' => 'password',
			'helper' => 'Please enter your chosen password',
			'placeholder' => '',
			'value' => null
		);
		$this->fields[] = array(
			'name' => 'register_link',
			'label' => '',
			'href' => '/register',
			'type' => 'forminputlink',
			'template' => 'formfieldflexible',
			'placeholder' => '',
			'value' => 'Register'
		);
		$this->fields[] = array(
			'name' => 'login_submit',
			'label' => '',
			'type' => 'forminputsubmit',
			'template' => 'formfieldflexible',
			'placeholder' => '',
			'value' => 'Login'
		);
		
		/**
		 * Set form data
		 */
		CoreForm::register($this->form_reference, array('method' => 'post', 'action' => ''), $this->fields);
		
		//handle submission
		if(CoreForm::getForm($this->form_reference)->validFormSubmitted()){

			//track user data
			CoreVisitor::setData('attempted:login', 1);
			
			//validate submission
			if(CoreForm::getForm($this->form_reference)->validateSubmission()){

				//Create Authentication object
                /* @var $UserAuthenticationObject UserAuthenticationObject */
				$UserAuthenticationObject = CoreLogic::getObject('UserAuthenticationObject');
				$UserAuthenticationObject->setEmail(CoreForm::getForm($this->form_reference)->grabFieldValue('email'));
				$UserAuthenticationObject->setPassword(CoreForm::getForm($this->form_reference)->grabFieldValue('password'));

                /* @var $UserObject UserObject */
				$UserObject = $this->UserService->authenticate($UserAuthenticationObject);

				if($UserObject)
					CoreHeaders::setFallbackRedirect('/');

			}
		
		}

	}
	
}		