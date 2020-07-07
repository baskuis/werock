<?php 

/**
 * Core URI Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreUriUtils { 

	const HTTP_PROTOCOL = 'http://';
	const HTTPS_PROTOCOL = 'https://';
	const PROTOCOL_SEPARATOR = '://';
	const PATH_SLASH = '/';
	const OFF = 'off';
	const ON = 'on';
	const HTTP = 'HTTP';
	const HTTPS = 'HTTPS';

	const DIGIT_MAP = array(
	    '0' => 'a',
        '1' => 'e',
        '2' => 'i',
        '3' => 'o',
        '4' => 'c',
        '5' => 'd',
        '6' => 'f',
        '7' => 'g',
        '8' => 'h',
        '9' => 'j'
    );

    const REVERSE_DIGIT_MAP = array(
        'a' => '0',
        'e' => '1',
        'i' => '2',
        'o' => '3',
        'c' => '4',
        'd' => '5',
        'f' => '6',
        'g' => '7',
        'h' => '8',
        'j' => '9'
    );

	static $requestParametersHash = null;

	/**
	 * Add to get string
	 * @param mixed $key Key or keys
	 * @param mixed $value Value or values
	 * @param array $remove_keys Remove keys array
	 * @param array $remove_values Remove key values
	 * @return string GET string
	 */
	public static function addToGetString($key = null, $value = null, $remove_keys = null, $remove_values = null){
		$get_array = isset($_GET) ? $_GET : array();
		if(!empty($remove_keys)){ $remove_keys = (array)$remove_keys; }
		if(!empty($remove_values)){ $remove_values = (array)$remove_values; }
		if(!empty($remove_keys)){
			foreach($remove_keys as $remove_key => $remove_key_value){  
				if(isset($get_array[$remove_key_value])){
					unset($get_array[$remove_key_value]); 
				}
				if(have($remove_values)){ 
					foreach($get_array as $get_key => $get_value){
						if(!is_array($get_value)){
							if($get_key == $remove_key_value and isset($remove_values[$remove_key]) and $remove_values[$remove_key] == $get_value){
								unset($get_array[$get_key]);
							}
						}else{
							foreach($get_value as $get_value_key => $get_value_value){
								if(isset($get_array[$get_key][$get_value_key]) and isset($remove_values[$remove_key]) and $remove_values[$remove_key] == $get_array[$get_key][$get_value_key]){
									unset($get_array[$get_key][$get_value_key]);
								}
							}
						}
					}
				} 
			} 
		}
		if(is_array($key)){ 
			$key_value = array_combine($key, $value); 
			foreach($key_value as $this_key => $this_value){ 
				$get_array[$this_key] = $this_value; 
			} 
		}else{ 
			$get_array[$key] = $value;
		}
		$return_get_string = '?';
		foreach($get_array as $get_key => $get_value){ 
			if(!empty($get_value)){ 
				if(is_array($get_value)){ 
					foreach($get_value as $value_instance){ 
						$return_get_string .= '&amp;' . urlencode($get_key) . '[]=' . urlencode($value_instance); 
					} 
				}else{ 
					$return_get_string .= '&amp;' . urlencode($get_key) . '=' . urlencode($get_value); 
				} 
			} 
		}
		return $return_get_string;
	}	

	/**
	 * Add to get string and return non encoded string
	 * @param mixed $key Key or keys
	 * @param mixed $value Value or values
	 * @param array $remove_keys Remove keys array
	 * @param array $remove_values Remove key values
	 * @return string GET string
	 */
	public static function addToGetStringAjax($key = null, $value = null, $remove_keys = null, $remove_values = null){
		return str_ireplace(array('&amp;'), array('&'), self::addToGetString($key, $value, $remove_keys, $remove_values));
	}
	
	/**
	 * Add get vars to existing mystery url
	 * @param string $url Url string
	 * @param array $data $_GET data
	 * @return string Url string
	 */
	public static function pushGetVar($url = null, $data = array()){
		return $url . ((strpos($url, '?') > 0) ? null : '?') . str_replace('?', '', self::addToGetString(array_keys($data), array_values($data)));
	}

	/**
	 * Returns full path of current url
	 * @return string Return full path.
	 */
	public static function getFullUrl(){ 
		return (empty($_SERVER[self::HTTPS]) ? '' : ($_SERVER[self::HTTPS] == self::ON) ? self::HTTPS_PROTOCOL : self::HTTP_PROTOCOL) . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	}

	/**
	 * Return parameters hash - one single time
	 *
	 * @return null|string
	 */
	public static function getGetParametersHash(){
		if(null === self::$requestParametersHash){
			self::$requestParametersHash = md5(serialize($_GET));
		}
		return self::$requestParametersHash;
	}

	/**
	 * Return full base url
	 *
	 * @return string
	 */
	public static function getFullBaseUrl(){
		return substr(self::getFullUrl(), 0, strpos(self::getFullUrl() . '?', '?'));
	}

	/**
	 * Is current url secure
	 *
	 * @return bool
	 */
	public static function isSecureUrl(){
		return (!empty($_SERVER[self::HTTPS]) && $_SERVER[self::HTTPS] != self::OFF);
	}

	/**
	 * Get HTTP protocol
	 *
	 * @return string
	 */
	public static function getProtocol(){
		return (self::isSecureUrl()) ? self::HTTPS_PROTOCOL : self::HTTP_PROTOCOL;
	}

	/**
	 * Get query string
	 *
	 * @return string
	 */
	public static function getQueryString(){
		return (!empty($_SERVER['QUERY_STRING'])) ? '?' . $_SERVER['QUERY_STRING'] : '';
	}

    /**
     * Encode number
     *
     * @param $number
     * @return string|null
     */
	public static function encodeNumber($number) {
        if ($number) {
            $return = '';
            for ($i = 0; $i < strlen($number); $i++) {
                $return .= self::DIGIT_MAP[$number{$i}];
            }
            return $return;
        }
        return null;
    }

    /**
     * Decode number
     *
     * @param $reference
     * @return string|null
     */
    public static function decodeNumber($reference) {
        if ($reference) {
            $return = '';
            for ($i = 0; $i < strlen($reference); $i++) {
                $return .= self::REVERSE_DIGIT_MAP[$reference{$i}];
            }
            return $return;
        }
        return null;
    }

	/**
	 * Merge two urls
	 *
	 * @param string $root Needs to be absolute
	 * @param string $url Can be a relative url
	 * @return bool|mixed|string
	 */
	public static function mergeUrls($root, $url){
		if(substr($root, 0, 2) == '//'){
			$root = self::HTTPS_PROTOCOL . substr($root, 2);
		}
		$components = parse_url($root);
		if(substr($url, 0, 2) == '//'){
			$url = ($components['scheme'] == self::HTTPS ? self::HTTPS_PROTOCOL : self::HTTP_PROTOCOL) . substr($url, 2);
		}
		if(
			false === (stripos($root, self::HTTP_PROTOCOL) > -1 || stripos($root, self::HTTPS_PROTOCOL) > -1) ||
			empty($url)
		){
			return false;
		}
		if(stripos($url, self::HTTP_PROTOCOL) > -1 || stripos($url, self::HTTPS_PROTOCOL) > -1){
			return $url;
		}
		if(substr($url, 0, 1) == self::PATH_SLASH){
			return $components['scheme'] . self::PROTOCOL_SEPARATOR . $components['host'] . $url;
		}
		$path = $components['path'];
		$lastSlash = -(strlen($path) - 1 - strrpos($path, self::PATH_SLASH));
		$last_component = ($lastSlash < 0) ? substr($path, $lastSlash) : '';
		if(strpos($last_component, '.') > -1){
			$root = rtrim(str_ireplace($last_component, '', $path), self::PATH_SLASH);
		}else{
			$root = rtrim($path, self::PATH_SLASH);
		}
		$step_backs = substr_count($url, '../');
		if($step_backs > 0){
			for($i = 0; $i < $step_backs; $i++){
				$root = substr($root, 0, strrpos($root, self::PATH_SLASH));
			}
			$url = str_replace('../', '', $url);
		}
		return $components['scheme'] . self::PROTOCOL_SEPARATOR . $components['host'] . $root . self::PATH_SLASH . $url;
	}

}