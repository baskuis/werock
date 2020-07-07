<?php

/**
 * Core Session Handler
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreSessionHandler implements SessionHandlerInterface {

    /**
     * Constants
     */
    const MAX_SESSION_LIFETIME_PROP_KEY = 'max:session:lifetime';
    const INI_SESSION_GC_MAX_LIFETIME = 'session.gc_maxlifetime';
    const SESSION_DATA_COLUMN = 'werock_session_data';
    const EMPTY_STRING = '';
    const PDO = 'PDO';

    /**
     * Columns
     */
    const COLUMN_ID = ':id';
    const COLUMN_KEY = ':key';
    const COLUMN_DATA = ':data';
    const COLUMN_EXPIRES = ':expires';

    /**
     * Set session lifetime
     *
     * @var int
     */
    var $lifeTime = 1440;

    /**
     * Set current time
     *
     * @var int
     */
    var $currentTime = 0;

    /**
     * Queries
     */
    const SELECT_SESSION_SQL = " SELECT * FROM `werock_sessions` WHERE `werock_session_id` = :id AND `werock_session_key` = :key AND `werock_session_expires` > :expires; ";
    const UPDATE_SESSION_SQL = " UPDATE `werock_sessions` SET `werock_session_expires` = :expires, `werock_session_data` = :data WHERE `werock_session_id` = :id AND `werock_session_key` = :key; ";
    const INSERT_SESSION_SQL = " INSERT INTO `werock_sessions` ( `werock_session_id`, `werock_session_expires`, `werock_session_data`, `werock_session_key` ) VALUES ( :id, :expires, :data, :key ) ON DUPLICATE KEY UPDATE `werock_session_expires` = :expires, `werock_session_data` = :data, `werock_session_key` = :key; ";
    const DELETE_SESSION_SQL = " DELETE FROM `werock_sessions` WHERE `werock_session_id` = :id; ";
    const DELETE_SESSIONS_SQL = " DELETE FROM `werock_sessions` WHERE `werock_session_expires` < :expires; ";

    /**
     * Open Session Handler
     *
     * @param $path
     * @param $name
     * @return bool
     */
    public function open($path = null, $name = null){
        if(CoreSysUtils::isCommandLine()) return true;
        $this->lifeTime = CoreProp::get(self::MAX_SESSION_LIFETIME_PROP_KEY, ini_get(self::INI_SESSION_GC_MAX_LIFETIME));
        $this->currentTime = time();
        if(get_class(CoreData::mysql()) !== PDO::class && CoreData::getSqlStore()){
            CoreLog::fatal('We need a db connection to store sessions');
            return false;
        }
        return true;
    }

    /**
     * Close session
     */
    public function close(){
        if(!CoreSysUtils::isCommandLine()) $this->gc(0);
        return true;
    }

    /**
     * Generate session check key, prevents session hijacking
     *
     * @return string
     */
    public function generateKey(){
        return md5(CoreSecUtils::getUserAgent() . CoreSecUtils::getRemoteIp2Octals() . SHORT_SALT);
    }

    /**
     * Read session data
     *
     * @param $id
     * @return string
     */
    public function read($id = null){
        if(CoreSysUtils::isCommandLine()) return null;
        $key = self::generateKey();
        $session = CoreSqlUtils::row(self::SELECT_SESSION_SQL, array(
            self::COLUMN_ID => $id,
            self::COLUMN_KEY => $key,
            self::COLUMN_EXPIRES => $this->currentTime)
        );
        return isset($session[self::SESSION_DATA_COLUMN]) ? $session[self::SESSION_DATA_COLUMN] : self::EMPTY_STRING;
    }

    /**
     * Write session data
     *
     * @param $id
     * @param $data
     * @return bool
     */
    public function write($id = null, $data = null){
        if(CoreSysUtils::isCommandLine()) return true;
        $expiration = time() + $this->lifeTime;
        $key = self::generateKey();
        if(false === CoreSqlUtils::update(self::UPDATE_SESSION_SQL, array(
            self::COLUMN_ID => $id,
            self::COLUMN_EXPIRES => $expiration,
            self::COLUMN_DATA => $data,
            self::COLUMN_KEY => $key
        ))){
            return (false !== CoreSqlUtils::insert(self::INSERT_SESSION_SQL, array(
                self::COLUMN_ID => $id,
                self::COLUMN_EXPIRES => $expiration,
                self::COLUMN_DATA => $data,
                self::COLUMN_KEY => $key
            )));
        }
        return true;
    }

    /**
     * Destroy session
     *
     * @param null $id
     * @return bool
     */
    public function destroy($id = null){
        if(CoreSysUtils::isCommandLine()) return true;
        return (CoreSqlUtils::delete(self::DELETE_SESSION_SQL, array(
                self::COLUMN_ID => $id
            )) > 0);
    }

    /**
     * Session garbage collection
     * @param int $maxLifeTime
     *
     * @return bool
     */
    public function gc($maxLifeTime){
        if(CoreSysUtils::isCommandLine()) return true;
        return CoreSqlUtils::delete(self::DELETE_SESSIONS_SQL, array(
            self::COLUMN_EXPIRES => $this->currentTime
        ));
    }

}