<?php

class MapTableManyManyRelationshipObject {

    /** @var MapTableTableObject $anchorTable */
    public $anchorTable;

    /** @var MapTableTableObject $relationshipTable */
    public $relationshipTable;

    /** @var MapTableTableObject $lookupTable */
    public $lookupTable;

    /** @var MapTableColumnObject $constraintColumn */
    public $constraintColumn;

    /**
     * Helper method to see if relationship table
     * is relevant to passed table
     *
     * @param MapTableTableObject $mapTableTableObject
     * @return bool
     */
    public function isRelevantTo(MapTableTableObject $mapTableTableObject){
        if($mapTableTableObject->getName() == $this->anchorTable->getName() || $mapTableTableObject->getName() == $this->lookupTable->getName()){
            return true;
        }
        return false;
    }

    /**
     * Modify based on context
     *
     * @param MapTableContextObject $mapTableContextObject
     */
    public function modifiyForContext(MapTableContextObject $mapTableContextObject){
        if($mapTableContextObject->getMapTableTableObject()->getPrimaryKeyColumn()->getField() != $this->anchorTable->getPrimaryKeyColumn()->getField()){
            if($mapTableContextObject->getMapTableTableObject()->getPrimaryKeyColumn()->getField() == $this->lookupTable->getPrimaryKeyColumn()->getField()){
                $tempAnchor = $this->anchorTable;
                $this->anchorTable = $this->lookupTable;
                $this->lookupTable = $tempAnchor;
            }else {
                CoreLog::error('Unable to modify many many relationship based on context');
            }
        }
    }

    /**
     * @return MapTableTableObject
     */
    public function getAnchorTable()
    {
        return $this->anchorTable;
    }

    /**
     * @param MapTableTableObject $anchorTable
     */
    public function setAnchorTable($anchorTable)
    {
        $this->anchorTable = $anchorTable;
    }

    /**
     * @return MapTableTableObject
     */
    public function getRelationshipTable()
    {
        return $this->relationshipTable;
    }

    /**
     * @param MapTableTableObject $relationshipTable
     */
    public function setRelationshipTable($relationshipTable)
    {
        $this->relationshipTable = $relationshipTable;
    }

    /**
     * @return MapTableTableObject
     */
    public function getLookupTable()
    {
        return $this->lookupTable;
    }

    /**
     * @param MapTableTableObject $lookupTable
     */
    public function setLookupTable($lookupTable)
    {
        $this->lookupTable = $lookupTable;
    }

    /**
     * @return MapTableColumnObject
     */
    public function getConstraintColumn()
    {
        return $this->constraintColumn;
    }

    /**
     * @param MapTableColumnObject $constraintColumn
     */
    public function setConstraintColumn($constraintColumn)
    {
        $this->constraintColumn = $constraintColumn;
    }

}