<?php

/**
 * Core Remote Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreRemoteUtils {

    const PHP_SAPI_CLI = 'cli';
    const HTTP_USER_AGENT = 'user_agent';
    const GBOT_SAFE_UA = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';

    /**
     * Get remote contents
     *
     * @param string $url
     * @param string $userAgent
     * @param array $headers
     * @return bool|mixed
     */
    public final static function getRemoteContents($url, $userAgent = null, $headers = array()){
        if(!$url) CoreLog::error('Need url to get remote contents');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, (!empty($userAgent) ? $userAgent : self::GBOT_SAFE_UA));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_ACCEPT_ENCODING, 1);
        if ($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);
        curl_close($ch);
        if($output){
            return $output;
        }else{
            return false;
        }
    }

}