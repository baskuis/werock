<?php

/**
 * Collecting .. what would be functions inside a static class
 * prevents collisions
 */
class CoreStringUtils {

    /**
     * Constants
     */
    const EMPTY_STRING = '';
    const DOUBLE_QUOTE = '"';
    const DEFAULT_STRING_END = '...';
    const DEFAULT_UTF8_ENCODING = 'UTF-8';
    const DEFAULT_UTF8_ENCODING_IGNORE = 'UTF-8//TRANSLIT';
    const REGULAR_EXPRESSION_WHITESPACE = '/[\s\t\n]+/';
    const ONE_SPACE = ' ';
    const DASH = '-';
    const UNDERSCORE = '_';

    /**
     * HTML attribute string
     *
     * @param null $string
     * @return string
     */
    public static function htmlAttribute($string = null){
        return htmlentities($string, ENT_NOQUOTES, CoreStringUtils::DEFAULT_UTF8_ENCODING, false);
    }

	/**
	 * Make string save for tag
	 * @param String $string String to make safe for html tag
	 * @param String $strip What character to strip
	 * @return String Tag save string
	 */
	public static function strip($string = null, $strip = self::DOUBLE_QUOTE){
		return trim(str_replace($strip, self::EMPTY_STRING, $string));
	}

	/**
	 * Make string save for tag
	 * @param String JS string
	 * @param String $delimiter Delimiter " or '
	 * @return String JS save string
	 */	
	public static function jsString($string = null, $delimiter = self::DOUBLE_QUOTE){
		return trim(preg_replace(self::REGULAR_EXPRESSION_WHITESPACE, self::ONE_SPACE, str_replace(array('\\', $delimiter), array('\\\\', '\\' . $delimiter), $string)));
	}

	/**
	 * Salt string
	 * @param string $string String to be salted
	 * @return string hashed string
	 */
	public static function saltString($string = null, $salt = null){
		if(empty($string) || empty($salt)){ return false; }
		return md5(md5($string . $salt) . $salt);
	}

    /**
     * Limits string to certain length then appends with $string_end
     *
     * @param null $string
     * @param int $limit
     * @param string $string_end
     * @return null|string
     */
    public static function limitString($string = null, $limit = 30, $string_end = self::DEFAULT_STRING_END){
        if(empty($string)){ return null; } if(!is_numeric($limit)){ return null; }
        return (strlen($string) > $limit) ? substr($string, 0, (int)$limit) . $string_end : $string;
    }

    /**
     * Compresses string to certain length by inserting $compress_string in middle
     *
     * @param null $string
     * @param int $limit
     * @param string $compress_string
     * @return null|string
     */
    public static function compressString($string = null, $limit = 30, $compress_string = self::DEFAULT_STRING_END){
        if(empty($string)){ return null; } if(!is_numeric($limit)){ return null; }
        return (strlen($string) > $limit) ? substr($string, 0, ((int)$limit/2) - strlen($compress_string)) . $compress_string . substr($string, -((int)$limit/2)) : $string;
    }

    /**
     * Convert timestamp to time ago
     * NOTE: make multi lingual
     *
     * @param $then
     * @return string
     */
    public static function convertToTimeAgo($then = null){
        if(!is_numeric($then)){ $then = strtotime($then); }	$now = time(); $time_difference = $now - $then;
        if(($time_difference / ( 60 * 60 * 24 * 365 * 1 )) >=  1){
            $years = round($time_difference / ( 60 * 60 * 24 * 365 * 1 )); $return = $years . ' year'; if($years > 1){ $return .= 's'; }
        }elseif(($time_difference / ( 60 * 60 * 24 * 30 * 1 )) >= 1){
            $months = round($time_difference / ( 60 * 60 * 24 * 30 * 1 )); $return = $months . ' month'; if($months > 1){ $return .= 's'; }
        }elseif(($time_difference / ( 60 * 60 * 24 * 7 * 1 )) >= 1){
            $weeks = round($time_difference / ( 60 * 60 * 24 * 7 * 1 )); $return = $weeks . ' week'; if($weeks > 1){ $return .= 's'; }
        }elseif(($time_difference / ( 60 * 60 * 24 * 1 )) >= 1){
            $days = round($time_difference / ( 60 * 60 * 24 * 1 )); $return = $days . ' day'; if($days > 1){ $return .= 's'; }
        }elseif(($time_difference / ( 60 * 60 * 1 )) >= 1){
            $hours = round($time_difference / ( 60 * 60 * 1 )); $return = $hours . ' hour'; if($hours > 1){ $return .= 's'; }
        }elseif(($time_difference / ( 60 * 1 )) >= 1){
            $minutes = round($time_difference / ( 60 * 1 )); $return = $minutes . ' minute'; if($minutes > 1){ $return .= 's'; }
        }else{
            $return = $time_difference . ' seconds';
        }
        return $return;
    }

