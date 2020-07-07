<?php

/**
 * Core page rendering template
 *
 * PHP version 5
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreRenderTemplate {

    /**
     * Render template constants
     */
    const DECORATOR_CONTENT_DATA_KEY = "decorator_content";

	/**
	 * Every rendered page needs a template
	 */
	public $template = CoreStringUtils::EMPTY_STRING;

    /**
     * Optionally, a decorator template could be used
     *
     * @var string
     */
    public $decorator = CoreStringUtils::EMPTY_STRING;

	/**
	 * Page title
	 */
	public $title = CoreStringUtils::EMPTY_STRING;
	
	/**
	 * Page description
	 */
	public $description = CoreStringUtils::EMPTY_STRING;	
	
	/**
	 * Page notifications
	 */
	public $notifications = array();

    /**
     * Set Decorator
     *
     * @param string $value
     */
    public function setDecorator($value = CoreStringUtils::EMPTY_STRING){
       $this->decorator = $value;
    }

	/**
	 * Set template
	 *
	 * @param string $value
	 */
	public function setTemplate($value = CoreStringUtils::EMPTY_STRING){
		$this->template = $value;
	}
	
	/**
	 * Template getter
	 */
	public function getTemplate(){
		return $this->template;
	}

	/**
	 * Set page title
	 *
	 * @param string $value
	 */
	public function setTitle($value = CoreStringUtils::EMPTY_STRING){
		$this->title = $value;
	}

	/**
	 * Set page description
	 *
	 * @param string $value
	 */
	public function setDescription($value = CoreStringUtils::EMPTY_STRING){
		$this->description = $value;
	}

	/**
	 * Set notifications
	 *
	 * @param array $notifications
	 */
	public function setNotifications($notifications = array()){
		$this->notifications = $notifications;
	}

	/**
	 * Get page title
	 */
	public function getTitle(){
		return $this->title;
	}
	
	/**
	 * Get page description
	 */
	public function getDescription(){
		return $this->description;
	}
	
	/**
	 * Get notifications
	 */
	public function getNotifications(){
		return $this->notifications;
	}

	/**
	 * Builds Notifications on rendering object
	 */
    protected function buildNotifications(){

		//set notifications	
		$notificationTypes = CoreNotification::getTypes();
		foreach($notificationTypes as $type){
			$this->notifications[$type]	= CoreNotification::getNotifications($type);
		}
				
	}

	/**
	 * Prepare template for Mustache
	 */	
	protected function prepareTemplate(){
		return self::addMustacheHelpers((array) $this);
	}

	/**
	 * Add Mustache helpers
	 * recursive array stepper
	 * @parem mixed $array
	 * @return array
	 */
    protected function addMustacheHelpers($array = array()){
		
		//define return
		$return = array();
		
		//step through array
		foreach($array as $key => $value){
			
			//recursive
			$return[$key] = is_array($value) ? self::addMustacheHelpers($value) : $value;
			
			//mustache exists helper
			if(!is_numeric($key)) $return[CoreFeTemplate::FE_HAVE_PREFIX . $key] = !empty($value);
		
		}
		
		//return array
		return $return;
		
	}
		
	/**
	 * Render template and output to browser
	 */
	public function execute(){

		/**
		 * Skip building model
		 * not needed
		 */
		if(!CoreHeaders::needBody()) return;

		/**
		 * Set title and description
		 */
		CoreRender::$title = $this->title;
		CoreRender::$description = $this->description;

        //set notifications
        self::buildNotifications();

		//template data
		$template = self::prepareTemplate();

		//add in queued data
		if(!empty(CoreRender::$data)){
			foreach(CoreRender::$data as $key => $value){

				//recursive
				$template[$key] = is_array($value) ? self::addMustacheHelpers($value) : $value;
	
				//mustache exists helper
				if(!is_numeric($key)) $template[CoreFeTemplate::FE_HAVE_PREFIX . $key] = !empty($value);							

			}
		}

        /**
         * Use Decorator
         */
        if(!empty($this->decorator)){

            //set decorator content
			/** @suppress render */
            $template[self::DECORATOR_CONTENT_DATA_KEY] = CoreTemplate::render($this->template, $template);

            //render template
            CoreRender::renderPage(CoreTemplate::render($this->decorator, $template));

            return null;

        }

		/**
		 * Render template
		 */		 
		CoreRender::renderPage(CoreTemplate::render($this->template, $template));

        return null;

	}
	
}