<?php

class MapTableLightContextObject {

    /** @var MapTableColumnObject $primaryKey */
    public $primaryKey;

    /** @var string $primaryValue */
    public $primaryValue;

    /**
     * @param MapTableContextObject $mapTableContextObject
     */
    public function setContext(MapTableContextObject $mapTableContextObject){

        /** @var MapTableColumnObject primaryKey */
        $this->primaryKey = $mapTableContextObject->getPrimaryKeyColumn();
        $this->primaryValue = $mapTableContextObject->getPrimaryValue();

    }

    /**
     * @return MapTableColumnObject
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param MapTableColumnObject $primaryKey
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * @return string
     */
    public function getPrimaryValue()
    {
        return $this->primaryValue;
    }

    /**
     * @param string $primaryValue
     */
    public function setPrimaryValue($primaryValue)
    {
        $this->primaryValue = $primaryValue;
    }

}