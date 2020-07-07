<?php

class MapTableIndexObject {

    public $table;
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
        $this->columnName = $columnName;
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

}