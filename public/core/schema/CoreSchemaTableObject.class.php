<?php

/**
 * Core schema table object
 * This class allows interpretation of schema files - and will create of modify SQL based schema's
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreSchemaTableObject {

    public $table;
    public $engine;
    public $collation;
    public $rowFormat;
    public $comments;

    public $fields;

    public $indexes;

    /**
     * @param mixed $collation
     */
    public function setCollation($collation)
    {
        $this->collation = $collation;
    }

    /**
     * @return mixed
     */
    public function getCollation()
    {
        return $this->collation;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $engine
     */
    public function setEngine($engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return mixed
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param mixed $rowFormat
     */
    public function setRowFormat($rowFormat)
    {
        $this->rowFormat = $rowFormat;
    }

    /**
     * @return mixed
     */
    public function getRowFormat()
    {
        return $this->rowFormat;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param mixed $indexes
     */
    public function setIndexes($indexes)
    {
        $this->indexes = $indexes;
    }

    /**
     * @return mixed
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * Add field
     *
     * @param CoreSchemaTableColumnObject $coreSchemaTableColumnObject
     * @return int
     */
    public function addField(CoreSchemaTableColumnObject $coreSchemaTableColumnObject){
        if(empty($this->fields)) $this->fields = array();
        return array_push($this->fields, $coreSchemaTableColumnObject);
    }

    /**
     * Add index
     *
     * @param CoreSchemaTableKeyObject $coreSchemaTableKeyObject
     * @return int
     */
    public function addIndex(CoreSchemaTableKeyObject $coreSchemaTableKeyObject){
        if(empty($this->indexes)) $this->indexes = array();
        return array_push($this->indexes, $coreSchemaTableKeyObject);
    }

}