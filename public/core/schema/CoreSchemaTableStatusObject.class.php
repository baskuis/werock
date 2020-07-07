<?php

/**
 * Core Schema
 * This is a representation of the table status information
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreSchemaTableStatusObject {

    public $Name;
    public $Engine;
    public $Version;
    public $Row_format;
    public $Rows;
    public $Avg_row_length;
    public $Data_length;
    public $Data_max_length;
    public $Index_length;
    public $Data_free;
    public $Auto_increment;
    public $Create_time;
    public $Update_time;
    public $Check_time;
    public $Collation;
    public $Checksum;
    public $Create_options;
    public $Comment;

    /**
     * @param mixed $Auto_increment
     */
    public function setAutoIncrement($Auto_increment)
    {
        $this->Auto_increment = $Auto_increment;
    }

    /**
     * @return mixed
     */
    public function getAutoIncrement()
    {
        return $this->Auto_increment;
    }

    /**
     * @param mixed $Avg_row_length
     */
    public function setAvgRowLength($Avg_row_length)
    {
        $this->Avg_row_length = $Avg_row_length;
    }

    /**
     * @return mixed
     */
    public function getAvgRowLength()
    {
        return $this->Avg_row_length;
    }

    /**
     * @param mixed $Check_time
     */
    public function setCheckTime($Check_time)
    {
        $this->Check_time = $Check_time;
    }

    /**
     * @return mixed
     */
    public function getCheckTime()
    {
        return $this->Check_time;
    }

    /**
     * @param mixed $Checksum
     */
    public function setChecksum($Checksum)
    {
        $this->Checksum = $Checksum;
    }

    /**
     * @return mixed
     */
    public function getChecksum()
    {
        return $this->Checksum;
    }

    /**
     * @param mixed $Collation
     */
    public function setCollation($Collation)
    {
        $this->Collation = $Collation;
    }

    /**
     * @return mixed
     */
    public function getCollation()
    {
        return $this->Collation;
    }

    /**
     * @param mixed $Comment
     */
    public function setComment($Comment)
    {
        $this->Comment = $Comment;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->Comment;
    }

    /**
     * @param mixed $Create_options
     */
    public function setCreateOptions($Create_options)
    {
        $this->Create_options = $Create_options;
    }

    /**
     * @return mixed
     */
    public function getCreateOptions()
    {
        return $this->Create_options;
    }

    /**
     * @param mixed $Create_time
     */
    public function setCreateTime($Create_time)
    {
        $this->Create_time = $Create_time;
    }

    /**
     * @return mixed
     */
    public function getCreateTime()
    {
        return $this->Create_time;
    }

    /**
     * @param mixed $Data_free
     */
    public function setDataFree($Data_free)
    {
        $this->Data_free = $Data_free;
    }

    /**
     * @return mixed
     */
    public function getDataFree()
    {
        return $this->Data_free;
    }

    /**
     * @param mixed $Data_length
     */
    public function setDataLength($Data_length)
    {
        $this->Data_length = $Data_length;
    }

    /**
     * @return mixed
     */
    public function getDataLength()
    {
        return $this->Data_length;
    }

    /**
     * @param mixed $Data_max_length
     */
    public function setDataMaxLength($Data_max_length)
    {
        $this->Data_max_length = $Data_max_length;
    }

    /**
     * @return mixed
     */
    public function getDataMaxLength()
    {
        return $this->Data_max_length;
    }

    /**
     * @param mixed $Engine
     */
    public function setEngine($Engine)
    {
        $this->Engine = $Engine;
    }

    /**
     * @return mixed
     */
    public function getEngine()
    {
        return $this->Engine;
    }

    /**
     * @param mixed $Index_length
     */
    public function setIndexLength($Index_length)
    {
        $this->Index_length = $Index_length;
    }

    /**
     * @return mixed
     */
    public function getIndexLength()
    {
        return $this->Index_length;
    }

    /**
     * @param mixed $Name
     */
    public function setName($Name)
    {
        $this->Name = $Name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->Name;
    }

    /**
     * @param mixed $Row_format
     */
    public function setRowFormat($Row_format)
    {
        $this->Row_format = $Row_format;
    }

    /**
     * @return mixed
     */
    public function getRowFormat()
    {
        return $this->Row_format;
    }

    /**
     * @param mixed $Rows
     */
    public function setRows($Rows)
    {
        $this->Rows = $Rows;
    }

    /**
     * @return mixed
     */
    public function getRows()
    {
        return $this->Rows;
    }

    /**
     * @param mixed $Update_time
     */
    public function setUpdateTime($Update_time)
    {
        $this->Update_time = $Update_time;
    }

    /**
     * @return mixed
     */
    public function getUpdateTime()
    {
        return $this->Update_time;
    }

    /**
     * @param mixed $Version
     */
    public function setVersion($Version)
    {
        $this->Version = $Version;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->Version;
    }

}