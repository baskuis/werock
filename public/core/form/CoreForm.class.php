<?php

/**
 * Core Form
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreForm {

    /**
     * Allow reflection
     */
    use ClassReflectionTrait;

    /**
     * Allow interceptors
     */
    use CoreInterceptorTrait;

	/**
	 * Form instances
	 */
	private static $formInstances = array();

	/**
	 * Stack form
	 */
	private static $forms = array();

	/**
	 * Forms
	 */
	const FORMS_FOLDER = 'forms';

	/**
	 * Get form
	 * @param String $name
	 * @return FormUI
	 */
	public static function getForm($name = null){
		if(isset(self::$forms[$name])){
			return self::$forms[$name];
		}
	}

	/**
	 * Load form
	 * @param null $path
	 */
	public static function loadForms($path = null){
		$forms = CoreFilesystemUtils::readFiles(DOCUMENT_ROOT . $path . "/" . self::FORMS_FOLDER);
		if(!empty($forms)){
			foreach($forms as $form) {
				$formPath = DOCUMENT_ROOT . $path . "/" . self::FORMS_FOLDER . "/" . $form;
				$formName = str_ireplace('.php', null, $form);
				$formInstance = new CoreFormObject();
				$formInstance->setName($formName);
				$formInstance->setLoaded(false);
				$formInstance->setPath($formPath);
				if (isset($formInstances[$formName])) {
					CoreLog::debug('Overloading form with name ' . $formName);
				}
				$formInstances[$formName] = $formInstance;
			}
		}
	}
	
	/**
	 * Register a form
     *
     * @param null $name
     * @param array $form
     * @param array $fields
     * @return FormUI
     */
    public static function register($name = null, $form = array(), $fields = array()){
		
		//quick sanity check
		if(empty($name)){
			CoreLog::error('Cannot create form with no name');
			return false;
		}
		
		//don't override form
		if(isset(self::$forms[$name])){
			CoreLog::error('Already had a form with name ' . $name);
			return false;
		}

		/**
		 * Create 
		 */
		self::$forms[$name] = new FormUI();
		
		/**
		 * Set form attributes
		 */
		self::$forms[$name]->setFormName((string)$name);
		self::$forms[$name]->setFormAction(isset($form['action']) ? (string)$form['action'] : null);
		self::$forms[$name]->setFormMethod(isset($form['method']) ? (string)$form['method'] : null);
		
		/**
		 * Set form fields
		 */
		if(!empty($fields)) self::$forms[$name]->buildFormFromArray($fields);

        /**
         * Return the form
         */
        return self::$forms[$name];
		
	}
	
	/**
	 * Build form header
	 * @param String form name
	 * @return String form header html string
	 */
	public static function buildFormHeader($name = null){
		
		//check for form
		if(!isset(self::$forms[$name])){
			CoreLog::error('Unable to find form by name [' . $name . ']');
			return false;
		}
		
		//check for method
		if(!method_exists(self::$forms[$name], 'buildFormHeader')){
			CoreLog::error('Invalid entry for form by name [' . $name . ']. Unable to call method []');
			return false;
		}
		
		//return header
		return self::$forms[$name]->buildFormHeader();
		
	}
	
	/**
	 * Grab rendered field by name
	 * @param String Form name
	 * @param String Form field name
	 * @return String Rendered form html string
	 */
	public static function grabField($name = null, $fieldName = null){

		//check for form
		if(!isset(self::$forms[$name])){
			CoreLog::error('Unable to find form by name [' . $name . ']');
			return false;
		}
		
		//check for method
		if(!method_exists(self::$forms[$name], 'grabField')){
			CoreLog::error('Invalid entry for form by name [' . $name . ']. Unable to call method []');
			return false;
		}
		
		//return field
		return self::$forms[$name]->grabField($fieldName);
				
	}

	/**
	 * Build form header
	 * @param String form name
	 * @return String form header html string
	 */
	public static function buildFormFooter($name = null){
		
		//check for form
		if(!isset(self::$forms[$name])){
			CoreLog::error('Unable to find form by name [' . $name . ']');
			return false;
		}
		
		//check for method
		if(!method_exists(self::$forms[$name], 'buildFormFooter')){
			CoreLog::error('Invalid entry for form by name [' . $name . ']. Unable to call method []');
			return false;
		}
		
		//return header
		return self::$forms[$name]->buildFormFooter();
		
	}
			
}