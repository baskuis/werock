<?php

/**
 * Core Security Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreSecUtils { 

	const REMOTE_ADDR = 'REMOTE_ADDR';
	const HTTP_USER_AGENT = 'HTTP_USER_AGENT';
	const PERIOD = '.';
	const CLI = 'cli';
	const SHA512 = 'sha512';

	static private $octals;
	static public $remoteIp;

	/**
	 * Prepare password
	 *
	 * @param String $string
	 * @return String hashed
	 */
	public static function preparePassword($string = null){
		return hash(self::SHA512, $string);
	}
	
	/**
	 * Generate key
	 *
	 * @return String Generate key
	 */
	public static function generateKey(){
		return md5(time() . rand());
	}

    /**
     * Get remote ip
     */
    public static function getRemoteIp(){
		if(empty(self::$remoteIp)) self::$remoteIp = isset($_SERVER[self::REMOTE_ADDR]) ? $_SERVER[self::REMOTE_ADDR] : false;
        return self::$remoteIp;
    }

	/**
	 * Is client
	 *
	 * @return bool
	 */
	public static function isCli(){
		return php_sapi_name() == self::CLI;
	}

    /**
     * Get user agent
     *
     * @return mixed
     */
    public static function getUserAgent(){
        return isset($_SERVER[self::HTTP_USER_AGENT]) ? $_SERVER[self::HTTP_USER_AGENT] : false;
    }

    /**
     * Return first two ocals of requestor ip
     *
     * @return string
     */
    public static function getRemoteIp2Octals(){
       	$ip = self::getRemoteIp();
		if(empty(self::$octals)) self::$octals = substr($ip, 0, strrpos($ip, self::PERIOD, strrpos($ip, self::PERIOD, 0) - strlen($ip) - 1));
       	return self::$octals;
    }
	
}