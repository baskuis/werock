<?php

/**
 * Core FE Template
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */ 
class CoreFeTemplate {

    /**
     * Allow reflection
     */
    use ClassReflectionTrait;

    /**
     * Allow interceptors
     */
    use CoreInterceptorTrait;

	/**
	 * Define FE template namespace
	 */
	const FE_TEMPLATE_NAMESPACE = 'WRTemplates';
	
	/**
	 * Define FE template type declaration
	 */
	const FE_TEMPLATE_TYPE = 'fe';
	
	/**
	 * Define FE have prefix
	 */
	const FE_HAVE_PREFIX = 'have__';

	/**
	 * Cache
	 */
	const CACHE_FE_TEMPLATES_KEY = 'fetemplates';
	const CACHE_FE_TEMPLATES_NS = 'fetemplates';
	const CACHE_FE_CACHE_DURATION = 86400;

	/**
	 * Constants
	 */
	const CONST_DOT = '.';
	const CONST_COLON = ':';
	const CONST_NL = "\n";
	
	/**
	 * Templates string
	 *
	 * @var string $string
	 */
	private static $string;

	/**
	 * Lets keep track of rendered fe templates
	 */
	private static $feNamespaceDeclarations = array();
	
	/**
	 * Generate js friendly namespace
	 */
	private static function prepareNamespace($namespace = null){
		return self::FE_TEMPLATE_NAMESPACE . self::CONST_DOT . str_replace(self::CONST_COLON, self::CONST_DOT, $namespace);
	}
	
	/**
	 * Prepare namespace js
	 * @param String template namespace param
	 * @return Javascript string to assure template namespace is declared
	 */
	private static function prepareNamespaceJS($namespace = null){
		
		//return holder
		$return = 'if(' . self::FE_TEMPLATE_NAMESPACE . '===undefined){var ' . self::FE_TEMPLATE_NAMESPACE . '={};}';
		
		//break up namespace
		$pieces = explode(self::CONST_COLON, $namespace);

		//build out pieces
		foreach($pieces as $key => $piece){
			
			//namespace substring
			$js_namespace_string = self::FE_TEMPLATE_NAMESPACE . self::CONST_DOT;
			
			/**
			 * Build namespace substring
			 */			
			$i = 0;
			while($i < $key){
				$js_namespace_string .= $pieces[$i] . self::CONST_DOT;
				$i++;
			}
			$js_namespace_string .= $piece;
			
			//not needed again
			if(in_array($js_namespace_string, self::$feNamespaceDeclarations)){
				continue;
			}
			
			//lets remember it
			array_push(self::$feNamespaceDeclarations, $js_namespace_string);
			
			//append declaration
			$return .= 'if(' . $js_namespace_string . '==undefined){' . $js_namespace_string . '={};}';
			
		}
		
		//return javascript string
		return $return;
		
	}
	
	/**
	 * Build FE template string
	 */
	public static function buildFeTemplatesString(){

		$string = CoreCache::getCache(self::CACHE_FE_TEMPLATES_KEY, true, array(self::CACHE_FE_TEMPLATES_NS), false);

		if(!empty($string)){
			return $string;
		}

		//get templates
		$coreTemplates = CoreTemplate::getCoreTemplates();
		
		//check for templates
		if(empty($coreTemplates)){
			CoreLog::error("Unable to get templates!");
			return false;
		}

		//return
		$string = '';
		
		//step through the templates
        /* @var CoreTemplateObject $CoreTemplateObject */
		foreach($coreTemplates as $CoreTemplateObject){

			//make sure template can be found
			if(!is_file($CoreTemplateObject->getTemplatePath())){
				CoreLog::error("Unable to find template at: " . $CoreTemplateObject->getTemplatePath());
				continue;
			}

			//see if this a fe template
			if(!$CoreTemplateObject->hasType(self::FE_TEMPLATE_TYPE)){
				continue; //ignore .. no need to render this
			}

            //get view
            $view = self::getView($CoreTemplateObject);

			//check for template
			if(empty($view)){
				CoreLog::debug("Unable to find view in " . $CoreTemplateObject->getTemplatePath());
				continue;
			}
				
			//prepare namespace declaration
			$string .= self::prepareNamespaceJS($CoreTemplateObject->getNamespace());
			
			//load the template
			$string .= self::prepareNamespace($CoreTemplateObject->getNamespace()) . '.template="' . CoreStringUtils::jsString($view) . '";';
			$string .= self::prepareNamespace($CoreTemplateObject->getNamespace()) . '.render=function(data){return Mustache.to_html(' . self::prepareNamespace($CoreTemplateObject->getNamespace()) . '.template, data);};';

		}

		CoreCache::saveCache(self::CACHE_FE_TEMPLATES_KEY, $string, self::CACHE_FE_CACHE_DURATION, true, array(self::CACHE_FE_TEMPLATES_NS), false);

		return $string;
				
	}

    /**
     * Return view for template
     * Note: Allow interception
     *
     * @param CoreTemplateObject $CoreTemplateObject
     * @return null
     */
    public static function _getView(CoreTemplateObject $CoreTemplateObject){

        //require template
        require $CoreTemplateObject->getTemplatePath();

        //check for template
        if(!isset($view)) CoreLog::debug("Unable to find view in " . $CoreTemplateObject->getTemplatePath());

        //return view
        return isset($view) ? $view : null;

    }

	/**
	 * Render templates
	 * @return Boolean true or false
	 */	
	public static function stackFeTemplates(){
		
		//check
		if(false !== ($string = self::buildFeTemplatesString())){
		
			//load script string
			return CoreScript::loadScriptString($string);
		
		}
		
		//something went wrong
		return false;
		
	}
	
	/**
	 * Render templates
	 * @return String or false
	 */
	public static function renderFeTemplatesBlock(){
		
		//check
		if(false !== ($string = self::buildFeTemplatesString())){
		
			//return templates
			return '<script type="text/javascript">' . self::CONST_NL . $string . self::CONST_NL . '</script>';
		
		}
		
		//something went wrong
		return false;
		
	}
	
	/**
	 * UserRegisterAction Mustache JS
	 */
	public static function registerMustacheJS(){
		
		//register mustache template
		CoreScript::register('core/fetemplate/inc/Mustache.js');
		
	}

}