<?php

/**
 * Language Module
 * Stores language strings the database and allows overriding of language in database
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */ 
class LanguageModule implements CoreModuleInterface {
	
	/**
	 * Module description
	 */
	public static $name = 'Language Module';
	public static $description = 'Allows language reference to be stored in the database and edited';
	public static $version = '1.0.1';
	public static $dependencies = array(
		'MapTable' => array(
			'min' => '1.0.0',
			'max' => '1.9.9'
		)
	);
	
	/**
	 * Mysql queries
	 */
	const GET_TRANSLATION_QUERY = " SELECT `werock_language_value` FROM `werock_language` WHERE `werock_language_key` = :key AND `werock_language_language` = :language; ";
	const INSERT_TRANSLATION_QUERY = " INSERT INTO `werock_language` (`werock_language_key`, `werock_language_value`, `werock_language_language`, `werock_language_date_added`) VALUES (:key, :value, :language, NOW()); ";

	/** @var MapTableService $MapTableService */
	private static $MapTableService;

	/**
	 * Get listeners
	 *
	 * @return mixed
	 */
	public static function getListeners()
	{

		$listeners = array();

		/**
		 * MapTable listener
		 */
		if(DEV_MODE || CACHING_ENABLED){
			array_push($listeners, new CoreObserverObject(MapTableModule::EVENT_UPDATED, __CLASS__, 'clearCaches'));
			array_push($listeners, new CoreObserverObject(MapTableModule::EVENT_INSERTED, __CLASS__, 'clearCaches'));
			array_push($listeners, new CoreObserverObject(MapTableModule::EVENT_DELETED, __CLASS__, 'clearCaches'));
		}

		return $listeners;

	}

	/**
	 * Get interceptors
	 *
	 * @return mixed
	 */
	public static function getInterceptors()
	{
		$interceptors = array();
		array_push($interceptors, new CoreInterceptorObject('CoreLanguage', 'get', __CLASS__, 'findInDatabase', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));
		return $interceptors;
	}

	/**
	 * Get menus
	 *
	 * @return mixed
	 */
	public static function getMenus()
	{

	}

	/**
	 * Get routes
	 *
	 * @return mixed
	 */
	public static function getRoutes()
	{

	}

	/**
	 * Register the module
	 * register, menu, routes, interceptors, observers
	 *
	 * @return mixed
	 */
	public static function __register__()
	{

	}

	/**
	 * UserRegisterAction listeners, toMethod
	 */	
	public static function __init__(){

		self::$MapTableService = CoreLogic::getService('MapTableService');

		/** Maptable columns */
		self::mapTableColumns();

	}

	public static final function mapTableColumns(){

		/** @var MapTableMapColumnObject $MapTableMapColumnObject */
		$MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
		$MapTableMapColumnObject->setId('languagekey');
		$MapTableMapColumnObject->setAppendMatch(array('language'));
		$MapTableMapColumnObject->setDataTypeMatch('/^varchar/i');
		$MapTableMapColumnObject->setInputTemplate('formselectchosen');
		$MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
		$MapTableMapColumnObject->setOptionMapper(function(MapTableColumnObject $MapTableColumnObject, MapTableContextObject $MapTableContextObject){
            $options = array();
			$languages = CoreLanguageUtils::getLangaguesISO639();
			foreach($languages as $key => $value){
				$option = new FormFieldOption();
				$option->setKey($key);
				$option->setValue($value);
				array_push($options, $option);
			}
			return $options;
		});
		self::$MapTableService->addMapping($MapTableMapColumnObject);

	}
	
	/**
	 * Find language in database
	 */
	public static function findInDatabase($return = null, $params = array()){

		//do lookup only when needed
		if(isset($params[0])){

			$cacheKey = 'language:' . CoreLanguage::$language . ':' . $params[0];
			$cacheNS = 'languages';

			if(false !== ($value = CoreCache::getCache($cacheKey, true, $cacheNS, false))){
				return $value;
			}

			//lookup in database
			$record = CoreSqlUtils::row(self::GET_TRANSLATION_QUERY, array(
                ':key' => $params[0],
                ':language' => CoreLanguage::$language
            ));

			/**
			 * Now lets return the value we found - or create an entry .. to be updated
			 */
			if(isset($record['werock_language_value']) && array_key_exists('werock_language_value', $record)){
				CoreCache::saveCache($cacheKey, $record['werock_language_value'], 86400, true, $cacheNS, false);
				return $record['werock_language_value'];
			}else{

				//see if we can find the value
				$value = isset(CoreLanguage::$reference[CoreLanguage::$language][$params[0]]) ? CoreLanguage::$reference[CoreLanguage::$language][$params[0]] : $params[0];

				//create an entry
				CoreSqlUtils::insert(self::INSERT_TRANSLATION_QUERY, array(
                    ':key' => $params[0],
                    ':value' => $value,
                    ':language' => CoreLanguage::$language
                ));

				//return key as name
				return $value;
				
			}
		}
		
		//pass the buck
		return $return;
		
	}

	/**
	 * Clear language caches
	 * TODO: Needs debugging - not currently working correctly
	 * TODO: cached values still show up
	 *
	 * @param MapTableContextObject $mapTableContextObject
	 */
	public static function clearCaches(MapTableContextObject $mapTableContextObject){
		switch($mapTableContextObject->getTable()) {
			case 'werock_language':

				/**
				 * Drop languages cache
				 */
				CoreCache::deleteCache(CoreLanguage::CACHE_LANGUAGE_KEY, true, array(CoreLanguage::CACHE_LANGUAGE_NS));

				/**
				 * Rebuild languages cache
				 */
				CoreLanguage::registerLanguages();

			break;
		}
	}

	/**
	 * Run on update
	 *
	 * @param $previousVersion
	 * @param $newVersion
	 *
	 * @return void
	 */
	public static function __update__($previousVersion, $newVersion)
	{

	}

	/**
	 * Run on enable
	 *
	 * @return void
	 */
	public static function __enable__()
	{

	}

	/**
	 * Run on disable
	 *
	 * @return mixed
	 */
	public static function __disable__()
	{

	}

	/**
	 * Run on install
	 *
	 */
	public static function __install__()
	{

	}
	
}