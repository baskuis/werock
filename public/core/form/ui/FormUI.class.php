<?php

/**
* Builds site forms
*
* PHP version 5
* @package Ukora
* @author Bas Kuis <b@ukora.com>
* @copyright 2012 Bas Kuis (http://www.ukora.com)
* @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
* @link http://www.ukora.com/cms/documentation/
*/
class FormUI {
    
    const NAME = 'name';
    const METHOD = 'method';
    const MD5NAME = 'md5name';
    const ACTION = 'action';
    const ATTR = 'attr';
    const TYPE = 'type';
    const LABEL = 'label';
    const DEFAULT_VALUE = 'default_value';
    const OPTIONS = 'options';
    const GET = 'get';
    const POST = 'post';
	const FORM = 'form';
    const FORM_ = 'form_';
    const _STAMP = '_stamp';
    const CONDITION = 'condition';
    const TEMPLATE = 'template';
    const DIMENSIONS = 'dimensions';
    const WIDTH = 'width';
    const HEIGHT = 'height';
    const PLACEHOLDER = 'placeholder';
    const HELPER = 'helper';
    const KEY = 'key';
    const VALUE = 'value';
    const EXTENSIONS = 'extensions';
    const DATA = 'data';
    const MAP_TO = 'map_to';
    const DISABLED = 'disabled';
    const HREF = 'href';
    const UNIQUEID = 'uniqueid';
    const CLASS_ = 'class';
    const ONCLICK = 'onclick';
    const OUTPUT = 'output';
    const SUBMITTED_VALUE = 'submitted_value';
    const CAPTCHA = 'captcha';
	const ALT_CAPTCHA = 'altcaptcha';
    const PASSWORD = 'password';
    const PASSWORD_REPEAT = 'password_repeat';
    const _PW = '_pw';
    const _PASSWORD = '_password';
    const EMPTY_STRING = '';
    const FIELD_ = 'field_';
	const _ = '_';
	const COMMA = ',';

	/**
	 * Form constructor
	 */
	function __construct(){
				
		//form details
		$this->form = array();
		$this->form[self::METHOD] = self::POST;
		$this->form[self::NAME] = self::EMPTY_STRING;

		//field prepend
		$this->id_field_prepend = self::FIELD_;
		
		//current element
		$this->form_element = null;
		
		//form elements
		$this->form_elements = array();

	}

	/**
	 * @var run this when form validates
	 */
	private $jsHandler;

	public function setJSHandler($js = null){
		$this->jsHandler = $js;
	}

	/**
	 * Get form method
	 *
	 * @return mixed
	 */
	public function getMethod(){
		return isset($this->form[self::METHOD]) ? $this->form[self::METHOD] : false;
	}

	/**
	 * Sets form actions, get or post
	 * @param string $action Action of form, can be null
	 * @return bool Returns true
	 */
	public function setFormAction($action = null){
		$this->form[self::ACTION] = $action;
		return true;
	}

	/**
	 * Sets form name
	 * @param string $name Name of form
	 */	
	public function setFormName($name = null){
		$this->form[self::NAME] = $name;
		$this->form[self::MD5NAME] = md5($name);
	}

    /**
     * Set form attributes
     * @param array $attr
     */
    public function setFormAttributes($attr = array()){
        $this->form[self::ATTR] = $attr;
    }

	/**
	 * Sets form actions, get or post
	 * @param string $method GET or POST
	 * @return bool Returns true when set and false otherwise
	 */	
	public function setFormMethod($method = self::POST){
		if(strtolower($method) == self::GET || strtolower($method) == self::POST){
			$this->form[self::METHOD] = $method;
			return true;	
		}	
		return false;			
	}

	/**
	 * Sets form table
	 * @param string $table_name Mysql table name
	 * @return bool Returns true
	 */
	public function setFormTable($table_name = self::EMPTY_STRING){
		$this->form['table'] = $table_name;
		return true;		
	}

	/**
	 * Checks if a valid form has been submitted
	 * @return bool Returns true when form has been submitted and false otherwise
	 */	
	public function validFormSubmitted(){
		if($this->form[self::METHOD] == self::GET){
			if(isset($_GET[self::FORM_ . $this->form[self::NAME] . self::_STAMP]) && $_GET[self::FORM_ . $this->form[self::NAME] . self::_STAMP] == md5(CoreVisitor::getId())){ 
				return true; 
			}
		}else{
			if(isset($_POST[self::FORM_ . $this->form[self::NAME] . self::_STAMP]) && $_POST[self::FORM_ . $this->form[self::NAME] . self::_STAMP] == md5(CoreVisitor::getId())){ 
				return true; 
			}
		}
		return false; 
	}
	
