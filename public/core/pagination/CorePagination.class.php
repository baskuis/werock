<?php

/**
 * Modifies SQL query to add pagination
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CorePagination {

    public static $ranQuery = false;
    public static $startKey = 'start';
    public static $sql = null;
    public static $rows = array();
    public static $count = 0;
    public static $start = 0;
    public static $length = 20;
    public static $pages = 8;

    public static function setSql($sql = null){
        self::$sql = $sql;
    }
    public static function getRows(){
        return self::$rows;
    }

    /**
     * Modify query
     * @return bool Returns str_replace success
     */
    protected static function modifyQuery(){
        return (self::$sql != (self::$sql = preg_replace('/SELECT/' , 'SELECT SQL_CALC_FOUND_ROWS', self::$sql, 1) . ' LIMIT ' . (int) self::$start . ', ' . (int) self::$length));
    }

    /**
     * Run query and get CorePaginationObject reference object
     *
     * @return CorePaginationObject
     */
    public static function runQuery(){

        /**
         * Currently only supported on Mysql
         */
        if(CoreData::$type != CoreData::TYPE_MYSQL) CoreLog::error('SQL Pagination currently only supported on mysql database');

        self::$ranQuery = true;
        self::$start = (isset($_GET[self::$startKey]) && (int)$_GET[self::$startKey] > 0) ? (int)$_GET[self::$startKey] : 0;
        self::modifyQuery();
        self::$rows = CoreSqlUtils::rows(self::$sql);
        self::$count = CoreSqlUtils::foundRows();

        $CorePaginationObject = self::getPaginationObject();
        $CorePaginationObject->setRows(self::$rows);

        return $CorePaginationObject;

    }

    public static function setPaginationObject(CorePaginationObject $CorePaginationObject){
        self::$ranQuery = true;
        self::$start = $CorePaginationObject->getStart();
        self::$count = $CorePaginationObject->getCount();
        self::$length = $CorePaginationObject->getLength();
        self::$pages = $CorePaginationObject->getPages();
        self::$startKey = $CorePaginationObject->getStartKey();
    }

    public static function getPaginationObject(){
        $CorePaginationObject = new CorePaginationObject();
        $CorePaginationObject->setStart(self::$start);
        $CorePaginationObject->setCount(self::$count);
        $CorePaginationObject->setLength(self::$length);
        $CorePaginationObject->setPages(self::$pages);
        $CorePaginationObject->setStartKey(self::$startKey);
        return $CorePaginationObject;
    }

    /**
     * @return boolean
     */
    public static function isRanQuery()
    {
        return self::$ranQuery;
    }

}