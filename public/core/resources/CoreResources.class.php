<?php

/**
 * Core resources
 */
class CoreResources {

	use CoreInterceptorTrait;
	use ClassReflectionTrait;

	/**
	 * Resources folder
	 */
	const RESOURCES_FOLDER = "resources";
	
	/**
	 * Store resource
	 * @param String $filename
	 * @param String $data
	 * @return Mixed int or boolean
	 */
	public static function _store($filename = null, $data = null){
		
		//store success
		return file_put_contents(DOCUMENT_ROOT . self::RESOURCES_FOLDER . '/' . $filename, $data);
	
	}
	
	/**
	 * Store resource
	 * @param String $filename
	 * @return Mixed int or boolean
	 */
	public static function _exists($filename = null){
		
		//is if file exists
		return is_file(DOCUMENT_ROOT . self::RESOURCES_FOLDER . '/' . $filename);

		
	}
	
	/**
	 * Get path
	 * @param String $filename
	 * @return String resources path
	 */
	public static function _getPath($filename = null){

		return '/' . self::RESOURCES_FOLDER . '/' . $filename;

	}
	
}