	/**
	 * Clears out field holder
	 * @return bool Returns true
	 */
	public function nextField(){
		$this->form_element = null;
		return true;
	}

	/**
	 * Sets field type
	 * @param string $type Sets field type
	 * @return bool Returns true when set and false otherwise
	 */	
	public function setFieldType($type = null){
		$this->form_element[self::TYPE] = $type;
		return true;
	}

	/**
	 * Sets field label
	 * @param string $label Sets field label
	 * @return bool Returns true when set and false otherwise
	 */	
	public function setFieldLabel($label = null){
		$this->form_element[self::LABEL] = $label;
	}

    private function handleValue($element, $value){
        if(!isset($element[self::NAME])) CoreLog::error('Unable to find element name');
        switch(true){
            case (strtolower(substr($element[self::NAME], -3)) == self::_PW):
                return CoreSecUtils::preparePassword($value);
                break;
            case (strtolower(substr($element[self::NAME], -9)) == self::_PASSWORD):
                return CoreSecUtils::preparePassword($value);
                break;
            case (strtolower(substr($element[self::NAME], -15)) == self::PASSWORD_REPEAT):
                return CoreSecUtils::preparePassword($value);
                break;
            default:

                return $value;
                break;
        }
    }

	/**
	 * Sets default field value
	 * @param string $value Default field value
	 * @return bool Returns true
	 */
	public function setDefaultValue($value = null){

        //populate
        if(strtolower($this->form[self::METHOD]) == self::GET){
			$this->form_element[self::DEFAULT_VALUE] = isset($_GET[self::buildFieldName()]) ? $_GET[self::buildFieldName()] : $value;
		}else{
			$this->form_element[self::DEFAULT_VALUE] = isset($_POST[self::buildFieldName()]) ? $_POST[self::buildFieldName()] : $value;
		}

        //mark options
        if(isset($this->form_element[self::OPTIONS]) && !empty($this->form_element[self::OPTIONS])){
			/** @var FormFieldOption $option */
			foreach($this->form_element[self::OPTIONS] as &$option){
                if($option->selected == null) {
					$option->selected = ($this->form_element[self::DEFAULT_VALUE] == $option->getKey());
				}
			}
        }

		return true;

	}

	/**
	 * Sets field condition, this can be *, null, regex or condition string set in validate()
	 * @param string $condition Condition string
	 * @return bool Returns true
	 */
	public function setFieldCondition($condition = null){
		$this->form_element[self::CONDITION] = $condition;
		return true;
	}
	
	/**
	 * Sets field name, this should be unique per field
	 * @param string $name Name of the field
	 * @return bool Returns true
	 */
	public function setFieldName($name = null){
		$this->form_element[self::NAME] = $name;
        $this->form_element[self::UNIQUEID] = md5($this->form[self::NAME] . ':' . $name);
		return true;
	}

	/**
	 * Sets field href
	 * @param string $name Name of the field
	 * @return bool Returns true
	 */
	public function setFieldHref($link = null){
		$this->form_element[self::HREF] = $link;
		return true;
	}

	/**
	 * Sets class
	 * @param string $name Name of the field
	 * @return bool Returns true
	 */
	public function setFieldClass($classes = null){
		$this->form_element[self::CLASS_] = $classes;
		return true;
	}

	/**
	 * Sets class
	 * @param string $onclick Onclick handler
	 * @return bool Returns true
	 */
	public function setFieldOnclick($onclick = null){
		$this->form_element[self::ONCLICK] = $onclick;
		return true;
	}

	/**
	 * Sets field map to mysql field name, if needed
	 * @param string $field_name Mysql field name
	 * @return bool Returns true
	 */
	public function setFieldMapTo($field_name = null){
		$this->form_element[self::MAP_TO] = $field_name;
		return true;
	}

	/**
	 * Sets field options, used for select fields
	 * @param array $options Array of field options, the array keys will be saved
	 * @return bool Returns true
	 */	
	public function setFieldOptions($options = null){
        $fixedOptions = array();
		if(!empty($options)) {
			foreach ($options as $key => $option) {
				if (is_object($option) && get_class($option) == FormFieldOption::class) {
					array_push($fixedOptions, $option);
				} else if (is_array($option)) {
					if (!isset($option[self::KEY]) || !isset($option[self::VALUE])) {
						CoreNotification::set('Need key and value to set option', CoreNotification::ERROR);
					} else {
						$FormFieldOption = new FormFieldOption();
						$FormFieldOption->setKey($option[self::KEY]);
						$FormFieldOption->setValue($option[self::VALUE]);
						array_push($fixedOptions, $FormFieldOption);
					}
				} else {
					$FormFieldOption = new FormFieldOption();
					$FormFieldOption->setKey($option);
					$FormFieldOption->setValue($option);
					array_push($fixedOptions, $FormFieldOption);
				}
			}
		}
		$this->form_element[self::OPTIONS] = $fixedOptions;
		return true;
	}

