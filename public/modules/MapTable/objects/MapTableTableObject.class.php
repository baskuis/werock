<?php

class MapTableTableObject {

    public $name;
    public $engine;
    public $version;
    public $rowFormat;
    public $rows;
    public $avgRowLength;
    public $dataLength;
    public $maxDataLength;
    public $indexLength;
    public $dataFree;
    public $autoIncrement;
    public $createTime;
    public $updateTime;
    public $checkTime;
    public $collation;
    public $checksum;
    public $createOptions;
    public $comment;

    public $columns;

    public $indexes;

    public $primaryKeyColumn;

    /**
     * @param mixed $autoIncrement
     */
    public function setAutoIncrement($autoIncrement)
    {
        $this->autoIncrement = $autoIncrement;
    }

    /**
     * @return mixed
     */
    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * @param mixed $avgRowLength
     */
    public function setAvgRowLength($avgRowLength)
    {
        $this->avgRowLength = $avgRowLength;
    }

    /**
     * @return mixed
     */
    public function getAvgRowLength()
    {
        return $this->avgRowLength;
    }

    /**
     * @param mixed $checkTime
     */
    public function setCheckTime($checkTime)
    {
        $this->checkTime = $checkTime;
    }

    /**
     * @return mixed
     */
    public function getCheckTime()
    {
        return $this->checkTime;
    }

