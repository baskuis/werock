<?php

/**
 * Map Table Row Object
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MapTableRowObject {

    /** @var MapTableTableObject $table */
    public $table;

    /** @var array $columns */
    public $columns;

    /**
     * @param MapTableColumnObject $MapTableColumnObject
     */
    public function addColumn(MapTableColumnObject $MapTableColumnObject){
        $this->columns[$MapTableColumnObject->getField()] = $MapTableColumnObject;
    }

    /**
     * @param array $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param \MapTableTableObject $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return \MapTableTableObject
     */
    public function getTable()
    {
        return $this->table;
    }

}