	/**
	 * Sets field image dimensions, used for image upload fields
	 * @param int $width Desired width of uploaded image in pixels
	 * @param int $height Desired height of uploaded image in pixels
	 * @return bool Returns true
	 */	
	public function setFieldImageDimensions($width = 200, $height = 120){
		$this->form_element[self::DIMENSIONS][self::WIDTH] = (int)$width;
		$this->form_element[self::DIMENSIONS][self::HEIGHT] = (int)$height;
		return true;
	}

	/**
	 * Sets field placeholder
	 * @param string $placeholder Placeholder of field, like an entry example
	 * @return bool Returns true
	 */		
	public function setFieldPlaceholder($placeholder = null){
		$this->form_element[self::PLACEHOLDER] = $placeholder;
		return true;
	}

	/**
	 * Sets field helper text
	 * @param string $helper Helper text of field
	 * @return bool Returns true
	 */	
	public function setFieldHelper($helper = null){
		$this->form_element[self::HELPER] = $helper;
		return true;
	}

	/**
	 * Sets allowed file extensions
	 * @param string $ext Extensions array of comma seperated string
	 * @return bool Returns true
	 */	
	public function setFileExtensions($ext = null){
		$this->form_element[self::EXTENSIONS] = !is_array($ext) ? explode(self::COMMA, $ext) : $ext;
		return true;
	}

	/**
	 * Sets field template
	 * @param string $template Set field template
	 * @return bool Returns true
	 */
	public function setTemplate($template = null){
		$this->form_element[self::TEMPLATE] = $template;
		return true;
	}

    /**
     * Set field data
     *
     * @param array $data
     * @return bool
     */
    public function setFieldData($data = array()){
        $this->form_element[self::DATA] = $data;
        return true;
    }

	/**
	 * Creates form field element
	 * @return string Returns field id attr
	 */
	public function buildFieldId(){
		return $this->id_field_prepend . $this->form[self::NAME] . self::_ . $this->form_element[self::NAME];
	}	

	/**
	 * Sets field template
	 * @param string $element Optionally pass an element, or current element
	 * @return string Returns form element name
	 */
	public function buildFieldName($element = null){
		if(!empty($element)){ return $element[self::NAME]; }
		return $this->form_element[self::NAME];
	}

    /**
     * Set field data
     *
     * @param FormField $formField
     */
    public function addField(FormField $formField){

        //template
        self::setTemplate($formField->getTemplate());

        //label
        self::setFieldLabel($formField->getLabel());

        //condition
        self::setFieldCondition($formField->getCondition());

        //type
        self::setFieldType($formField->getType());

        //options
        self::setFieldOptions($formField->getOptions());

        //name
        self::setFieldName($formField->getName());

        //map_to
        self::setFieldMapTo($formField->getMapTo());

        //width and height in case of image
        self::setFieldImageDimensions($formField->getWidth(), $formField->getHeight());

        //extensions
        self::setFileExtensions($formField->getExtensions());

        //placeholder
        self::setFieldPlaceholder($formField->getPlaceholder());

        //helper
        self::setFieldHelper($formField->getHelper());

		//disabled
		self::setFieldDisabled($formField->getDisabled());

        //set Field href
        self::setFieldHref($formField->getHref());

        //sets field class(es)
        self::setFieldClass($formField->getClass());

        //sets field onclick
        self::setFieldOnclick($formField->getOnclick());

        //value
        self::setDefaultValue($formField->getValue());

        //data
        self::setFieldData($formField->getData());

        //build field
        self::buildField();

        //reset field
        self::nextField();

    }

	/**
	 * Set field disabled
	 *
	 * @param bool $disabled
	 */
	private function setFieldDisabled($disabled = false){
		$this->form_element[self::DISABLED] = $disabled;
	}

