<?php

/**
 * Core Visitor
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreVisitor {

    /**
     * Allow reflection
     */
    use ClassReflectionTrait;

    /**
     * Allow interceptors
     */
    use CoreInterceptorTrait;

    /**
     * visitors cookie key
     */
    const VISITORS_COOKIE_KEY = 'visitorsId';

    /**
     * Events
     */
    const EVENT_VISITOR_CREATED = 'event:visitor:created';
    const EVENT_VISITOR_UPDATED = 'event:visitor:updated';

    /**
     * Visitor cookie expiration
     */
    const VISITOR_COOKIE_EXPIRATION = 15552000;

    /**
     * Vistor SQL Queries
     */
    const INSERT_VISITOR_SQL = "
		INSERT INTO 
			`werock_visitors` 
		( 
			`werock_visitor_hash`, 
			`werock_visitor_created`, 
			`werock_visitor_ip` 
		) VALUES ( 
			:werock_visitor_hash, 
			:werock_visitor_created, 
			:werock_visitor_ip 
		)
	";
    const SELECT_VISITOR_SQL = "
		SELECT 
			* 
		FROM 
			`werock_visitors` 
		WHERE 
			`werock_visitor_hash` = :werock_visitor_hash
	";
    const UPDATE_VISITOR_SQL = "
		UPDATE 
			`werock_visitors` 
		SET 
			`werock_visitor_hits` = `werock_visitor_hits` + 1 
		WHERE 
			`werock_visitor_hash` = :werock_visitor_hash
	";
    const SELECT_VISITOR_BY_ID_SQL = "
        SELECT
            *
        FROM
			`werock_visitors`
        WHERE
			`werock_visitor_id` = :werock_visitor_id
    ";

    /**
     * Visitor Data SQL Queries
     */
    const SELECT_VISITOR_DATA_ROW_SQL = "
		SELECT
			`werock_visitor_data_id`
		FROM 
			`werock_visitor_data`
		WHERE 
			`werock_visitor_data_key` = :werock_visitor_data_key
	";
    const INSERT_VISITOR_DATA_SQL = "
		INSERT INTO 
			`werock_visitor_data`
		(
			`werock_visitor_data_key`,
			`werock_visitor_data_date_added`
		) VALUES (
			:werock_visitor_data_key,
			NOW()
		)
	";
    const SELECT_VISITOR_VALUE_SQL = "
		SELECT 
			*
		FROM 
			`werock_visitor_data_values`
		WHERE 
			`werock_visitor_data_value_text` = :werock_visitor_data_value_text
		AND 
			`werock_visitor_id` = :werock_visitor_id
		AND 
			`werock_visitor_data_id` = :werock_visitor_data_id
		
	";
    const UPDATE_VISITOR_VALUE_SQL = "
		UPDATE 
			`werock_visitor_data_values`
		SET 
			`werock_visitor_data_value_last_modified` = NOW()
		WHERE 
			`werock_visitor_data_value_id` = :werock_visitor_data_value_id
	";
    const INSERT_VISITOR_VALUE_SQL = "
		INSERT INTO 
			`werock_visitor_data_values`
		(
			`werock_visitor_id`,
			`werock_visitor_data_id`,
			`werock_visitor_data_value_text`,
			`werock_visitor_data_value_date_added`
		) VALUES (
			:werock_visitor_id,
			:werock_visitor_data_id,
			:werock_visitor_data_value_text,
			NOW()
		)
	";

    /**
     * Get Visitor Data Queries
     */
    const SELECT_VISITOR_DATA_SQL = "
		SELECT 
			`werock_visitor_data_value_text`
		FROM 
			`werock_visitor_data_values`
		LEFT JOIN
			`werock_visitor_data` USING (`werock_visitor_data_id`)
		WHERE 
			`werock_visitor_id` = :werock_visitor_id
		AND 
			`werock_visitor_data_key` = :werock_visitor_data_key
		ORDER BY
			`werock_visitor_data_value_last_modified` DESC
		LIMIT 
			1
	";
    const SELECT_ALL_VISITOR_DATA_SQL = "
		SELECT 
			`werock_visitor_data_value_text`
		FROM 
			`werock_visitor_data_values`
		LEFT JOIN
			`werock_visitor_data` USING (`werock_visitor_data_id`)
		WHERE 
			`werock_visitor_id` = :werock_visitor_id
		AND 
			`werock_visitor_data_key` = :werock_visitor_data_key
	";

    /** @var CoreVisitorObject $visitor */
    private static $visitor = array();

    /**
     * Set visitor cookie
     *
     */
    public static function set(){

        //try to get a visitors reference
        if(false === (self::$visitor = self::get())){

            //or create one
            self::create();

            //check for visitors id
            if(false === (self::$visitor = self::get())){
                CoreLog::fatal('Unable to find or set a visitor cookie');
            }

        }

    }

    /**
     * Create key
     *
     */
    private static function createKey(){

        //create cookie key
        $_COOKIE[self::VISITORS_COOKIE_KEY] = sha1($_SERVER['REMOTE_ADDR'] . time() . rand());

        //set cookie
        setcookie(self::VISITORS_COOKIE_KEY, $_COOKIE[self::VISITORS_COOKIE_KEY], time() + self::VISITOR_COOKIE_EXPIRATION, '/', null /*$_SERVER['SERVER_NAME']*/, false, true);

    }

    /**
     * Get visitor
     *
     * @return bool|CoreVisitorObject
     */
    private static function _get(){

        //quick sanity check
        if(!isset($_COOKIE[self::VISITORS_COOKIE_KEY])){
            self::createKey();
            return false;
        }

        //get visitors
        if(false !== ($visitorRow = CoreSqlUtils::row(self::SELECT_VISITOR_SQL, array(
            ':werock_visitor_hash' => $_COOKIE[self::VISITORS_COOKIE_KEY]
        )))){

            //update visitor
            CoreSqlUtils::update(self::UPDATE_VISITOR_SQL, array(':werock_visitor_hash' => $_COOKIE[self::VISITORS_COOKIE_KEY]));

            /**
             * Populate core visitor object
             */
            $CoreVisitorObject = new CoreVisitorObject();
            $CoreVisitorObject->setId($visitorRow['werock_visitor_id']);
            $CoreVisitorObject->setHash($visitorRow['werock_visitor_hash']);
            $CoreVisitorObject->setHits($visitorRow['werock_visitor_hits']);
            $CoreVisitorObject->setCreated($visitorRow['werock_visitor_created']);
            $CoreVisitorObject->setIp($visitorRow['werock_visitor_ip']);

            /** Dispatch subscribed listners */
            CoreObserver::dispatch(self::EVENT_VISITOR_UPDATED, $CoreVisitorObject);

            //all went well
            return $CoreVisitorObject;

        };

        //something went wrong - visitor could not be inserted
        return false;

    }

    /**
     * Create visitor object
     * note: allow interception
     *
     * @return bool|CoreVisitorObject
     */
    private static function _create(){

        /** IP long */
        $iplong = ip2long($_SERVER['REMOTE_ADDR']);

        /** current time */
        $time = time();

        /** @var int $id */
        $id = CoreSqlUtils::insert(self::INSERT_VISITOR_SQL, array(
            ':werock_visitor_hash' => $_COOKIE[self::VISITORS_COOKIE_KEY],
            ':werock_visitor_created' => $time,
            ':werock_visitor_ip' => $iplong
        ));

        /** if id */
        if($id){

            /** @var CoreVisitorObject $CoreVisitorObject */
            $CoreVisitorObject = new CoreVisitorObject();
            $CoreVisitorObject->setId($id);
            $CoreVisitorObject->setHits(1);
            $CoreVisitorObject->setCreated($time);
            $CoreVisitorObject->setIp($iplong);
            $CoreVisitorObject->setHash($_COOKIE[self::VISITORS_COOKIE_KEY]);

            /** @var CoreVisitorObject $visitor */
            self::$visitor = $CoreVisitorObject;

            /** Dispatch subscribed events */
            CoreObserver::dispatch(self::EVENT_VISITOR_CREATED, $CoreVisitorObject);

            return $CoreVisitorObject;

        }

        return false;

    }

    /**
     * Return visitors id
     * @return Integer vistor id or false
     */
    public static function getId(){
        return isset(self::$visitor->id) ? (int) self::$visitor->getId() : false;
    }

    /**
     * Alias for setData
     *
     * @param null $key
     * @param $value
     * @return bool
     */
    public static function store($key = null, $value){
        return self::setData($key, $value, self::getId());
    }

    /**
     * Alias for getData
     *
     * @param null $key
     * @return bool
     */
    public static function retrieve($key = null){
        return self::getData($key, self::getId());
    }

    /**
     * Set visitor data
     * @param String $key
     * @param String $value
     * @param Integer $werock_visitor_id (Optional)
     * @return Boolean
     */
    public static function setData($key = null, $value = null, $werock_visitor_id = null){

        //sanity checks
        if(empty($key)){
            CoreLog::error('No meta value passed.');
            return false;
        }

        //be carefull
        try {

            //set passed visitor id
            $forwerock_visitor_id = ($werock_visitor_id !== false && is_numeric($werock_visitor_id)) ? $werock_visitor_id : self::getId();

            //begin transaction
            CoreSqlUtils::beginTransaction();

            //lookup data row
            $dataRow = CoreSqlUtils::row(self::SELECT_VISITOR_DATA_ROW_SQL, array(
                ':werock_visitor_data_key' => $key
            ));

            //insert if needed
            if(empty($dataRow)){
                $dataRow['werock_visitor_data_id'] = CoreSqlUtils::insert(self::INSERT_VISITOR_DATA_SQL, array(
                    ':werock_visitor_data_key' => $key
                ));
            }

            //lookup value
            $valueRow = CoreSqlUtils::row(self::SELECT_VISITOR_VALUE_SQL, array(
                ':werock_visitor_data_value_text' => (string) $value,
                ':werock_visitor_data_id' => (int) $dataRow['werock_visitor_data_id'],
                ':werock_visitor_id' => (int) $forwerock_visitor_id
            ));

            //or insert one
            if(empty($valueRow)){

                //insert value row
                $inserted = CoreSqlUtils::insert(self::INSERT_VISITOR_VALUE_SQL, array(
                    ':werock_visitor_data_value_text' => (string) $value,
                    ':werock_visitor_data_id' => (int) $dataRow['werock_visitor_data_id'],
                    ':werock_visitor_id' => (int) $forwerock_visitor_id
                ));

                //check inserted row and commit
                if(false !== $inserted){

                    //commit transaction
                    CoreSqlUtils::commitTransaction();
                    return true;

                }

                //update the row
            }else{

                //update the value row
                $updated = CoreSqlUtils::update(self::UPDATE_VISITOR_VALUE_SQL, array(
                    ':werock_visitor_data_value_id' => $valueRow['werock_visitor_data_value_id']
                ));

                //check updated row and commit
                if(false !== $updated){

                    //commit transaction
                    CoreSqlUtils::commitTransaction();
                    return true;

                }

            }

        } catch(Exception $e){

            //handle this error
            CoreLog::error($e->getMessage());

        }

        //roll back transaction
        CoreSqlUtils::rollbackTransaction();

        //something went wrong
        return false;

    }

    /**
     * Get visitor data value
     * @param String $key
     * @param Integer $visitorId (Optional)
     * @return Boolean
     */
    public static function getData($key = null, $visitorId = false){

        //be careful
        try {

            //set passed visitor id
            $forVisitorId = ($visitorId !== false && is_numeric($visitorId)) ? (int) $visitorId : self::getId();

            //get data row
            $dataValueRow = CoreSqlUtils::row(self::SELECT_VISITOR_DATA_SQL, array(
                ':werock_visitor_id' => (int) $forVisitorId,
                ':werock_visitor_data_key' => (string) $key
            ));

            //return the value
            if(isset($dataValueRow['werock_visitor_data_value_text'])){
                return $dataValueRow['werock_visitor_data_value_text'];
            }

        } catch(Exception $e){

            //handle issue
            CoreLog::error($e->getMessage());

        }

        //something went wrong
        return false;

    }

    /**
     * Get visitor
     *
     * @param bool $werock_visitor_id
     * @return CoreVisitorObject|bool
     */
    public static function _getVisitor($werock_visitor_id = false){

        //be careful
        try {

            /**
             * return current visitor
             */
            if(!$werock_visitor_id) return self::$visitor;

            /**
             * Get visitor data rows
             */
            $visitorRow = CoreSqlUtils::row(self::SELECT_VISITOR_BY_ID_SQL, array(
                ':werock_visitor_id' => (int) $werock_visitor_id
            ));

            if(!empty($visitorRow)){

                /**
                 * Populate core visitor object
                 */
                $CoreVisitorObject = new CoreVisitorObject();
                $CoreVisitorObject->setId($visitorRow['werock_visitor_id']);
                $CoreVisitorObject->setHash($visitorRow['werock_visitor_hash']);
                $CoreVisitorObject->setHits($visitorRow['werock_visitor_hits']);
                $CoreVisitorObject->setCreated($visitorRow['werock_visitor_created']);
                $CoreVisitorObject->setIp($visitorRow['werock_visitor_ip']);

                /**
                 * Return CoreVisitorObject
                 */
                return $CoreVisitorObject;

            }

        } catch(Exception $e){

            //handle issue
            CoreLog::error($e->getMessage());

        }

        //something went wrong
        return false;

    }

    /**
     * Get all visitor data values
     * @param String $key
     * @param Integer $werock_visitor_id (Optional)
     * @return Boolean
     */
    public static function getAllData($key = null, $werock_visitor_id = false){

        //be careful
        try {

            //set passed visitor id
            $forwerock_visitor_id = ($werock_visitor_id !== false && is_numeric($werock_visitor_id)) ? $werock_visitor_id : self::getId();

            //get data row
            $dataValueRows = CoreSqlUtils::rows(self::SELECT_ALL_VISITOR_DATA_SQL, array(
                ':werock_visitor_id' => (int) $forwerock_visitor_id,
                ':werock_visitor_data_key' => (string) $key
            ));

            //return the value
            if(!empty($dataValueRows)){

                //create neat return array
                $return = array();
                foreach($dataValueRows as $dataValueRow){
                    array_push($return, $dataValueRow['werock_visitor_data_value_text']);
                }

                //return simple array
                return $return;
            }

        } catch(Exception $e){

            //handle issue
            CoreLog::error($e->getMessage());

        }

        //something went wrong
        return false;

    }

}