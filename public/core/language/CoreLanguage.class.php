<?php

/**
 * Core Language
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */ 
class CoreLanguage {

    /**
     * Allow interception
     */
    use CoreInterceptorTrait;

	/**
	 * Language
	 */
	const LANGUAGES_FOLDER = 'languages';
	const LANGUAGES_APPEND = '.lang.php';
	
	const LANGUAGE_PREFIX = 'WELANG';

	/**
	 * Cache
	 */
	const CACHE_LANGUAGE_KEY = 'language';
	const CACHE_LANGUAGE_NS = 'language';

	/**
	 * Constants
	 */
	const SLASH = '/';
	
	/**
	 * Language
	 */
	public static $language = DEFAULT_LANGUAGE;
	
	/**
	 * Language reference
	 */
	public static $reference = array();

	/**
	 * Set language
	 *
	 * @param string $lang
	 * @return bool
	 */
	public static function setLanguage($lang = DEFAULT_LANGUAGE){
		
		//sanity check
		if(empty($lang)){
			return false;
		}
	
		//language
		self::$language = $lang;
		
		//all good
		return true;
		
	}
	
	/**
	 * Define reference
	 */
	public static function set($key = null, $lang = null, $value = null){
		
		//set reference		
		try {
			
			//store in reference
			self::$reference[$lang][$key] = $value;
						
			//return true
			return true;
	
		} catch (Exception $e){
			
			//handle error
			CoreLog::error($e->getMessage());
			
		}
		
		//return false	
		return false;
	
	}

    /**
     * Get translation
     * allow interception
     *
     * @param null $key
     * @return null
     */
    public static function _get($key = null){
		return isset(self::$reference[self::$language][$key]) ? self::$reference[self::$language][$key] : $key;
	}

    /**
     * Register languages or restore from cache
     */
	public static function registerLanguages(){

		self::$reference = CoreCache::getCache(self::CACHE_LANGUAGE_KEY, true, array(self::CACHE_LANGUAGE_NS), false);

		if(!empty(self::$reference)) return;

		self::$reference = array();

		/** @var CoreModuleObject $coreModuleObject */
		foreach(CoreModule::$modules as $coreModuleObject){
			if($coreModuleObject->getLanguages() != null){
				foreach($coreModuleObject->getLanguages() as $languageFile){
					require $languageFile;
				}
			}
		}

		CoreCache::saveCache(self::CACHE_LANGUAGE_KEY, self::$reference, 86400, true, array(self::CACHE_LANGUAGE_NS), false);

	}

	/**
	 * Load languages
	 *
	 * @param CoreModuleObject $CoreModuleObject
	 * @return array|bool
	 */
	public static function loadLanguages(CoreModuleObject $CoreModuleObject){

		$path = $CoreModuleObject->getPath();

		$languages = CoreFilesystemUtils::readFiles($path . self::SLASH . self::LANGUAGES_FOLDER);
		
		if(empty($languages)){
			return false;
		}

		$response = array();
		
		//load languages
		foreach($languages as $language){
			try {
				if(false === strpos($language, self::LANGUAGES_APPEND)) continue;
				array_push($response, $path . self::SLASH . self::LANGUAGES_FOLDER . self::SLASH . $language);
			} catch(Exception $e){
				CoreLog::set($e);
			}
		}

		return $response;

	}
	
	/**
	 * Build languages string
	 */
	private static function buildLanguagesString(){
		
		//open block
		$string = 'var ' . self::LANGUAGE_PREFIX . '={};';

        //only when language entries are defined
        if(!empty(self::$reference[self::$language])){

            //add entries
            foreach(self::$reference[self::$language] as $key => &$value){
                $string .= self::LANGUAGE_PREFIX . '["' . $key . '"]="' . CoreStringUtils::jsString(self::get($key)) . '";';
            }

            //clean up
            unset($value);

        }

		//give it back
		return $string;
		
	}
	
	/**
	 * Stack Language
	 */
	public static function stackLanguages(){
		
		//check
		if(false !== ($string = self::buildLanguagesString())){
		
			//load script string
			return CoreScript::loadScriptString($string);
		
		}
		
		//something went wrong
		return false;		
		
	}
	
}