    /**
     * Set field from array
     *
     * @param Array $field_configuration
     */
    private function setFieldFromArray($field_configuration){

        //template
        self::setTemplate(isset($field_configuration[self::TEMPLATE]) ? $field_configuration[self::TEMPLATE] : null);

        //label
        self::setFieldLabel(isset($field_configuration[self::LABEL]) ? $field_configuration[self::LABEL] : null);

        //condition
        self::setFieldCondition(isset($field_configuration[self::CONDITION]) ? $field_configuration[self::CONDITION] : null);

        //type
        self::setFieldType(isset($field_configuration[self::TYPE]) ? $field_configuration[self::TYPE] : null);

        //options
        self::setFieldOptions(isset($field_configuration[self::OPTIONS]) ? $field_configuration[self::OPTIONS] : null);

        //name
        self::setFieldName(isset($field_configuration[self::NAME]) ? $field_configuration[self::NAME] : null);

        //map_to
        self::setFieldMapTo(isset($field_configuration[self::MAP_TO]) ? $field_configuration[self::MAP_TO] : null);

        //width
        self::setFieldImageDimensions(isset($field_configuration[self::WIDTH]) ? (int)$field_configuration[self::WIDTH] : 200, isset($field_configuration[self::HEIGHT]) ? (int)$field_configuration[self::HEIGHT] : 200);

        //extensions
        self::setFileExtensions(isset($field_configuration[self::EXTENSIONS]) ? $field_configuration[self::EXTENSIONS] : null);

        //placeholder
        self::setFieldPlaceholder(isset($field_configuration[self::PLACEHOLDER]) ? $field_configuration[self::PLACEHOLDER] : null);

        //helper
        self::setFieldHelper(isset($field_configuration[self::HELPER]) ? $field_configuration[self::HELPER] : null);

		//disabled
		self::setFieldDisabled(isset($field_configuration[self::DISABLED]) ? $field_configuration[self::DISABLED] : false);

        //set Field href
        self::setFieldHref(isset($field_configuration[self::HREF]) ? $field_configuration[self::HREF] : null);

        //sets field class(es)
        self::setFieldClass(isset($field_configuration[self::CLASS_]) ? $field_configuration[self::CLASS_] : null);

        //sets field onclick
        self::setFieldOnclick(isset($field_configuration[self::ONCLICK]) ? $field_configuration[self::ONCLICK] : null);

        //value
        self::setDefaultValue(isset($field_configuration[self::VALUE]) ? $field_configuration[self::VALUE] : null);

        //data
        self::setFieldData(isset($field_configuration[self::DATA]) ? $field_configuration[self::DATA] : null);

        //build field
        self::buildField();

        //reset field
        self::nextField();

    }

	/**
	 * Sets field template
	 * @param array $form_configuration Array that lists all fields
	 * @return string Returns try when configuration has loaded false when not
	 */	
	public function buildFormFromArray($form_configuration = array()){
		if(empty($form_configuration)){ return false; }
		foreach($form_configuration as $field_configuration){

            /**
             * Set field configuration
             */
            self::setFieldFromArray($field_configuration);
		
		}
		return true;
	}
	
