<?php

/**
 * Core Session Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreSessionUtils {
	
	/**
	 * See if valid session has been started
	 * @return boolean True is session is started, false otherwise
	 */
	public static function sessionStarted(){
		return isset($_SESSION);
	}
	
	/**
	 * Assure session has been started
	 * @return boolean True is session has been started, false otherwise
	 */
	public static function assureSession(){
		if(!self::sessionStarted()){ 
			if(!headers_sent()){

                /**
                 * Set hash function
                 */
                ini_set('session.hash_function', 'sha256');

                /** @var $CoreSessionHandler CoreSessionHandler */
                $CoreSessionHandler = new CoreSessionHandler();

                //configure session handler
                session_set_save_handler($CoreSessionHandler, true);

                //the following prevents unexpected effects when using objects as save handlers
                register_shutdown_function('session_write_close');

                //start a session
                return session_start();

			} 
		} 
		return true;
	}
	
}