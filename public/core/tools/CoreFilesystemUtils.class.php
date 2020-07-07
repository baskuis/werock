<?php

/**
 * Core FS Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreFilesystemUtils {

	const DOT = '.';
	const SLASH = '/';

	/**
	 * Read folder
	 *
	 * @param String $path
	 * @return Array $return
	 */
	public static function readFolders($path = null){
		$return = array();
		if(false !== ($directories = @opendir($path))){
			while($dir = readdir($directories)){
				if(substr($dir, 0, 1) == self::DOT){ continue; }
				if(is_dir($path . self::SLASH . $dir)){
					array_push($return, $dir);
				}
			}
			closedir($directories);
		}
		return $return;
	}

	/**
	 * Read files
	 *
	 * @param String $folder
	 * @return Array $return
	 */	
	public static function readFiles($path = null){
		$return = array();
		if(false !== ($files = @opendir($path))){
			while($file = readdir($files)){
				if(substr($file, 0, 1) == self::DOT){ continue; }
				if(is_file($path . self::SLASH . $file)){
					array_push($return, $file);
				}
			}
			closedir($files);
		}
		return $return;
	}
	
}