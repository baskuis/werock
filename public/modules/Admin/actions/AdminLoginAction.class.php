<?php 

/**
 * Login form actions
 */
class AdminLoginAction extends CoreRenderTemplate implements CoreRenderTemplateInterface {

    /**
     * Page title
     *
     * @var string
     */
    public $title = 'Admin Login';

    /**
     * Page description
     *
     * @var string
     */
    public $description = '';

	/**
	 * Set main template
	 */
	public $template = 'adminlogin';

    /** @var UserService $UserService */
    private $UserService = null;

    /** @var UserEntitlementService $UserEntitlementService */
    private $UserEntitlementService = null;

    /**
     * Login form
     * @var FormUI $LoginForm
     */
    private $LoginForm;

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

        $route = new CoreControllerObject('/admin/login', __CLASS__, null, CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_PAGE);
        $route->setType(CoreControllerObject::TYPE_ACTION);
        array_push($routes, $route);

        return $routes;

    }

    /**
     * UserRegisterAction the model
     *
     * @return mixed
     */
    public function register()
    {

    }

    /**
	 * Catch params
	 */
	public function build($params = array()){

        /** @var UserEntitlementService $UserEntitlementService */
        $UserEntitlementService = CoreLogic::getService('UserEntitlementService');
        if($UserEntitlementService->hasEntitlement(UserModule::ENTITLEMENT_SYSTEM_ADMIN)){
            CoreHeaders::setRedirect('/admin');
        }

        $this->UserService = CoreLogic::getService('UserService');
        $this->UserEntitlementService = CoreLogic::getService('UserEntitlementService');

		/**
		 * Set form data
         * @var FormUI $this->LoginForm
		 */
        $this->LoginForm = CoreForm::register('adminlogin', array('method' => 'post', 'action' => ''));
        $this->LoginForm->setFormAttributes(array('role' => 'form'));

        /**
         * Create Username Field
         */
        $FormField = new FormField();
        $FormField->setName('username');
        $FormField->setLabel('Username');
        $FormField->setType('forminputtext');
        $FormField->setTemplate('formfieldnaked');
        $FormField->setCondition('/^[^\s^\t]+$/');
        $FormField->setHelper('Please enter a valid username');
        $FormField->setPlaceholder('id: john.parker');
        $FormField->setValue(null);
        $this->LoginForm->addField($FormField);

        /**
         * Create Password Field
         */
        $FormField = new FormField();
        $FormField->setName('password');
        $FormField->setLabel('Password');
        $FormField->setType('forminputpassword');
        $FormField->setTemplate('formfieldnaked');
        $FormField->setCondition('password');
        $FormField->setHelper('Your password is required');
        $FormField->setPlaceholder(null);
        $FormField->setValue(null);
        $this->LoginForm->addField($FormField);

        /**
         * Remember me
         */
        $FormField = new FormField();
        $FormField->setName('login_rememberme');
        $FormField->setLabel(null);
        $FormField->setType('formcheckbox');
        $FormField->setTemplate('formfieldnaked');
        $FormField->setCondition(null);
        $FormField->setHelper('Remember Me');
        $FormField->setPlaceholder(null);
        $FormField->setValue('yes');
        $this->LoginForm->addField($FormField);

        /**
         * Create Submit Button
         */
        $FormField = new FormField();
        $FormField->setName('login_submit');
        $FormField->setLabel(null);
        $FormField->setType('formbuttonlarge');
        $FormField->setTemplate('formfieldnaked');
        $FormField->setCondition(null);
        $FormField->setHelper(null);
        $FormField->setPlaceholder('Sign In');
        $FormField->setValue(null);
        $this->LoginForm->addField($FormField);

		//handle submission
		if($this->LoginForm->validFormSubmitted()){
			if($this->LoginForm->validateSubmission()){
						
				//Create authentication object
				$UserAuthenticationObject = CoreLogic::getObject('UserAuthenticationObject');
				$UserAuthenticationObject->setUsername($this->LoginForm->grabFieldValue('username'));
				$UserAuthenticationObject->setPassword($this->LoginForm->grabFieldValue('password'));
						
				//Authentication Object
				$UserObject = $this->UserService->authenticate($UserAuthenticationObject);

                //redirect if successful login
                if($UserObject instanceof UserObject){
                    if($UserEntitlementService->hasEntitlement(UserModule::ENTITLEMENT_SYSTEM_ADMIN)){
                        CoreHeaders::setRedirect('/admin');
                    }
                }
				
			}
		}

	}
	
}