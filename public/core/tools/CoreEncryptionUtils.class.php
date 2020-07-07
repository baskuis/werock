<?php

/**
 * Core Encryption Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreEncryptionUtils {

    public static $cacheNamespace = array('werock', 'encryption');

    /**
     * Encrypt String
     *
     * @param null $string
     * @param $salt
     * @return mixed|string
     */
    public static function encryptString($string = null, $salt = SHORT_SALT){

        //create a key
        $key = 'encrypt:' . md5($string . $salt);

        //try to get cached version
        $val = CoreCache::getCache($key, true, self::$cacheNamespace);

        //return cached
        if(!empty($val)){

            //return value
            return $val;

        }else{

            //do the encryption
            $val = rawurlencode(trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $string, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))));

            //save cache
            CoreCache::saveCache($key, $val, 86400, self::$cacheNamespace);

            //return value
            return $val;

        }

    }

    /**
     * Decrypt String
     *
     * @param $string
     * @param $salt
     * @return mixed|string
     */
    public static function decryptString($string, $salt = SHORT_SALT){

        //create a key
        $key = 'decrypt:' . md5($string . $salt);

        //try to get cached version
        $val = CoreCache::getCache($key, true, self::$cacheNamespace);

        //return cached
        if(!empty($val)){

            //return value
            return $val;

        }else{

            //de the decryption
            $val = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode(rawurldecode($string)), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));

            //save cache
            CoreCache::saveCache($key, $val, 86400, self::$cacheNamespace);

            //return value
            return $val;

        }

    }

}