	/**
	 * Creates form field element
	 * @return string Returns form handling js block
	 */	
	public function buildJavascriptHandlers(){
		
		//build form js block
		$return = '

				/*************** KEEP STATUS STACK - JAVASCRIPT *******************/
				var hook' . $this->form[self::MD5NAME] . 'FieldStatusStack = {};
				
				/*************** FIELD HELPER STACK - JAVASCRIPT *******************/
				var hook' . $this->form[self::MD5NAME] . 'FieldHelperStack = {};
				';
		if(isset($this->form_elements) && !empty($this->form_elements)){
			foreach($this->form_elements as $element){
				$return .= '
				hook' . $this->form[self::MD5NAME] . 'FieldHelperStack[\'' . CoreStringUtils::strip(self::buildFieldName($element), '\'') . '\'] = \'' . CoreStringUtils::strip($element[self::HELPER], '\'') . '\';' . "\n";
			}
		}
		$return .= '

				/*************** GET FIELD STATUS - JAVASCRIPT *******************/
				function hook' . $this->form[self::MD5NAME] . 'getFieldStatus(name, value){
					switch(name){' . "\n";
		
		//check form elements
		if(isset($this->form_elements) && !empty($this->form_elements)){
			foreach($this->form_elements as $element){
				$return .= '
						case \'' . CoreStringUtils::strip(self::buildFieldName($element), '\'') . '\': //js case' . "\n";
				
				switch(true){
					
					/***************** JUST NOT EMPTY ****************/
					case ($element[self::CONDITION] == '*'):
						$return .= 'hook' . $this->form[self::MD5NAME] . 'setFieldStatus(name, (value.length > 0));' . "\n";
					break;
	
					/****************** MATCH PATTERN ****************/
					case (preg_match('/\/.*?\/[a-z]*/i', $element[self::CONDITION])):
						$return .= 'hook' . $this->form[self::MD5NAME] . 'setFieldStatus(name, (value.match(' . $element[self::CONDITION] . ') != undefined && value.match(' . $element[self::CONDITION] . ').length > 0));' . "\n";
					break;

                    /****************** PASSWORD REPEAT **************/
                    case (substr($element[self::NAME], 0, 15) == self::PASSWORD_REPEAT):
                        $return .= 'hook' . $this->form[self::MD5NAME] . 'setFieldStatus(name, (value == hook' . $this->form[self::MD5NAME] . 'checkValue(\'' . self::PASSWORD . '\')));' . "\n";
                    break;

                    /****************** PASSWORD *********************/
                    case ($element[self::CONDITION] == self::PASSWORD):
                        $return .= 'hook' . $this->form[self::MD5NAME] . 'setFieldStatus(name, (value.match(/^[^\s]{4,}$/) != undefined && value.match(/^[^\s]{4,}$/).length > 0));' . "\n";
                    break;

					/****************** ALWAYS TRUE ******************/
					default:
						$return .= 'hook' . $this->form[self::MD5NAME] . 'setFieldStatus(name, true);' . "\n";
					break;
					
				}
				$return .= '
						break; //js break' . "\n";

            }
		}
		
		//close form element checker
		$return .= '
					}
				}
				
				/*************** SET FIELD STATUS - JAVASCRIPT *******************/
				function hook' . $this->form[self::MD5NAME] . 'setFieldStatus(name, status){
					if(status){ 
						$(\'#wrapper_for_\' + name).addClass(\'checked\').removeClass(\'failed\');
					}else{
						$(\'#wrapper_for_\' + name).addClass(\'failed\').removeClass(\'checked\');
					}
					hook' . $this->form[self::MD5NAME] . 'FieldStatusStack[name] = status;
				}

				/*************** CHECK ALL FIELDS - JAVASCRIPT *******************/
				function hook' . $this->form[self::MD5NAME] . 'checkValue(name){
				    return $(\'select[name=\' + name + \'] option:selected, input[name=\' + name + \'], textarea[name=\' + name + \']\', \'#' . $this->form[self::NAME] . '_form_id\').val();
				}

				/*************** CHECK ALL FIELDS - JAVASCRIPT *******************/
				function hook' . $this->form[self::MD5NAME] . 'checkAllFields(){
					
					//check checkbox
					$(\'input[type=checkbox]\', \'#' . $this->form[self::NAME] . '_form_id\').each(function(){
						
						//get field status
						hook' . $this->form[self::MD5NAME] . 'getFieldStatus($(this).prop(\'name\'), ($(this).prop(\'checked\') ? $(this).val() : \'\'));
						
					});
					
					//check all input variants
					$(\'input[type=text], input[type=password], input[type=email], input[type=search], input[type=hidden]\', \'#' . $this->form[self::NAME] . '_form_id\').each(function(){
						
						//check for unchanged
						if($(this).val() == $(this).prop(\'title\')){ $(this).val(\'\'); $(this).addClass(\'untouched\').removeClass(\'touched\'); }else{ $(this).addClass(\'touched\').removeClass(\'untouched\'); }
						
						//get field status
						hook' . $this->form[self::MD5NAME] . 'getFieldStatus($(this).prop(\'name\'), $(this).val());
												
					});
					
					//check all select boxes
					$(\'select\', \'#' . $this->form[self::NAME] . '_form_id\').each(function(){

						//get field status
						hook' . $this->form[self::MD5NAME] . 'getFieldStatus($(this).prop(\'name\'), $(this).children(\'option:selected\').val());
												
					});
					
					//check all textareas
					$(\'textarea\', \'#' . $this->form[self::NAME] . '_form_id\').each(function(){

						//check for unchanged
						if($(this).val() == $(this).prop(\'title\')){ $(this).val(\'\'); }
												
						//get field status
						hook' . $this->form[self::MD5NAME] . 'getFieldStatus($(this).prop(\'name\'), $(this).val());
						
					});
					
				}
				
				/*************** ON SUBMIT - JAVASCRIPT *******************/
				function hook' . $this->form[self::MD5NAME] . 'submitForm(){
					
					//check all fields
					hook' . $this->form[self::MD5NAME] . 'checkAllFields();
					
					//assume we are doing good
					var problems = false;
					
					//keep track of fields
					var problem_fields = [];
					
					//keep track of messages
					var problem_helper = \'\';
					
					if(hook' . $this->form[self::MD5NAME] . 'FieldStatusStack != undefined){
						for(name in hook' . $this->form[self::MD5NAME] . 'FieldStatusStack){
							if(true !== hook' . $this->form[self::MD5NAME] . 'FieldStatusStack[name]){
								
								//we found a problem
								problems = true;
								
								//keep track of the problematic fields
								problem_fields.push(name);
								
								//add helper
								if(hook' . $this->form[self::MD5NAME] . 'FieldHelperStack[name] != undefined){ 
									problem_helper = problem_helper + hook' . $this->form[self::MD5NAME] . 'FieldHelperStack[name] + \'\\n\';
								}
							}
						}
					}
					
					if(problems){ 
						alert(\'It looks like a couple of things are missing. \n\n\' + problem_helper); 
						return false;
					}else{
					 	' . (!empty($this->jsHandler) ? '
					 	try {
					 		' . $this->jsHandler . '
						} catch(e){
							console.error("jsHandler error", e);
						}
						return false;' : 'return true;') . '
					}
				
				}
				
				/*************** DOCUMENT TRIGGERS - JAVASCRIPT *******************/
				$().ready(function(){
					
					//check all input variants
					$(\'input[type=text], input[type=password], input[type=email], input[type=search], input[type=hidden]\', \'#' . $this->form[self::NAME] . '_form_id\').focus(function(){

						if($(this).val() == $(this).prop(\'title\')){ $(this).val(\'\'); $(this).addClass(\'touched\').removeClass(\'untouched\'); }
											
					});
					
						//check all textareas
					$(\'textarea\', \'#' . $this->form[self::NAME] . '_form_id\').focus(function(){
					
						if($(this).val() == $(this).prop(\'title\')){ $(this).val(\'\'); $(this).addClass(\'touched\').removeClass(\'untouched\'); }
						
					});					
					
					//on blur
					$(\'input[type=text], input[type=password], input[type=email], input[type=search]\', \'#' . $this->form[self::NAME] . '_form_id\').blur(function(){
						
						//get field status
						hook' . $this->form[self::MD5NAME] . 'getFieldStatus($(this).prop(\'name\'), $(this).val());
						if($(this).val() == \'\'){ $(this).val($(this).prop(\'title\')); $(this).addClass(\'untouched\').removeClass(\'touched\'); }
						if($(this).val() == $(this).prop(\'title\')){ $(this).addClass(\'untouched\').removeClass(\'touched\'); }
						
					});
					
					//on keyup
					$(\'input[type=text], input[type=password], input[type=email], input[type=search]\', \'#' . $this->form[self::NAME] . '_form_id\').keyup(function(){
						
						//get field status
						hook' . $this->form[self::MD5NAME] . 'getFieldStatus($(this).prop(\'name\'), $(this).val());
						$(this).addClass(\'touched\').removeClass(\'untouched\');
					
					});
					
					//on blur
					$(\'textarea\', \'#' . $this->form[self::NAME] . '_form_id\').blur(function(){
						
						//get field status
						hook' . $this->form[self::MD5NAME] . 'getFieldStatus($(this).prop(\'name\'), $(this).val());
						if($(this).val() == \'\'){ $(this).val($(this).prop(\'title\')); $(this).addClass(\'untouched\').removeClass(\'touched\'); }
						if($(this).val() == $(this).prop(\'title\')){ $(this).addClass(\'untouched\').removeClass(\'touched\'); }
						
					});
					
					//on keyup
					$(\'textarea\', \'#' . $this->form[self::NAME] . '_form_id\').keyup(function(){
						
						//get field status
						hook' . $this->form[self::MD5NAME] . 'getFieldStatus($(this).prop(\'name\'), $(this).val());
						
					});
					
					//select on blur
					$(\'select\', \'#' . $this->form[self::NAME] . '_form_id\').blur(function(){
						
						//get field status
						hook' . $this->form[self::MD5NAME] . 'getFieldStatus($(this).prop(\'name\'), $(this).children(\'option:selected\').val());
					
					});
					
					//select on change
					$(\'select\', \'#' . $this->form[self::NAME] . '_form_id\').change(function(){
						
						//get field status
						hook' . $this->form[self::MD5NAME] . 'getFieldStatus($(this).prop(\'name\'), $(this).children(\'option:selected\').val());
					
					});
					
				});

            ';

		/**
		 * Adding request scoped script
		 */
		CoreScript::appendRequestScopedScript($return, $this->form[self::NAME]);

	}

	/**
	 * Returns full form
	 * @return string Full form
	 */
	public function getFullForm(){
		
		//return full form
		return self::buildFormHeader() . self::printFormFields() . self::buildFormFooter();
		
	}

	/**
	 * Returns form header
	 * @return string Returns form header
	 */	
	public function buildFormHeader(){
		
		//print form header
        $attr_string = self::EMPTY_STRING;
		if(!empty($this->form[self::ATTR])){
            foreach($this->form[self::ATTR] as $key => $value){
                $attr_string .= $key . '="' . $value . '" ';
            }
        }

        //return form and form identifier
        return self::buildJavascriptHandlers() . '<form action="' . $this->form[self::ACTION] . '" method="' . $this->form[self::METHOD] . '" name="' . $this->form[self::NAME] . '" id="' . $this->form[self::NAME] . '_form_id" ' . $attr_string . ' onsubmit="javascript: return hook' . $this->form[self::MD5NAME] . 'submitForm();">' .
			CoreHtmlUtils::input(null, array(self::TYPE => 'hidden', self::NAME => self::FORM_ . $this->form[self::NAME] . self::_STAMP, self::VALUE => md5(CoreVisitor::getId())));
			
	}

	/**
	 * Returns form field template
	 * @return string Returns form field template
	 */	
	public function renderOutput(){

		//cut short
		if(empty($this->form_element[self::TEMPLATE])){ return null; }

		//sanity check
		$rendered = CoreTemplate::render($this->form_element[self::TEMPLATE], $this->form_element);
		if(!$rendered){
			CoreLog::error('Unable to render field using template: ' . $this->form_element[self::TEMPLATE] . ' do you have From as a dependency? Are you trying to define a form in the public function register() method?');
		}

		//save output
		return $this->form_element[self::OUTPUT] = $rendered;
		
	}

	/**
	 * Returns form footer
	 * @return string Returns form footer
	 */
	public function buildFormFooter(){
		return '</form>';
	}
	
	/**
	 * Builds the field, assembles all the parts
	 * @return string Returns form field holder
	 */
	public function buildField(){
		
		//build template and insert field
		self::renderOutput();
		
		//add to stack
		$this->form_elements[] = $this->form_element;
		
		//return the element
		return $this->form_element;
		
	}

	/**
	 * Combines and returns all fields
	 * @return string Returns all form fields in order
	 */
	public function printFormFields(){
		$return = self::EMPTY_STRING;
		if(isset($this->form_elements) && !empty($this->form_elements)){
			foreach($this->form_elements as $element){
				$return .= $element[self::OUTPUT];
			}
		}
		return $return;
	}

	/**
	 * Gets a specific field
	 * @param string $name Name of field to get
	 * @return string Returns field string or false when not found
	 */
	public function grabField($name = null){
		if(isset($this->form_elements) && !empty($this->form_elements)){
			foreach($this->form_elements as $element){
				if($name == $element[self::NAME]){
					return $element[self::OUTPUT];
				}
			}
		}
		return false;
	}

	/**
	 * Gets a specific field
	 * @param string $name Name of field to get
	 * @param bool $append Look for append
	 * @return array Returns field array or false when not found
	 */
	public function grabFieldArray($name = null, $append = false){
		if(isset($this->form_elements) && !empty($this->form_elements)){
			foreach($this->form_elements as $element){
				if($name == $element[self::NAME] || ($append && substr($element[self::NAME], -strlen($name)) == $name)){
					return $element;
				}
			}
		}
		return false;
	}

	/**
	 * Grab field value
	 * @param string $name Field name
	 * @return string Submitted value
	 */
	public function grabFieldValue($name){
		$field = self::grabFieldArray($name);
		return isset($field[self::SUBMITTED_VALUE]) ? $field[self::SUBMITTED_VALUE] : false;
	}

	/**
	 * Validates form submission
	 * @return bool Returns true when valid submission and false when not
	 */
	public function validateSubmission(){
		
		//lets assume we are all good
		$problems = false;
		
		//go trough fields and check
		if(isset($this->form_elements) && !empty($this->form_elements)){
			foreach($this->form_elements as $key => $element){
				
				//assign submitted value
				if(strtolower($this->form[self::METHOD]) == self::GET){
					$this->form_elements[$key][self::SUBMITTED_VALUE] = $element[self::SUBMITTED_VALUE] = isset($_GET[self::buildFieldName($element)]) ? self::handleValue($element, $_GET[self::buildFieldName($element)]) : (isset($element[self::DEFAULT_VALUE]) ? $element[self::DEFAULT_VALUE] : null);
				}else{
					$this->form_elements[$key][self::SUBMITTED_VALUE] = $element[self::SUBMITTED_VALUE] = isset($_POST[self::buildFieldName($element)]) ? self::handleValue($element, $_POST[self::buildFieldName($element)]) : (isset($element[self::DEFAULT_VALUE]) ? $element[self::DEFAULT_VALUE] : null);
				}

                //field problems
				$field_problem = false;
				
				//handle field condition
				/**
				 * TODO: Find more elegant way to capture this logic in the Form module instead
				 */
				switch(true){

					//altcaptcha
					case ((strpos($element[self::TYPE], self::ALT_CAPTCHA) !== false)):

						$altcaptcha_field = self::grabFieldArray($element[self::NAME], true);

						//check supplied secret key
						if(!isset($_SESSION[self::FORM][self::ALT_CAPTCHA][$element[self::NAME]]) || $_SESSION[self::FORM][self::ALT_CAPTCHA][$element[self::NAME]] != $altcaptcha_field[self::SUBMITTED_VALUE]){
							CoreNotification::set('Altcaptcha validation failed', CoreNotification::ERROR);
							$field_problem = true;
							$problems = true;
						}

					break;

					//captcha
					case ((strpos($element[self::TYPE], self::CAPTCHA) !== false)):
					
						//grab the other entry
						$captcha_field = self::grabFieldArray($element[self::NAME], true);
					
						//captcha check
						if(!isset($_SESSION[self::CAPTCHA]) || strtolower(trim($_SESSION[self::CAPTCHA])) != strtolower(trim($captcha_field[self::SUBMITTED_VALUE]))){
							CoreNotification::set('Captcha validation failed', CoreNotification::ERROR);
							$field_problem = true;
							$problems = true;	
						}
					
					break;
					
					//password repeat
					case (substr($element[self::NAME], -15) == self::PASSWORD_REPEAT):
						
						//grab the other entry
						$password_field = self::grabFieldArray(self::PASSWORD, true);

						//check for a match
						if(isset($password_field[self::SUBMITTED_VALUE]) && CoreSecUtils::preparePassword($password_field[self::SUBMITTED_VALUE]) != $element[self::SUBMITTED_VALUE]){
							$field_problem = true;
							$problems = true;
						}
						
					break;
					
					//needs to match pattern
					case (preg_match('/^\/.*?\/[a-z]*$/i', $element[self::CONDITION])):
					
						//try match pattern
						if(!preg_match($element[self::CONDITION], $element[self::SUBMITTED_VALUE])){
							$field_problem = true;
							$problems = true;
						}
						
					break;
					
					//alternate validator
					case (preg_match('/^[a-z0-9_\-\.]+$/i', $element[self::CONDITION])):
						
						//check validator
						if(!self::validate($element[self::CONDITION], $element[self::SUBMITTED_VALUE])){
							$field_problem = true;
							$problems = true;
						}
						
					break;

					//Just needs to be present
					case ($element[self::CONDITION] == '*'):
						
						//if we don't have anything
						if(empty($element[self::SUBMITTED_VALUE])){
							$field_problem = true;
							$problems = true;
						}
						
					break;
									
				}
				
				//remove the problem .. just like that
				if($field_problem){
										
					//set notification
					CoreNotification::set(isset($element[self::HELPER]) ? $element[self::HELPER] : $element[self::LABEL] . ' entry invalid', CoreNotification::ERROR);	
				
					//remove it
					unset($this->form_elements[$key][self::SUBMITTED_VALUE]);
				
				}
			}
		}
		
		//return
		return ($problems === false);
	
	}
	
	/**
	 * Validate value 
	 * @param String $condition Condition string
	 * @param String $value Value string
	 * @return Boolean
	 */
	private function validate($condition = null, $value = null){
		
		/**
		 * Lookup condition
		 */
		switch($condition){
		
			//password
			case self::PASSWORD:
				return !empty($value);
			break;
			
			//no match to condition found
			default:
				return true;
			break;
			
		}
		
	}

	/**
	 * Returns submitted values
	 * @return array Array of submitted values
	 */	
	public function getFormValues(){
		
		//quick sanity check
		if(!isset($this->form_elements) or empty($this->form_elements)){ return false; }
		
		//values
		$return = array();
		
		//build array
		foreach($this->form_elements as $element){
			$return[$element[self::NAME]] = $element[self::SUBMITTED_VALUE];
		}
		
		//return values
		return $return;
		
	}

}