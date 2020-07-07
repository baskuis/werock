<?php

/**
 * Map Table Context Object
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MapTableContextObject {

    /**
     * Enable reflection
     */
    use CoreInterceptorTrait;
    use ClassReflectionTrait;

    /** @var string $table */
    public $table = null;

    /** @var string $template */
    public $template = 'maptabledefault';

    /** @var string $listingTemplate */
    public $listingTemplate = 'maptablelisting';

    /** @var string $listingHeaderTemplate */
    public $listingHeaderTemplate = 'maptablelistingheader';

    /** @var string $listingPaginationTop */
    public $listingPaginationTop = 'maptablelistingpaginationtop';

    /** @var string $listingPaginationBottom */
    public $listingPaginationBottom = 'maptablelistingpaginationbottom';

    /** @var MapTableTableObject $MapTableTableObject */
    public $MapTableTableObject = null;

    /** @var MapTableColumnObject $primaryKeyColumn */
    public $primaryKeyColumn = null;

    /** @var CorePaginationObject $CorePaginationObject */
    public $CorePaginationObject = null;

    /** @var string $primaryValue */
    public $primaryValue = null;

    /** @var string $action */
    public $action = null;

    /** @var string $searchQuery */
    public $searchQuery = null;

    /** @var FormUI $FormUI */
    public $FormUI = null;

    /** @var array $row */
    public $row = array();

    /** @var array $stickyFields */
    public $stickyFields = array();

    /** @var array $relatedTables */
    public $relatedTables = array();

    /** @var array $associatedTables */
    public $associatedTables = array();

    /**
     * Table context builder
     * Set values from request
     */
    function __construct(){
        $this->action = isset($_GET['action']) ? $_GET['action'] : null;
        $this->primaryValue = isset($_GET['primary_value']) ? (int) $_GET['primary_value'] : null;
        $this->searchQuery = isset($_GET['search']) ? $_GET['search'] : null;
    }

    /**
     * Add related field
     *
     * @param MapTableRelatedTableDescriptionObject $MapTableRelatedTableDescriptionObject
     */
    function addRelatedTable(MapTableRelatedTableDescriptionObject $MapTableRelatedTableDescriptionObject){
        $this->relatedTables[$MapTableRelatedTableDescriptionObject->getTable()] = $MapTableRelatedTableDescriptionObject;
    }

    /**
     * Add associated table
     *
     * @param MapTableAssociatedTableDescriptionObject $mapTableAssociatedTableObject
     */
    function addAssociatedTable(MapTableAssociatedTableDescriptionObject $mapTableAssociatedTableObject){
        $this->associatedTables[$mapTableAssociatedTableObject->getTable()] = $mapTableAssociatedTableObject;
    }

    /**
     * Get related field description
     * allow interceptors to wrap this method
     *
     * @param null $table
     * @return bool
     */
    function _getRelatedTable($table = null){
        return (isset($this->relatedTables[$table])) ? $this->relatedTables[$table] : false;
    }

    /**
     * Have search
     *
     * @return bool
     */
    public function haveSearch(){
        return !empty($this->searchQuery);
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
     * @param MapTableTableObject $MapTableTableObject
     */
    public function setMapTableTableObject(MapTableTableObject $MapTableTableObject)
    {
        $this->MapTableTableObject = $MapTableTableObject;
    }

    /**
     * Do we have a table object set?
     *
     * @return bool
     */
    public function haveMapTableTableObject(){
        return !empty($this->MapTableTableObject);
    }

    /**
     * @return MapTableTableObject
     */
    public function getMapTableTableObject()
    {
        return $this->MapTableTableObject;
    }

    /**
     * @param null $primaryKey
     */
    public function setPrimaryKeyColumn($primaryKey)
    {
        $this->primaryKeyColumn = $primaryKey;
    }

    /**
     * @return null
     */
    public function getPrimaryKeyColumn()
    {
        return $this->primaryKeyColumn;
    }

    /**
     * @param null $primaryValue
     */
    public function setPrimaryValue($primaryValue)
    {
        $this->primaryValue = $primaryValue;
    }

    /**
     * @return null
     */
    public function getPrimaryValue()
    {
        return $this->primaryValue;
    }

    /**
     * @param FormUI $FormUI
     */
    public function setFormUI($FormUI)
    {
        $this->FormUI = $FormUI;
    }

    /**
     * @return FormUI
     */
    public function getFormUI()
    {
        return $this->FormUI;
    }

    /**
     * @param string $searchQuery
     */
    public function setSearchQuery($searchQuery)
    {
        $this->searchQuery = $searchQuery;
    }

    /**
     * @return string
     */
    public function getSearchQuery()
    {
        return $this->searchQuery;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Find primarykey
     */
    public function findPrimaryKey(){

        /**
         * Check to see if map table table object has already been set
         */
        if(!$this->haveMapTableTableObject()){
            CoreLog::error('Unable to find primary key');
        }

        /**
         * Find primary key
         */
        $this->primaryKeyColumn = $this->MapTableTableObject->getPrimaryKeyColumn();

    }

    /**
     * @param array $row
     */
    public function setRow($row)
    {
        $this->row = $row;
    }

    /**
     * @return array
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @param string $listingHeaderTemplate
     */
    public function setListingHeaderTemplate($listingHeaderTemplate)
    {
        $this->listingHeaderTemplate = $listingHeaderTemplate;
    }

    /**
     * @return string
     */
    public function getListingHeaderTemplate()
    {
        return $this->listingHeaderTemplate;
    }

    /**
     * @param string $listingTemplate
     */
    public function setListingTemplate($listingTemplate)
    {
        $this->listingTemplate = $listingTemplate;
    }

    /**
     * @return string
     */
    public function getListingTemplate()
    {
        return $this->listingTemplate;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $listingPaginationBottom
     */
    public function setListingPaginationBottom($listingPaginationBottom)
    {
        $this->listingPaginationBottom = $listingPaginationBottom;
    }

    /**
     * @return string
     */
    public function getListingPaginationBottom()
    {
        return $this->listingPaginationBottom;
    }

    /**
     * @param string $listingPaginationTop
     */
    public function setListingPaginationTop($listingPaginationTop)
    {
        $this->listingPaginationTop = $listingPaginationTop;
    }

    /**
     * @return string
     */
    public function getListingPaginationTop()
    {
        return $this->listingPaginationTop;
    }

    /**
     * @param \CorePaginationObject $CorePaginationObject
     */
    public function setCorePaginationObject($CorePaginationObject)
    {
        $this->CorePaginationObject = $CorePaginationObject;
    }

    /**
     * @return \CorePaginationObject
     */
    public function getCorePaginationObject()
    {
        return $this->CorePaginationObject;
    }

    /**
     * @return array
     */
    public function getStickyFields()
    {
        return $this->stickyFields;
    }

    /**
     * @param array $stickyFields
     */
    public function setStickyFields($stickyFields)
    {
        $this->stickyFields = $stickyFields;
    }

    /**
     * @return array
     */
    public function getRelatedTables()
    {
        return $this->relatedTables;
    }

    /**
     * @param array $relatedTables
     */
    public function setRelatedTables($relatedTables)
    {
        $this->relatedTables = $relatedTables;
    }

    /**
     * Allow interceptors
     *
     * @return array
     */
    public function _getAssociatedTables()
    {
        return $this->associatedTables;
    }

    /**
     * @param array $associatedTables
     */
    public function setAssociatedTables($associatedTables)
    {
        $this->associatedTables = $associatedTables;
    }

}