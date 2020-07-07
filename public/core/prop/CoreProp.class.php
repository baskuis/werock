<?php

/**
 * Core Properties
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreProp {
	
	/**
	 * Sql queries
	 */
	const SQL_GET_CORE_PROP = " SELECT * FROM `werock_properties` WHERE `werock_property_key` = :key; ";
	const SQL_SET_CORE_PROP = " INSERT INTO `werock_properties` ( `werock_property_key`, `werock_property_value` ) VALUES ( :key, :value ) ON DUPLICATE KEY UPDATE `werock_property_value` = :value; ";
	const SQL_UPDATE_CORE_PROP = " UPDATE `werock_properties` SET `werock_property_value` = :value WHERE `werock_property_key` = :key; ";

	/**
	 * Get core property
	 * @param String $key
	 * @param String $default
	 * @return null
	 */
	public static function get($key = null, $default = null){
		
		//get record
		$record = CoreSqlUtils::row(self::SQL_GET_CORE_PROP, array(
			':key' => $key
		));
		
		//insert if needed
		if(!isset($record['werock_property_value'])){
			
			//insert prop
			CoreSqlUtils::insert(self::SQL_SET_CORE_PROP, array(
				':key' => $key,
				':value' => $default
			));
			
			//return default
			return $default;
			
		}
		
		//return value
		return $record['werock_property_value'];
		
	}
	
	/**
	 * Get core property
	 * @param String $key
	 * @param String $value
	 * @return null
	 */
	public static function set($key = null, $value = null){

        $record = CoreSqlUtils::row(self::SQL_GET_CORE_PROP, array(
            ':key' => $key
        ));

        if(isset($record['werock_property_value'])){
            if($record['werock_property_value'] == $value){
                return (int) $record['werock_property_id'];
            }else{
                if(CoreSqlUtils::update(self::SQL_UPDATE_CORE_PROP, array(
                    ':key' => $key,
                    ':value' => $value
                ))){
                    return (int) $record['werock_property_id'];
                }
            }
        }else{
            return CoreSqlUtils::insert(self::SQL_SET_CORE_PROP, array(
                ':key' => $key,
                ':value' => $value
            ));
        }

        CoreLog::error("Unable to set property key: " . $key . " value: " . $value);
				
	}
	
}