    /**
     * @param mixed $checksum
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;
    }

    /**
     * @return mixed
     */
    public function getChecksum()
    {
        return $this->checksum;
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
     * @param mixed $createOptions
     */
    public function setCreateOptions($createOptions)
    {
        $this->createOptions = $createOptions;
    }

    /**
     * @return mixed
     */
    public function getCreateOptions()
    {
        return $this->createOptions;
    }

    /**
     * @param mixed $createTime
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
    }

    /**
     * @return mixed
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * @param mixed $dataFree
     */
    public function setDataFree($dataFree)
    {
        $this->dataFree = $dataFree;
    }

    /**
     * @return mixed
     */
    public function getDataFree()
    {
        return $this->dataFree;
    }

    /**
     * @param mixed $dataLength
     */
    public function setDataLength($dataLength)
    {
        $this->dataLength = $dataLength;
    }

    /**
     * @return mixed
     */
    public function getDataLength()
    {
        return $this->dataLength;
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
     * @param mixed $indexLength
     */
    public function setIndexLength($indexLength)
    {
        $this->indexLength = $indexLength;
    }

    /**
     * @return mixed
     */
    public function getIndexLength()
    {
        return $this->indexLength;
    }

    /**
     * @param mixed $maxDataLength
     */
    public function setMaxDataLength($maxDataLength)
    {
        $this->maxDataLength = $maxDataLength;
    }

    /**
     * @return mixed
     */
    public function getMaxDataLength()
    {
        return $this->maxDataLength;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
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
     * @param mixed $rows
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return mixed
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param mixed $updateTime
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;
    }

    /**
     * @return mixed
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return mixed
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param MapTableColumnObject $primaryKey
     */
    public function setPrimaryKeyColumn($primaryKey)
    {
        $this->primaryKeyColumn = $primaryKey;
    }

    /**
     * @return MapTableColumnObject
     */
    public function getPrimaryKeyColumn()
    {
        return $this->primaryKeyColumn;
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
     * Find primary key
     */
    public function findPrimaryKey(){

        /**
         * Check to make sure there are columns
         */
        if(empty($this->columns)){
            CoreLog::debug('No columns');
        }

        /**
         * @var MapTableColumnObject $MapTableColumnObject
         */
        foreach($this->columns as $MapTableColumnObject){
            if($MapTableColumnObject->getKey() == 'PRI'){
                self::setPrimaryKeyColumn($MapTableColumnObject);
            }
        }

    }

    /**
     * Get Extra column
     *
     * @return bool|MapTableColumnObject
     */
    public function getExtraColumn(){

        /**
         * Check to make sure there are columns
         */
        if(empty($this->columns)){
            CoreLog::debug('No columns');
        }

        /**
         * @var MapTableColumnObject $MapTableColumnObject
         */
        foreach($this->columns as $MapTableColumnObject){
            if(substr($MapTableColumnObject->getType(), 0, 7) == 'varchar'){
                if(self::getNameColumn() == $MapTableColumnObject) continue;
                if(self::getDescriptionColumn() == $MapTableColumnObject) continue;
                return $MapTableColumnObject;
            }
        }

        /**
         * Text field
         *
         * @var MapTableColumnObject $MapTableColumnObject
         */
        foreach($this->columns as $MapTableColumnObject){
            if(substr($MapTableColumnObject->getType(), 0, 4) == 'text'){
                if(self::getNameColumn() == $MapTableColumnObject) continue;
                if(self::getDescriptionColumn() == $MapTableColumnObject) continue;
                return $MapTableColumnObject;
            }
        }

        return false;

    }

    /**
     * Get Data Added
     *
     * @return bool|MapTableColumnObject
     */
    public function getDateAddedColumn(){

        /**
         * Check to make sure there are columns
         */
        if(empty($this->columns)){
            CoreLog::debug('No columns');
        }

        /**
         * @var MapTableColumnObject $MapTableColumnObject
         */
        foreach($this->columns as $MapTableColumnObject){
            if(substr($MapTableColumnObject->getType(), 0, 8) == 'datetime'){
                if(substr($MapTableColumnObject->getField(), -5) == 'added'){
                    return $MapTableColumnObject;
                }
            }
        }

        return false;

    }

    /**
     * Find name field
     *
     * @return MapTableColumnObject $MapTableColumnObject
     */
    public function getNameColumn(){

        /**
         * Check to make sure there are columns
         */
        if(empty($this->columns)){
            CoreLog::debug('No columns');
        }

        /**
         * In case of more than one name match we want
         * to choose the shorted column
         *
         * @var array $nameCandidates
         */
        $nameCandidates = array();

        /**
         * @var MapTableColumnObject $MapTableColumnObject
         */
        foreach($this->columns as $MapTableColumnObject){
            if(substr($MapTableColumnObject->getType(), 0, 7) == 'varchar'){
                if(substr($MapTableColumnObject->getField(), -4) == 'name'){
                    $nameCandidates[$MapTableColumnObject->getField()] = $MapTableColumnObject;
                }
            }
        }
        if(sizeof($nameCandidates) > 0){
            $keys = array_map('strlen', array_keys($nameCandidates));
            if(false !== array_multisort($keys, SORT_ASC, $nameCandidates)){
                return array_shift($nameCandidates);
            }
        }

        /**
         * return varchar
         */
        foreach($this->columns as $MapTableColumnObject){
            if(substr($MapTableColumnObject->getType(), 0, 7) == 'varchar'){
                return $MapTableColumnObject;
            }
        }

        return false;

    }

    /**
     * Find order field/column
     *
     * @return bool|MapTableColumnObject
     */
    public function getOrderColumn(){

        /**
         * Check to make sure there are columns
         */
        if(empty($this->columns)){
            CoreLog::debug('No columns');
        }

        /**
         * @var MapTableColumnObject $MapTableColumnObject
         */
        foreach($this->columns as $MapTableColumnObject) {
            if (substr($MapTableColumnObject->getType(), 0, 3) == 'int') {
                if (substr($MapTableColumnObject->getField(), -10) == 'orderfield') {
                    return $MapTableColumnObject;
                }
                if (substr($MapTableColumnObject->getField(), -5) == 'order') {
                    return $MapTableColumnObject;
                }
            }
        }

        return false;

    }

    /**
     * Find description field
     *
     * @return MapTableColumnObject $MapTableColumnObject
     */
    public function getDescriptionColumn(){

        /**
         * Check to make sure there are columns
         */
        if(empty($this->columns)){
            CoreLog::debug('No columns');
        }

        /**
         * Text field with type of description
         *
         * @var MapTableColumnObject $MapTableColumnObject
         */
        foreach($this->columns as $MapTableColumnObject){
            if(substr($MapTableColumnObject->getType(), 0, 4) == 'text'){
                if(substr($MapTableColumnObject->getField(), -11) == 'description'){
                    return $MapTableColumnObject;
                }
            }
        }

        /**
         * Text field
         *
         * @var MapTableColumnObject $MapTableColumnObject
         */
        foreach($this->columns as $MapTableColumnObject){
            if(substr($MapTableColumnObject->getType(), 0, 4) == 'text'){
                return $MapTableColumnObject;
            }
        }

        /**
         * Varchar
         *
         * @var MapTableColumnObject $MapTableColumnObject
         */
        foreach($this->columns as $MapTableColumnObject){
            if(substr($MapTableColumnObject->getType(), 0, 7) == 'varchar'){
                return $MapTableColumnObject;
            }
        }

        return false;

    }

    /**
     * Does this table have a primary key
     *
     * @return bool
     */
    public function hasPrimaryKey(){
        return !empty($this->primaryKeyColumn);
    }

}