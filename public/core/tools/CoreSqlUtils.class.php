<?php

/**
 * Core Mysql Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreSqlUtils {

	/**
	 * Does column exist
	 */
	const SQL_COLUMN_EXISTS = "SHOW COLUMNS FROM :table LIKE :fieldName";

	/**
	 * Does table exist
	 */
	const SQL_TABLE_EXISTS = "SELECT 1 FROM :table LIMIT 1";

    /**
     * Stored statements
     *
     * @var array
     */
    private static $statements = array();

    /**
     * Keep track of active transactions
     *
     * @var bool
     */
    private static $activeTransaction = false;

	/**
	 * Begin transaction
	 * @return True or false
	 */	
	public static function beginTransaction(){

		try {		

            /** check if we already have an active transaction */
            if(self::$activeTransaction) return true;

            /** otherwise begin one now */
			CoreData::getSqlStore()->beginTransaction();
            self::$activeTransaction = true;

			return true;
			
		} catch(PDOException $e) {  
			
			//Core errors
			CoreLog::fatal($e->getMessage());
		
		}
		
		return false;		

	}

	/**
	 * Commit transaction
	 * @return True or false
	 */		
	public static function commitTransaction(){

		try {		

            /** only if we have an active transaction */
			if(self::$activeTransaction) CoreData::getSqlStore()->commit();
            self::$activeTransaction = false;
			return true;
			
		} catch(PDOException $e) {  
			
			//Core errors
			CoreLog::fatal($e->getMessage());
		
		}
		
		return false;	
				
	}

    /**
     * Bind data
     *
     * @param $query
     * @param $data
     * @return mixed
     */
    private static function bindData($query, $data){

        /**
         * Bind data
         */
        if(!empty($data)){
            foreach($data as $key => $value){
                switch(true){

                    /**
                     * Integer
                     */
                    case (is_int($value)):
                        $query->bindValue($key, (int)$value, PDO::PARAM_INT);
                        break;

                    /**
                     * String
                     */
                    default:
                        $query->bindValue($key, $value, PDO::PARAM_STR);
                    break;

                }
            }
        }

        /**
         * Return statement
         */
        return $query;

    }

	/**
	 * Roll back transaction
	 * @return True or false
	 */	
	public static function rollbackTransaction(){

		try {

            /** only if we have an active transaction */
            if(self::$activeTransaction) CoreData::getSqlStore()->rollBack();
            self::$activeTransaction = false;
			return true;
			
		} catch(PDOException $e) {  
			
			//Core errors
			CoreLog::fatal($e->getMessage());
		
		}
		
		return false;	
				
	}
	
	/**
	 * Run update statement
     * attempt to re-use existing statement
     *
	 * @param String $sql
	 * @param array $data
	 * @return boolean
	 */
	public static function update($sql = null, $data = array()){
		
		try {

            /**
             * Execute query
             */
            $key = md5($sql);
            if(false === ($statement = isset(self::$statements[$key]) ? self::$statements[$key] : false)){
                $statement = CoreData::getSqlStore()->prepare($sql);
				if(!CoreSysUtils::isCommandLine()) self::$statements[$key] = $statement;
            }

            /**
             * Bind data
             */
            $update = self::bindData($statement, $data);

            /**
             * Run query
             */
            $update->execute();

			//if updated return true
			return $update->rowCount() ? true : false;
			
		} catch(PDOException $e) {  
			
			//Core errors
			CoreLog::fatal($e->getMessage());
		
		}
		
		return false;		
		
	}

	/**
	 * Table exists
	 *
	 * @param string $table
	 * @return bool
	 */
	public static function tableExists($table = null) {
		try {
			$result = CoreData::getSqlStore()->query(str_replace(':table', $table, self::SQL_TABLE_EXISTS));
		} catch (Exception $e) {
			return false;
		}
		return $result !== false;
	}

	/**
	 * Column exists
	 *
	 * @param null $table
	 * @param null $column
	 * @return bool
	 */
	public static function columnExists($table = null, $column = null){

		try {

			if(false === CoreSqlUtils::tableExists($table)){
				return false;
			}

			$statement = CoreData::getSqlStore()->prepare(str_replace(':table', $table, self::SQL_COLUMN_EXISTS));
			$result = self::bindData($statement, array(
				':fieldName' => $column
			));

			/**
			 * Run query
			 */
			$result->execute();

			$return = $result->fetch(PDO::FETCH_ASSOC);
			return !empty($return);

		} catch(PDOException $e) {

			//Core errors
			CoreLog::fatal($e->getMessage());

		}

		return false;
	}

	/**
	 * Get a single row
	 * @param String $sql
	 * @param array $data
	 * @return array row or false
	 */
	public static function row($sql = null, $data = array()){

		try {		
			
			/**
			 * Execute query
			 */
            $key = md5($sql);
            if(false === ($statement = isset(self::$statements[$key]) ? self::$statements[$key] : false)){
                $statement = CoreData::getSqlStore()->prepare($sql);
				if(!CoreSysUtils::isCommandLine()) self::$statements[$key] = $statement;
            }

            /**
             * Bind data
             */
            $result = self::bindData($statement, $data);

            /**
             * Run query
             */
            $result->execute();

			//return row
			$return = $result->fetchAll(PDO::FETCH_ASSOC);

			return isset($return[0]) ? $return[0] : false;
			
		} catch(PDOException $e) {  

			//Core errors
			CoreLog::fatal($e->getMessage());
		
		}
		
		return false;
		
	}

    /**
     * Delete rows
     *
     * @param null $sql
     * @param array $data
     * @return bool
     */
    public static function delete($sql = null, $data = array()){

        try {

            /**
             * Execute query
             */
            $key = md5($sql);
            if(false === ($statement = isset(self::$statements[$key]) ? self::$statements[$key] : false)){
                $statement = CoreData::getSqlStore()->prepare($sql);
				if(!CoreSysUtils::isCommandLine()) self::$statements[$key] = $statement;
            }

            /**
             * Bind data
             */
            $delete = self::bindData($statement, $data);

            /**
             * Run query
             */
            $delete->execute();

            /**
             * Show affected rows
             */
            return ($delete->rowCount() > 0);

        } catch(PDOException $e) {

            //Core errors
            CoreLog::fatal($e->getMessage());

        }

        return false;

    }

	/**
	 * Get multiple rows
     *
	 * @param String $sql
	 * @param array $parameters
	 * @return Array row or false
	 */	
	public static function rows($sql = null, $data = array()){
		
		try {
			
			/**
			 * Execute query
			 */
            $key = md5($sql);
            if(false === ($statement = isset(self::$statements[$key]) ? self::$statements[$key] : false)){
                $statement = CoreData::getSqlStore()->prepare($sql);
                if(!CoreSysUtils::isCommandLine()) self::$statements[$key] = $statement;
            }

            /**
             * Bind data
             */
            $result = self::bindData($statement, $data);

            /**
             * Run query
             */
            $result->execute();

			return $result->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $e) {  
			
			//Core errors
			CoreLog::fatal($e->getMessage());
		
		}
		
		return false;
		
	}

    /**
     * Build sql
     *
     * @param null $sql
     * @param array $data
     * @return mixed|null
     */
    public static function sql($sql = null, $data = array()){
        foreach($data as $key => $value){
            $sql = str_ireplace($key, CoreSqlUtils::quote($value), $sql);
        }
        return $sql;
    }

	/**
	 * Insert a single row
	 * @param String $sql
	 * @param array $data
	 * @return int new id or false
	 */	
	public static function insert($sql = null, $data = array()){

		try {		
			
			/**
			 * Execute query
			 */
            $key = md5($sql);
            if(false === ($statement = isset(self::$statements[$key]) ? self::$statements[$key] : false)){
                $statement = CoreData::getSqlStore()->prepare($sql);
				if(!CoreSysUtils::isCommandLine()) self::$statements[$key] = $statement;
            }

            /**
             * Bind data
             */
            $insert = self::bindData($statement, $data);

            /**
             * Run query
             */
            $insert->execute();

			//return insert id
			if(false !== ($id = CoreData::getSqlStore()->lastInsertId())){
                return $id;
			}
						
		} catch(PDOException $e) {  
			
			//Core errors
			CoreLog::fatal($e->getMessage());
		
		}
		
		return false;
				
	}

    /**
     * Found rows
     *
     * @return mixed
     */
    public static function foundRows(){

        try {

            return CoreData::getSqlStore()->query('SELECT FOUND_ROWS();')->fetch(PDO::FETCH_COLUMN);

        } catch(PDOException $e) {

            //Core errors
            CoreLog::fatal($e->getMessage());

        }

    }

    /**
     * Run simple query
     * @param null $sql
     * @return bool
     */
    public static function query($sql = null){

        try {

            /**
             * Simply run query
             */
            $result = CoreData::getSqlStore()->query($sql);

            return $result;

        } catch(PDOException $e) {

            //Core errors
            CoreLog::fatal($e->getMessage());

        }

        return false;

    }

	/**
	 * See if table exists
	 * @param String $table Table name
	 * @return boolean True if exists or false otherwise
	 */
	public static function exists($table = null){

        try {

			/**
			 * Lookup table
			 */
            $statement = CoreData::getSqlStore()->query("SHOW TABLES LIKE '" . $table . "';");
            $statement->execute();
            $return = false;
            while($row = $statement->fetch(PDO::FETCH_ASSOC)){
                $return = true;
            }
            $statement->closeCursor();
            $statement = null;

            return $return;

		} catch(PDOException $e) {  

			//Core errors
			CoreLog::fatal($e->getMessage());
		
		}
		
		return false;
	}

	/**
	 * String to SQL friendly date
	 *
	 * @param null $string
	 * @return bool|string
	 */
	public static function toDate($string = null){
		return date('Y-m-d H:i:s', strtotime($string));
	}

	/**
	 * Get timezone offset to properly configure mysql
	 * source: http://www.sitepoint.com/synchronize-php-mysql-timezone-configuration/
	 *
	 * @return string
	 */
	public static function getOffset(){
		$now = new DateTime();
		$mins = $now->getOffset() / 60;
		$sgn = ($mins < 0 ? -1 : 1);
		$mins = abs($mins);
		$hrs = floor($mins / 60);
		$mins -= $hrs * 60;
		return sprintf('%+d:%02d', $hrs*$sgn, $mins);
	}

    /**
     * Escape string
     *
     * @param $string
     * @return string
     */
    public static function quote($string = null){
        return CoreData::getSqlStore()->quote($string);
    }

}