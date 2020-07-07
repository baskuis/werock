<?php

/**
 * Registers notifications
 */
class CoreNotification {
	
	/**
	 * Keys
	 */
	const TEXT = 'text';
	const TYPE = 'type';
	
	/**
	 * Type list
	 */
	const STANDARD = 'standard';
	const WARNING = 'warning';
	const SUCCESS = 'success';
	const ERROR = 'error';

    /**
     * For session storage
     */
    const SESSION_KEY = 'notifications';

	/**
	 * Notifications holder 
	 */
	private static $notifications = array();
	
	/**
	 * Get Types
	 * @return Array
	 */
	public static function getTypes(){
		return array(self::STANDARD, self::WARNING, self::SUCCESS, self::ERROR);
	}
	
	/**
	 * Set notification
	 *
	 * @param String $text Notification text
	 * @param String $type Notification type
	 * @return Boolean
	 */
	public static function set($text = null, $type = self::STANDARD){
		if(empty($text)){ return false; }
        CoreSessionUtils::assureSession();
		$notification = array();
		$notification[self::TEXT] = $text;
		$notification[self::TYPE] = $type;
		$_SESSION[self::SESSION_KEY][] = self::$notifications[] = $notification;
		if($type == self::ERROR || $type == self::WARNING) {
			CoreLog::warn('Notification[' . $type . ']: ' . $text);
		}
		if($type == self::STANDARD || $type == self::SUCCESS) {
			CoreLog::info('Notification[' . $type . ']: ' . $text);
		}
		return true;
	}
	
	/**
	 * Get notification of a certain type - or blank
	 *
	 * @param String $type notifications type
	 * @return array Return notifications
	 */
	public static function getNotifications($type = null){
		$return = array();
		if(empty($_SESSION[self::SESSION_KEY])){ return array(); }
		if(isset($_SESSION[self::SESSION_KEY]) && !empty($_SESSION[self::SESSION_KEY])){
			foreach($_SESSION[self::SESSION_KEY] as $key => $notification){
				if(!empty($type) && $notification[self::TYPE] == $type){
					if(CoreHeaders::needBody()) {
						unset($_SESSION[self::SESSION_KEY][$key]);
					}
					$return[] = $notification;
				}else if(empty($type)){
					if(CoreHeaders::needBody()) {
						unset($_SESSION[self::SESSION_KEY][$key]);
					}
					$return[] = $notification;
				} // or skip
			}
		}
		return $return;
	}
	
}