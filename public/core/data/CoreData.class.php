<?php

/**
 * Core Data
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreData { 

    const TYPE_MYSQL = 'mysql';

	/**
	 * Mysql holder
	 */
	private static $mysql = null;
	
	/**
	 * Set sql store
	 */
	private static $sqlStore = null;

    /**
     * Data type
     */
    public static $type = null;

	/**
	 * Set sql store
	 * @param String $type
	 * @return
	 */
	public static function setSqlStore($type = null){

        //set type
        self::$type = $type;

		//mysql type
		switch($type){
		
			//mysql
            case self::TYPE_MYSQL:
				self::$sqlStore = self::mysql();
			break;
			
			//catch issue
			default:
				CoreLog::fatal("SQL store " . $type . " is not supported.");
			break;
			
		}
		
	}
	
	/**
	 * Return instance sql store
	 */
	public static function getSqlStore(){
		return self::$sqlStore;
	}

	/**
	 * Mysql holder
	 */
	public static function mysql(){
		return self::$mysql;
	}

	/**
	 * Connect to to mysql database
	 *
	 * @param null $host
	 * @param null $dbname
	 * @param null $user
	 * @param null $pass
	 */
	public static function connectMysql($host = null, $dbname = null, $user = null, $pass = null){
		try {
		  	self::$mysql = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, array(
                PDO::ATTR_PERSISTENT => false,
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone='" . CoreSqlUtils::getOffset() . "';",
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			));
		} catch(PDOException $e) {
			CoreLog::fatal($e->getMessage());
		}
	}

}