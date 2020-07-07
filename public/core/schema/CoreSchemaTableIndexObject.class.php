<?php

/**
 * Core Schema Index Object
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
class CoreSchemaTableIndexObject {

    public $nonUnique;
    public $keyName;
    public $seqInIndex;
    public $columnName;
    public $collation;
    public $cardinality;
    public $subPart;
    public $packed;
    public $null;
    public $indexType;
    public $comment;
    public $indexComment;

    /**
     * @param mixed $cardinality
     */
    public function setCardinality($cardinality)
    {
        $this->cardinality = $cardinality;
    }

    /**
     * @return mixed
     */
    public function getCardinality()
    {
        return $this->cardinality;
    }

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
     * @param mixed $columnName
     */
    public function setColumnName($columnName)
    {
        if(!$this->columnName) $this->columnName = array();
        array_push($this->columnName, $columnName);
    }

    /**
     * @return mixed
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $indexComment
     */
    public function setIndexComment($indexComment)
    {
        $this->indexComment = $indexComment;
    }

    /**
     * @return mixed
     */
    public function getIndexComment()
    {
        return $this->indexComment;
    }

    /**
     * @param mixed $indexType
     */
    public function setIndexType($indexType)
    {
        $this->indexType = $indexType;
    }

    /**
     * @return mixed
     */
    public function getIndexType()
    {
        return $this->indexType;
    }

    /**
     * @param mixed $keyName
     */
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;
    }

    /**
     * @return mixed
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    /**
     * @param mixed $nonUnique
     */
    public function setNonUnique($nonUnique)
    {
        $this->nonUnique = $nonUnique;
    }

    /**
     * @return mixed
     */
    public function getNonUnique()
    {
        return $this->nonUnique;
    }

    /**
     * @param mixed $null
     */
    public function setNull($null)
    {
        $this->null = $null;
    }

    /**
     * @return mixed
     */
    public function getNull()
    {
        return $this->null;
    }

    /**
     * @param mixed $packed
     */
    public function setPacked($packed)
    {
        $this->packed = $packed;
    }

    /**
     * @return mixed
     */
    public function getPacked()
    {
        return $this->packed;
    }

    /**
     * @param mixed $seqInIndex
     */
    public function setSeqInIndex($seqInIndex)
    {
        $this->seqInIndex = $seqInIndex;
    }

    /**
     * @return mixed
     */
    public function getSeqInIndex()
    {
        return $this->seqInIndex;
    }

    /**
     * @param mixed $subPart
     */
    public function setSubPart($subPart)
    {
        $this->subPart = $subPart;
    }

    /**
     * @return mixed
     */
    public function getSubPart()
    {
        return $this->subPart;
    }

}