    /**
     * Evaluate boolean string
     *
     * @param null $value
     * @return bool
     */
    public static function evaluateBoolean($value = null){
        switch(true){
            case (strtolower(trim($value)) == 'yes'):
                return true;
            case (strtolower(trim($value)) == 'true'):
                return true;
            case (strtolower(trim($value)) == '1'):
                return true;
            case (strtolower(trim($value)) == 'on'):
                return true;
            case (strtolower(trim($value)) == 'y'):
                return true;
            case (strtolower(trim($value)) == 't'):
                return true;
        }
        return false;
    }

    /**
     * Get format bytes
     *
     * @param $bytes
     * @param int $precision
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . self::ONE_SPACE . $units[$pow];
    }

    /**
     * Prepare for use as url
     *
     * @param null $string
     * @param string $char
     * @return string
     */
    public static function url($string = null, $char = '-'){
        return strtolower(str_replace(self::ONE_SPACE, $char, trim(preg_replace('/[^a-z^A-Z^0-9]+/', self::ONE_SPACE, $string))));
    }

    /**
     * Normalize string from url
     *
     * @param null $string
     * @param string $char
     * @return string
     */
    public static function unurl($string = null, $char = '-'){
        return trim(str_replace($char, self::ONE_SPACE, urldecode($string)));
    }

    /**
     * Encode string
     *
     * @param null $text
     * @param null $encoding
     * @return string
     */
    public static function encodeStringTo($text = null, $encoding = null){
        return @iconv(mb_detect_encoding($text, mb_detect_order(), true), $encoding, $text);
    }

    /**
     * Encode string to UTF-8
     *
     * @param null $text
     * @return string
     */
    public static function encodeStringToUTF8($text = null){
        return self::encodeStringTo($text, self::DEFAULT_UTF8_ENCODING_IGNORE);
    }

    /**
     * Are strings similar? Does a contain b, or does b contain a?
     * No case sensitive. Normalizes white-space.
     *
     * @param string $a
     * @param string $b
     * @return float
     */
    public static function similar($a = null, $b = null){
        $a = trim(preg_replace(self::REGULAR_EXPRESSION_WHITESPACE, self::ONE_SPACE, $a));
        $b = trim(preg_replace(self::REGULAR_EXPRESSION_WHITESPACE, self::ONE_SPACE, $b));
        if(strtolower($a) == strtolower($b)) return 1;
        if(stripos($b, $a) > -1 || stripos($a, $b) > -1) return (strlen($a) + strlen($a) - abs(strlen($a) - strlen($b))) / (strlen($a) + strlen($a));
        similar_text($a, $b, $percentage);
        return $percentage / 100;
    }

    /**
     * Underscore to camelcase
     *
     * @param $string
     * @param bool $capitalizeFirstCharacter
     * @return mixed
     */
    public static function underscoresToCamelCase($string, $capitalizeFirstCharacter = false){
        $str = str_replace(self::ONE_SPACE, self::EMPTY_STRING, ucwords(str_replace(self::UNDERSCORE, self::ONE_SPACE, $string)));
        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }
        return $str;
    }

    /**
     * Dashes to camelcase
     *
     * @param $string
     * @param bool $capitalizeFirstCharacter
     * @return mixed
     */
    public static function dashesToCamelCase($string, $capitalizeFirstCharacter = false){
        $str = str_replace(self::ONE_SPACE, self::EMPTY_STRING, ucwords(str_replace(self::DASH, self::ONE_SPACE, $string)));
        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }
        return $str;
    }

    /**
     * Object to attribute
     *
     * @param null $object
     * @return mixed
     */
    public static function objectToEncodedAttributeString($object = null){
        $encoded = !empty($object) ? json_encode($object) : json_encode(new stdClass());
        return str_replace(array('\'', '"'), array('\\\'', '\''), preg_replace('/[\s\t\n]+/', ' ', preg_replace('/&[#a-z0-9]{2,4}\;/', ' ', $encoded)));
    }

    /**
     * Compare string a to b ignoring whitespace wrap and casing
     *
     * @param null $a
     * @param null $b
     * @return bool
     */
    public static function compare($a = null, $b = null){
        return (trim(strtolower($a)) == trim(strtolower($b)));
    }

    /**
     * Show if condition is met
     *
     * @param bool|false $condition
     * @param string $string
     * @return string
     */
    public static function showIf($condition = false, $string = null){
        if($condition){
            return $string;
        }
        return self::EMPTY_STRING;
    }

    /**
     * Does a contain b or b contain a
     *
     * @param null $a
     * @param null $b
     * @return bool
     */
    public static function contains($a = null, $b = null){
        if(false !== stripos($a, $b)){
            return true;
        }
        if(false !== stripos($b, $a)){
            return true;
        }
        return false;
    }

}