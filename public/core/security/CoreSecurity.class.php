<?php

/**
 * Core Security
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreSecurity {

    /**
     * Security constants
     */
    const ACCESS_TOKEN_SESSION_KEY = 'accessToken';
    const ACCESS_TOKEN_COOKIE_KEY = 'werockAccessToken';
    const COOKIE_PATH = '/';
    const MAX_VALID_ACCESS_TOKENS = 30;

    /**
     * Access token
     *
     * @var string $apiToken
     */
    private static $accessToken;

    /**
     * Valid entry status
     * @var bool
     */
    private static $entry = false;

    /**
     * CSRF check enabled
     *
     * @var bool
     */
    public static $checkCSRFTokenEnabled = true;

    /**
     * Get entry
     *
     * @return bool
     */
    public static function getEntry(){
        return self::$entry;
	}

    /**
     * Set entry
     *
     * @param bool $entry
     */
    public static function setEntry($entry = false){
		self::$entry = $entry;
	}

    /**
     * Do we have a valid entry
     *
     * @return bool
     */
    public static function validEntry(){
        if(false === self::$entry){
            CoreLog::error('Did not find valid entry point!');
        }
        return self::$entry;
    }

    /**
     * Generate access token
     *
     * @return string
     */
    public static function generateAccessToken(){
        if(!self::$checkCSRFTokenEnabled) return null;
        if(!CoreSessionUtils::sessionStarted()) CoreLog::error('No session started');
        self::$accessToken = md5(HOST_NAME . mktime() . rand());
        if(!isset($_SESSION[self::ACCESS_TOKEN_SESSION_KEY]) || !is_array($_SESSION[self::ACCESS_TOKEN_SESSION_KEY])){
            $_SESSION[self::ACCESS_TOKEN_SESSION_KEY] = array();
        }
        array_push($_SESSION[self::ACCESS_TOKEN_SESSION_KEY], self::$accessToken);
        if(sizeof($_SESSION[self::ACCESS_TOKEN_SESSION_KEY]) > self::MAX_VALID_ACCESS_TOKENS){
            array_shift($_SESSION[self::ACCESS_TOKEN_SESSION_KEY]);
        }
        setcookie(self::ACCESS_TOKEN_COOKIE_KEY, self::$accessToken, 0, self::COOKIE_PATH);
        return self::$accessToken;
    }

    /**
     * Get access token
     *
     * @return string
     */
    private static function getAccessTokens()
    {
        return isset($_SESSION[self::ACCESS_TOKEN_SESSION_KEY]) ? $_SESSION[self::ACCESS_TOKEN_SESSION_KEY] : self::$accessToken;
    }

    /**
     * Check access token
     *
     * @throws Exception
     */
    public static function checkAccessToken(){
        if(!self::$checkCSRFTokenEnabled) return;
        $tokens = self::getAccessTokens();
        if(empty($tokens) || !isset($_COOKIE[self::ACCESS_TOKEN_COOKIE_KEY]) || !in_array($_COOKIE[self::ACCESS_TOKEN_COOKIE_KEY], $tokens)){
            throw new CoreSecurityApiException('Invalid access token');
        }
    }

}