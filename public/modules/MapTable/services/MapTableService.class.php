<?php

/**
 * Map Table Service
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MapTableService {

    use ClassReflectionTrait;
    use CoreInterceptorTrait;

    /**
     * Context
     *
     * @var MapTableContextObject
     */
    var $context = null;

    /**
     * Array of tables
     * keyed by table name
     *
     * @var array
     */
    var $tables = array();

    /**
     * Table object mappings
     * @var array $tableObjectMappings
     */
    var $tableObjectMappings = array();

    /**
     * Object table mappings
     * @var array $objectTableMappings
     */
    var $objectTableMappings = array();

    /**
     * Array of tables keyed
     * by primary key if available
     *
     * @var array
     */
    var $tablesByPrimaryKey = array();

    /**
     * Map of table arrays which contain the
     * primary key listed in the lookup which is
     * not the tables primary key
     *
     * @var array
     */
    var $tablesContainingPrimaryKey = array();

    /**
     * Tables which hold relationship
     * and don't have a primary key
     *
     * @var array
     */
    var $relationshipTables = array();

    /**
     * Mapping array
     *
     * @var array
     */
    var $mapping = array();

    /**
     * List of actions
     */
    const ACTION_TYPE_CREATE = 'create';
    const ACTION_TYPE_EDIT = 'edit';
    const ACTION_TYPE_DELETE = 'delete';
    
    /**
     * Keys
     */
    const KEY_PAGINATION = 'pagination';
    const KEY_LISTINGS = 'listings';
    const KEY_DELETE = 'delete';
    const KEY_METHOD = 'method';
    const ATTR_POST = 'post';
    const QUERY_PRIMARY_VALUE = 'primary_value';
    const QUERY_ACTION = 'action';
    const KEY_ID = 'id';
    const TYPE_ENUM = 'enum';
    const PRIMARY_KEY = 'PRI';
    const COLUMN_NAME = 'Name';
    const COLUMN_ENGINE = 'Engine';
    const COLUMN_VERSION = 'Version';
    const COLUMN_ROW_FORMAT = 'Row_format';
    const COLUMN_ROWS = 'Rows';
    const COLUMN_AVG_ROW_LENGTH = 'Avg_row_length';
    const COLUMN_DATA_LENGTH = 'Data_length';
    const COLUMN_INDEX_LENGTH = 'Index_length';
    const COLUMN_DATA_FREE = 'Data_free';
    const COLUMN_AUTO_INCREMENT = 'Auto_increment';
    const COLUMN_CREATE_TIME = 'Create_time';
    const COLUMN_UPDATE_TIME = 'Update_time';
    const COLUMN_CHECK_TIME = 'Check_time';
    const COLUMN_COLLATION = 'Collation';
    const COLUMN_CHECKSUM = 'Checksum';
    const COLUMN_CREATE_OPTIONS = 'Create_options';
    const COLUMN_COMMENT = 'Comment';
    const COLUMN_FIELD = 'Field';
    const COLUMN_TYPE = 'Type';
    const COLUMN_NULL = 'Null';
    const COLUMN_KEY = 'Key';
    const COLUMN_DEFAULT = 'Default';
    const COLUMN_EXTRA = 'Extra';
    const COLUMN_TABLE = 'Table';
    const COLUMN_TABLE_LC = 'table';
    const COLUMN_NON_UNIQUE = 'Non_unique';
    const COLUMN_KEY_NAME = 'Key_name';
    const COLUMN_SEQ_IN_INDEX = 'Seq_in_index';
    const COLUMN_COLUMN_NAME = 'Column_name';
    const COLUMN_CARDINALITY = 'Cardinality';
    const COLUMN_SUP_PART = 'Sub_part';
    const COLUMN_PACKED = 'Packed';
    const COLUMN_INDEX_TYPE = 'Index_type';
    const COLUMN_INDEX_COMMENT = 'Index_comment';
    const MATCH_ALL_CHARACTER = '*';
    
    /**
     * Object names
     * 
     */
    const MapTableTableObject = 'MapTableTableObject';
    const MapTableColumnObject = 'MapTableColumnObject';
    const MapTableActionModifierObject = 'MapTableActionModifierObject';
    const MapTableContextObject = 'MapTableContextObject';
    const MapTableManyManyRelationshipObject = 'MapTableManyManyRelationshipObject';
    
    /**
     * Associated table name prepend
     * format: 'associated-field-{tablename}-{id}'
     */
    const ASSOCIATED_TABLE_NAME_PREPEND = 'associated-field-';
    const ASSOCIATED_TABLE_REGEX = '/associated-field-\(([^\]]+)\)-\(([0-9]+)\)/i';

    /**
     * Cache timeout
     */
    const CACHE_DURATION = 86400;
    
    /**
     * Cache namespace
     */
    var $cacheNS = array('maptable');

    /** @var MapTableRepository $MapTableRepository */
    private $MapTableRepository = null;

    /**
     * Map table service constructor
     */
    function __construct(){
        $this->MapTableRepository = CoreLogic::getRepository('MapTableRepository');
    }

    /**
     * Add table object mapping
     *
     * @param string $table
     * @param string $object
     */
    public function addTableObjectMapping($table = null, $object = null){
        if(isset($this->tableObjectMappings[$table])) CoreLog::error('Already have ' . $this->tableObjectMappings[$table] . ' mapped for table: ' . $table);
        $this->tableObjectMappings[$table] = $object;
        $this->objectTableMappings[$object] = $table;
    }

    /**
     * Get mapped object
     *
     * @param null $table
     * @return bool
     */
    public function getMappedObject($table = null){
        return isset($this->tableObjectMappings[$table]) ? $this->tableObjectMappings[$table] : false;
    }

    /**
     * Get mapped table
     *
     * @param null $object
     * @return bool
     */
    public function getMappedTable($object = null){
        if(is_object($object)) $object = get_class($object);
        return isset($this->objectTableMappings[$object]) ? $this->objectTableMappings[$object] : false;
    }

    /**
     * Add mapping
     *
     * @param MapTableMapColumnObject $MapTableMapColumnObject
     */
    public function addMapping(MapTableMapColumnObject $MapTableMapColumnObject){
        if(isset($this->mapping[$MapTableMapColumnObject->getId()])) CoreLog::debug('Overloading maptable mapping by key: ' . $MapTableMapColumnObject->getId());
        $this->mapping[$MapTableMapColumnObject->getId()] = $MapTableMapColumnObject;
    }

    /**
     * Get m
     *
     * @return array
     */
    public function getMappings(){
        return $this->mapping;
    }

    /**
     * Set context
     *
     * @param MapTableContextObject $MapTableContextObject
     */
    public function setContext(MapTableContextObject $MapTableContextObject){
        $this->context = $MapTableContextObject;
    }

    /**
     * Map tables
     */
    public function mapTables(){

        /**
         * Cache lookup
         */
        $cacheKey = 'maptable:tables';
        $cackeKeyPrimary = 'maptable:tables:primary';

        /**
         * Assure Objects Loaded
         * so that un serializing works in APC
         */
        CoreLogic::getObject(self::MapTableTableObject);
        CoreLogic::getObject(self::MapTableColumnObject);

        /**
         * Check cached values first
         */
        if(
            false !== ($tables = CoreCache::getCache($cacheKey, true, $this->cacheNS, false)) &&
            false !== ($tablesByPrimaryKey = CoreCache::getCache($cackeKeyPrimary, true, $this->cacheNS, false))
        ){

            /**
             * Set tables reference
             * from cached value
             */
            $this->tables = $tables;
            $this->tablesByPrimaryKey = $tablesByPrimaryKey;

        }else{

            $tableRows = $this->MapTableRepository->getAllTables();

            /**
             * Map all tables
             */
            foreach($tableRows as $table){

                /** @var MapTableTableObject $MapTableTableObject */
                $MapTableTableObject = CoreLogic::getObject(self::MapTableTableObject);

                /**
                 * Populate table object
                 */
                $MapTableTableObject->setName($table[self::COLUMN_NAME]);
                $MapTableTableObject->setEngine($table[self::COLUMN_ENGINE]);
                $MapTableTableObject->setVersion($table[self::COLUMN_VERSION]);
                $MapTableTableObject->setRowFormat($table[self::COLUMN_ROW_FORMAT]);
                $MapTableTableObject->setRows($table[self::COLUMN_ROWS]);
                $MapTableTableObject->setAvgRowLength($table[self::COLUMN_AVG_ROW_LENGTH]);
                $MapTableTableObject->setDataLength($table[self::COLUMN_DATA_LENGTH]);
                $MapTableTableObject->setIndexLength($table[self::COLUMN_INDEX_LENGTH]);
                $MapTableTableObject->setDataFree($table[self::COLUMN_DATA_FREE]);
                $MapTableTableObject->setAutoIncrement($table[self::COLUMN_AUTO_INCREMENT]);
                $MapTableTableObject->setCreateTime($table[self::COLUMN_CREATE_TIME]);
                $MapTableTableObject->setUpdateTime($table[self::COLUMN_UPDATE_TIME]);
                $MapTableTableObject->setCheckTime($table[self::COLUMN_CHECK_TIME]);
                $MapTableTableObject->setCollation($table[self::COLUMN_COLLATION]);
                $MapTableTableObject->setCheckSum($table[self::COLUMN_CHECKSUM]);
                $MapTableTableObject->setCreateOptions($table[self::COLUMN_CREATE_OPTIONS]);
                $MapTableTableObject->setComment($table[self::COLUMN_COMMENT]);

                /**
                 * Add column data
                 */
                $MapTableTableObject->setColumns(self::mapColumns($MapTableTableObject));

                /**
                 * Add indexes data
                 */
                $MapTableTableObject->setIndexes(self::mapIndexes($MapTableTableObject));

                /**
                 * Set primary key for easy access
                 */
                $MapTableTableObject->findPrimaryKey();

                /**
                 * Stack table reference
                 */
                $this->tables[$MapTableTableObject->getName()] = $MapTableTableObject;

                /** @var MapTableColumnObject $PrimaryKeyColumn */
                $PrimaryKeyColumn = $MapTableTableObject->getPrimaryKeyColumn();
                if(!empty($PrimaryKeyColumn)) $this->tablesByPrimaryKey[$PrimaryKeyColumn->getField()] = $MapTableTableObject;

            }

            /**
             * Store cached
             */
            CoreCache::saveCache($cacheKey, $this->tables, self::CACHE_DURATION, true, $this->cacheNS, false);
            CoreCache::saveCache($cackeKeyPrimary, $this->tablesByPrimaryKey, self::CACHE_DURATION, true, $this->cacheNS, false);

        }

        /**
         * Set table reference on context
         */
        if(!empty($this->context)){

            /**
             * Assertion
             */
            if(!isset($this->tables[$this->context->getTable()])){
                CoreLog::error('Unable to find table ' . $this->context->getTable());
            }

            /**
             * This table
             */
            $MapTableTableObject = $this->tables[$this->context->getTable()];

            /**
             * Set reference in context
             */
            $this->context->setMapTableTableObject($MapTableTableObject);

            /**
             * Find the primary key
             */
            $this->context->findPrimaryKey();

        }

        /**
         * Map tables with primary reference
         */
        self::mapTablesWithPrimaryReference();

        /**
         * Map tables that store relations
         */
        self::mapTablesWithRelationships();

        /**
         * Return
         */
        return true;

    }

    /**
     * Map tables that store relationships
     *
     * @return bool
     */
    public function mapTablesWithRelationships(){

        /**
         * Cache lookup
         */
        $cacheKey = 'maptable:tables:manymanyrels';

        /**
         * Attempt to restore cached value first
         */
        if(false !== ($relationshipTables = CoreCache::getCache($cacheKey, true, $this->cacheNS, false))){

            $this->relationshipTables = $relationshipTables;

        }else{

            /**
             * @var string $table_name
             * @var MapTableTableObject $MapTableTableObject
             */
            foreach ($this->tables as $table_name => $MapTableTableObject) {
                if ($MapTableTableObject->getPrimaryKeyColumn() == null) {

                    /** @var MapTableColumnObject $key1 */
                    $key1 = null;
                    /** @var MapTableColumnObject $key2 */
                    $key2 = null;
                    /** @var MapTableColumnObject $constraintColumn */
                    $constraintColumn = null;

                    /** @var MapTableColumnObject $MapTableColumnObject */
                    foreach ($MapTableTableObject->getColumns() as $MapTableColumnObject) {

                        /**
                         * Skip constraints
                         * these could not be considered ids
                         * TODO: Support multiple constraints
                         */
                        if($MapTableColumnObject->getComment() == 'constraint'){
                            if($MapTableColumnObject->getNull() != 'YES'){
                                CoreLog::error('Constraint must allow null to prevent default key=PRI when part of unique index');
                            }
                            $constraintColumn = $MapTableColumnObject;
                            continue;
                        }

                        /**
                         * Otherwise find keys
                         */
                        if (!empty($key1) && empty($key2) && isset($this->tablesByPrimaryKey[$MapTableColumnObject->getField()])) {
                            $key2 = $MapTableColumnObject;
                        }
                        if (empty($key1) && isset($this->tablesByPrimaryKey[$MapTableColumnObject->getField()])) {
                            $key1 = $MapTableColumnObject;
                        }

                    }

                    /** see if we have a relationship table */
                    if (
                        $key1 && $key2 &&
                        (
                            $key1->getField() == $this->context->getMapTableTableObject()->getPrimaryKeyColumn()->getField()
                            ||
                            $key2->getField() == $this->context->getMapTableTableObject()->getPrimaryKeyColumn()->getField()
                        )
                    ) {

                        /** @var MapTableManyManyRelationshipObject $MapTableManyManyRelationshipObject */
                        $MapTableManyManyRelationshipObject = CoreLogic::getObject(self::MapTableManyManyRelationshipObject);
                        $MapTableManyManyRelationshipObject->setRelationshipTable($MapTableTableObject);
                        if (isset($this->tablesByPrimaryKey[$key1->getField()])) {
                            $MapTableManyManyRelationshipObject->setAnchorTable($this->tablesByPrimaryKey[$key1->getField()]);
                        } else {
                            CoreLog::error('Unable to find anchor table by primary key: ' . $key1->getField());
                        }
                        if (isset($this->tablesByPrimaryKey[$key2->getField()])) {
                            $MapTableManyManyRelationshipObject->setLookupTable($this->tablesByPrimaryKey[$key2->getField()]);
                        } else {
                            CoreLog::error('Unable fo find lookup table by primary key: ' . $key2->getField());
                        }
                        if( isset($this->tablesByPrimaryKey[$constraintColumn->getField()])){
                            $MapTableManyManyRelationshipObject->setConstraintColumn($constraintColumn);
                        }

                        /**
                         * Save the relationship table reference
                         */
                        $this->relationshipTables[$MapTableTableObject->getName()] = $MapTableManyManyRelationshipObject;

                    }

                }
            }

            /**
             * Store cached
             */
            CoreCache::saveCache($cacheKey, $this->relationshipTables, self::CACHE_DURATION, true, $this->cacheNS, false);

        }

        return true;

    }

    /**
     * Map table which contain a primary key as
     * a regular column
     *
     *
     * @return bool
     */
    private function mapTablesWithPrimaryReference(){

        /** sanity check */
        if(empty($this->tablesByPrimaryKey)){
            CoreLog::error('Need reference of tables by primary key');
        }

        /**
         * Cache lookup
         */
        $cacheKey = 'maptable:tables:pri:ref';

        /** attempt to do cache lookup and set value */
        if(false !== ($tablesContainingPrimaryKey = CoreCache::getCache($cacheKey, true, $this->cacheNS, false))){

            /** @var array tablesContainingPrimaryKey */
            $this->tablesContainingPrimaryKey = $tablesContainingPrimaryKey;

        }else{

            /**
             * @var String $primary_key
             * @var MapTableTableObject $MapTableTableObject
             */
            foreach($this->tablesByPrimaryKey as $primary_key => $MapTableTableObject){

                /** @var array $tables all tables which have primary key as a 'regular' column */
                $tables = array();

                /**
                 * @var String $lookup_primary_key
                 * @var MapTableTableObject $LookupMapTableTableObject
                 */
                foreach($this->tablesByPrimaryKey as $lookup_primary_key => $LookupMapTableTableObject){
                    $columns = $LookupMapTableTableObject->getColumns();
                    if(!empty($columns)){
                        /** @var MapTableColumnObject $column */
                        foreach($columns as $column){
                            if($column->getKey() == self::PRIMARY_KEY) continue;
                            if($column->getField() == $primary_key){
                                array_push($tables, $LookupMapTableTableObject);
                            }
                        }
                    }

                }
                if(!empty($tables)){
                    $this->tablesContainingPrimaryKey[$primary_key] = $tables;
                }

            }

            CoreCache::saveCache($cacheKey, $this->tablesContainingPrimaryKey, self::CACHE_DURATION, true, $this->cacheNS, false);

        }

        return true;

    }

    /**
     * Return table for primary key
     *
     * @param MapTableColumnObject $columnObject
     * @return MapTableTableObject or boolean
     */
    private function tableByPrimaryKey(MapTableColumnObject $columnObject){
        if(empty($this->tablesByPrimaryKey)){
            CoreLog::error('$this->tablesByPrimaryKey empty!');
        }
        if(!isset($this->tablesByPrimaryKey[$columnObject->getField()])){
            return false;
        }
        return $this->tablesByPrimaryKey[$columnObject->getField()];
    }

    /**
     * Map table columns
     *
     * @param MapTableTableObject $MapTableTableObject
     * @return array|mixed
     */
    private function mapColumns(MapTableTableObject $MapTableTableObject){

        /**
         * Cache lookup
         */
        $cacheKey = 'maptable:columns:' . $MapTableTableObject->getName();

        /** attempt to do cache lookup and set value */
        if(false !== ($columns = CoreCache::getCache($cacheKey, true, $this->cacheNS, false))) {
            return $columns;
        }

        /**
         * Get them from the repository
         */
        $columnRows = $this->MapTableRepository->getAllColumns($MapTableTableObject->getName());

        /**
         * Columns
         */
        $columns = array();

        /**
         * Map all columns
         */
        foreach($columnRows as $column){

            /** @var MapTableColumnObject $MapTableColumnObject */
            $MapTableColumnObject = CoreLogic::getObject(self::MapTableColumnObject);

            /**
             * Populate the column object reference
             */
            $MapTableColumnObject->setField($column[self::COLUMN_FIELD]);
            $MapTableColumnObject->setType($column[self::COLUMN_TYPE]);
            $MapTableColumnObject->setCollation($column[self::COLUMN_COLLATION]);
            $MapTableColumnObject->setNull($column[self::COLUMN_NULL]);
            $MapTableColumnObject->setKey($column[self::COLUMN_KEY]);
            $MapTableColumnObject->setDefault($column[self::COLUMN_DEFAULT]);
            $MapTableColumnObject->setExtra($column[self::COLUMN_EXTRA]);
            $MapTableColumnObject->setComment($column[self::COLUMN_COMMENT]);

            /**
             * Key by field
             * will retain order
             */
            $columns[$MapTableColumnObject->getField()] = $MapTableColumnObject;

        }

        /**
         * Save cache
         */
        CoreCache::saveCache($cacheKey, $columns, self::CACHE_DURATION, true, $this->cacheNS, false);

        /**
         * Return columns
         */
        return $columns;

    }

    /**
     * Map indexes
     *
     * @param MapTableTableObject $MapTableTableObject
     * @return array
     */
    private function mapIndexes(MapTableTableObject $MapTableTableObject){

        /**
         * Cache lookup
         */
        $cacheKey = 'maptable:indexes:' . $MapTableTableObject->getName();

        /** attempt to do cache lookup and set value */
        if(false !== ($indexes = CoreCache::getCache($cacheKey, true, $this->cacheNS, false))) {
            return $indexes;
        }

        /**
         * Indexes
         */
        $indexes = array();

        $indexRows = $this->MapTableRepository->getAllIndexes($MapTableTableObject->getName());

        /**
         * Step though the rows
         * and map to object
         */
        foreach($indexRows as $index){

            /**
             * Get index object
             * @var MapTableIndexObject $MapTableIndexObject
             */
            $MapTableIndexObject = CoreLogic::getObject('MapTableIndexObject');

            /**
             * Populate index
             */
            $MapTableIndexObject->setTable($index[self::COLUMN_TABLE]);
            $MapTableIndexObject->setNonUnique($index[self::COLUMN_NON_UNIQUE]);
            $MapTableIndexObject->setKeyName($index[self::COLUMN_KEY_NAME]);
            $MapTableIndexObject->setSeqInIndex($index[self::COLUMN_SEQ_IN_INDEX]);
            $MapTableIndexObject->setColumnName($index[self::COLUMN_COLUMN_NAME]);
            $MapTableIndexObject->setCollation($index[self::COLUMN_COLLATION]);
            $MapTableIndexObject->setCardinality($index[self::COLUMN_CARDINALITY]);
            $MapTableIndexObject->setSubPart($index[self::COLUMN_SUP_PART]);
            $MapTableIndexObject->setPacked($index[self::COLUMN_PACKED]);
            $MapTableIndexObject->setNull($index[self::COLUMN_NULL]);
            $MapTableIndexObject->setIndexType($index[self::COLUMN_INDEX_TYPE]);
            $MapTableIndexObject->setIndexComment($index[self::COLUMN_INDEX_COMMENT]);

            /**
             * Push to array
             */
            array_push($indexes, $MapTableIndexObject);

        }

        /**
         * Save cache
         */
        CoreCache::saveCache($cacheKey, $indexes, self::CACHE_DURATION, true, $this->cacheNS, false);

        /**
         * Return indexes
         */
        return $indexes;

    }

    /**
     * populate selected row
     */
    private function populateSelectedRow(){

        /** Only if we have primary value */
        if(empty($this->context->primaryValue)){
            return;
        }

        /** @var MapTableColumnObject $PrimaryKeyColumn */
        $PrimaryKeyColumn = $this->context->getPrimaryKeyColumn();

        /**
         * We need a primary key at this point
         * if this condition validates to true
         * perhaps this table doens't have a
         * primary key
         */
        if(empty($PrimaryKeyColumn)){
            CoreLog::error('Unable to find primary key in context');
            return;
        }

        /** Set Row */
        $this->context->setRow($this->MapTableRepository->getRecordFromContext($this->context));

    }

    /**
     * Build the form
     *
     * @return bool
     */
    public function buildForm(){

        /**
         * FormUI $FormUI
         */
        $FormUI = CoreForm::register($this->context->table, array(self::QUERY_ACTION => '', self::KEY_METHOD => self::ATTR_POST));

        /**
         * Setting formUI in context
         */
        $this->context->setFormUI($FormUI);

        /**
         * Populate selected row
         * if needed - will be skipped
         * if no primary value present
         */
        self::populateSelectedRow();

        /**
         * Build Top Form controls
         */
        if(false !== ($controls = self::buildTopFormControls())){
            foreach($controls as $FormField){
                $this->context->getFormUI()->addField($FormField);
            }
        }

        /**
         * Columns
         * @var MapTableColumnObject $column
         */
        foreach($this->context->getMapTableTableObject()->getColumns() as $column){

            /**
             * Add form field
             */
            if(false !== ($FormField = self::buildFormField($column))){
                $this->context->getFormUI()->addField($FormField);
            }

        }

        /**
         * Associated tables
         */
        if(false !== ($associatedTables = self::buildAssociatedTableBlocks())){
            foreach($associatedTables as $FormField){
                $this->context->getFormUI()->addField($FormField);
            }
        }

        /**
         * Related tables
         */
        if(false !== ($relatedTables = self::buildRelatedTableBlocks())){
            foreach($relatedTables as $FormField){
                $this->context->getFormUI()->addField($FormField);
            }
        }

        /**
         * Build Bottom Form controls
         */
        if(false !== ($controls = self::buildBottomFormControls())){
            foreach($controls as $FormField){
                $this->context->getFormUI()->addField($FormField);
            }
        }

        return true;

    }

    /**
     * Build associated table block(s)
     *
     * @return array|bool
     * @throws Exception
     */
    private function buildAssociatedTableBlocks(){

        /** only show for EDIT or DELETE or CREATE */
        if(
            $this->context->getAction() != self::ACTION_TYPE_EDIT &&
            $this->context->getAction() != self::ACTION_TYPE_DELETE &&
            $this->context->getAction() != self::ACTION_TYPE_CREATE
        ){
            return false;
        }

        /** @var array $associatedDescTables */
        $associatedDescTables = $this->context->getAssociatedTables();

        /** @var array $associatedTables */
        $associatedTables = array();

        /** if we have relationship tables */
        if(!empty($this->relationshipTables)){

            /** @var MapTableManyManyRelationshipObject $relationshipTable */
            foreach($this->relationshipTables as $relationshipTable){

                /** skip if not white listed */
                if(!isset($associatedDescTables[$relationshipTable->getRelationshipTable()->getName()])){
                    continue;
                }

                /** see if relevant */
                if($relationshipTable->isRelevantTo($this->context->getMapTableTableObject())){

                    /** @var MapTableAssociatedTableDescriptionObject $MapTableAssociatedTableDescriptionObject */
                    $MapTableAssociatedTableDescriptionObject = $associatedDescTables[$relationshipTable->getRelationshipTable()->getName()];

                    /** relate to context */
                    $relationshipTable->modifiyForContext($this->context);

                    $FormField = new FormField();
                    $FormField->setName($relationshipTable->getRelationshipTable()->getName());
                    $FormField->setLabel($MapTableAssociatedTableDescriptionObject->getTitle());
                    $FormField->setHelper($MapTableAssociatedTableDescriptionObject->getDescription());
                    $FormField->setTemplate($MapTableAssociatedTableDescriptionObject->getFieldTemplate());
                    $FormField->setType($MapTableAssociatedTableDescriptionObject->getInputTemplate());

                    $LookupTable = $relationshipTable->getLookupTable();

                    /** columns */
                    $PrimaryColumn = $LookupTable->getPrimaryKeyColumn();
                    $NameColumn = $LookupTable->getNameColumn();

                    /** attempt to lookup related table */
                    if(!$NameColumn) {

                        /** @var MapTableColumnObject $MapTableColumnObject */
                        foreach ($LookupTable->getColumns() as $TempMapTableColumnObject) {
                            if ($TempMapTableColumnObject->getKey() == self::PRIMARY_KEY) continue;
                            if (null !== ($RelatedNameTable = $TempMapTableColumnObject->getRelatedTable())) {
                                CoreLog::debug('Did not find name column but found associated table ' . $RelatedNameTable->getName());
                                break;
                            }
                        }

                    }

                    /** @var array $records */
                    $records = $this->MapTableRepository->getAllAssociatedRecords($this->context, $relationshipTable);

                    /** @var array $options */
                    $options = array();

                    /** @var array $record */
                    foreach($records as $record){

                        /** @var FormFieldOption $option */
                        $option = new FormFieldOption();

                        /** attempt to populate */
                        if($PrimaryColumn){ $option->setKey($record[self::KEY_ID]); }
                        if($NameColumn){ $option->setValue($record[$NameColumn->getField()]); }

                        /** handle alternate/related table description lookups */
                        if(isset($RelatedNameTable) && !empty($RelatedNameTable)){
                            $NamePriColumn = $RelatedNameTable->getPrimaryKeyColumn();
                            if($NamePriColumn) {
                                $nameRecord = $this->MapTableRepository->getRecord($RelatedNameTable->getName(), $NamePriColumn->getField(), $record[$NamePriColumn->getField()]);
                                $RelatedNameColumn = $RelatedNameTable->getNameColumn();
                                if(isset($RelatedNameColumn)) {
                                    if(isset($nameRecord[$RelatedNameColumn->getField()])) {
                                        $option->setValue($nameRecord[$RelatedNameColumn->getField()]);
                                    }
                                }
                            }
                        }

                        /** set selected */
                        $option->setSelected(($record['rel_id'] > 0));

                        array_push($options, $option);

                    }

                    /** stack option */
                    $FormField->setOptions($options);

                    array_push($associatedTables, $FormField);

                }
            }
        }

        return !empty($associatedTables) ? $associatedTables : false;

    }

    /**
     * Build related table block(s)
     *
     * @return array|bool
     * @throws Exception
     */
    private function buildRelatedTableBlocks(){

        /** only show for EDIT or DELETE */
        if($this->context->getAction() != self::ACTION_TYPE_EDIT && $this->context->getAction() != self::ACTION_TYPE_DELETE){
            return false;
        }

        $relatedTableFields = array();

        /** @var MapTableTableObject $MapTableTableObject */
        $MapTableTableObject = $this->context->getMapTableTableObject();

        /** @var MapTableColumnObject $MapTableColumnObject */
        $MapTableColumnObject = $MapTableTableObject->getPrimaryKeyColumn();

        if(!empty($this->tablesContainingPrimaryKey[$MapTableColumnObject->getField()])){

            /** @var MapTableTableObject $table */
            foreach($this->tablesContainingPrimaryKey[$MapTableColumnObject->getField()] as $table){

                /** assure table has been whitelisted */
                /** @var MapTableRelatedTableDescriptionObject $relatedTableDescription */
                if(false !== ($relatedTableDescription = $this->context->getRelatedTable($table->getName()))) {

                    $FormField = new FormField();
                    $FormField->setName($table->getName());
                    $FormField->setTemplate($relatedTableDescription->getFieldTemplate());
                    $FormField->setType($relatedTableDescription->getInputTemplate());
                    $FormField->setLabel($relatedTableDescription->getTitle());
                    $FormField->setHelper($relatedTableDescription->getDescription());

                    $data = new stdClass();
                    $data->key = $MapTableColumnObject->getField();
                    $data->value = $this->context->primaryValue;
                    $FormField->setData($data);

                    array_push($relatedTableFields, $FormField);

                }

            }

            /** @var MapTableLightContextObject $MapTableLightContextObject */
            $MapTableLightContextObject = CoreLogic::getObject('MapTableLightContextObject');
            $MapTableLightContextObject->setContext($this->context);

            /** make data available */
            CoreTemplate::setData('maptablerelatedtable', 'mapTableLightContextObject', $MapTableLightContextObject);

        }

        return !empty($relatedTableFields) ? $relatedTableFields : false;

    }

    /**
     * Find mapping for column
     *
     * @param MapTableColumnObject $MapTableColumnObject
     * @return bool|MapTableMapColumnObject
     */
    public function findMapping(MapTableColumnObject $MapTableColumnObject){

        /** @var MapTableMapColumnObject $MapTableMapColumnObject */
        foreach($this->mapping as $MapTableMapColumnObject){

            /**
             * See if the data-type matches
             */
            $fails = 0;
            $typeMatch = is_array($MapTableMapColumnObject->getDataTypeMatch()) ? $MapTableMapColumnObject->getDataTypeMatch() : array($MapTableMapColumnObject->getDataTypeMatch());
            if(!empty($typeMatch[0])){
                foreach($typeMatch as $typeMatchEntry){
                    $matches = array();
                    if(!preg_match($typeMatchEntry, $MapTableColumnObject->getType(), $matches)){
                        $fails++;
                    }
                }
                if(sizeof($typeMatch) == $fails) continue;
            }

            /**
             * See if the field name matches
             * allow an array to be passed
             */
            $fails = 0;
            $matchAppend = is_array($MapTableMapColumnObject->getAppendMatch()) ? $MapTableMapColumnObject->getAppendMatch() : array($MapTableMapColumnObject->getAppendMatch());
            if(!empty($matchAppend[0])){
                foreach($matchAppend as $matchAppendEntry){
                    if(
                        substr($MapTableColumnObject->getField(), -strlen($matchAppendEntry)) != $matchAppendEntry &&
                        $matchAppendEntry != self::MATCH_ALL_CHARACTER
                    ){
                        $fails++;
                    }
                }
                if(sizeof($matchAppend) == $fails) continue;
            }

            return $MapTableMapColumnObject;

        }

        return false;

    }

    /**
     * Build the form field
     *
     * @param MapTableColumnObject $MapTableColumnObject
     * @return FormField
     */
    private function _buildFormField(MapTableColumnObject $MapTableColumnObject){

        /**
         * No need to build a form field for the primary key
         */
        if($MapTableColumnObject->getKey() === self::PRIMARY_KEY){
            return false;
        }

        /**
         * No need to build if this is a sticky field
         */
        $stickyFields = $this->context->getStickyFields();
        if(!empty($stickyFields)){
            /** @var MapTableStickyFieldObject $stickyField */
            foreach($stickyFields as $stickyField){
                if($MapTableColumnObject->getField() == $stickyField->getName()){
                    return false;
                }
            }
        }

        /**
         * New form field
         */
        $FormField = new FormField();

        /**
         * Set form field value
         * from default - or from
         * datebase row
         */
        $FormField->setValue((isset($this->context->row[$MapTableColumnObject->getField()]) ? $this->context->row[$MapTableColumnObject->getField()] : $MapTableColumnObject->getDefault()));

        /**
         * See if we can map this field/column
         */
        $foundMatch = false;

        /** @var MapTableMapColumnObject $MapTableMapColumnObject */
        foreach($this->mapping as $MapTableMapColumnObject){

            /**
             * See if the datatype matches
             */
            $fails = 0;
            $typeMatch = is_array($MapTableMapColumnObject->getDataTypeMatch()) ? $MapTableMapColumnObject->getDataTypeMatch() : array($MapTableMapColumnObject->getDataTypeMatch());
            if(!empty($typeMatch[0])){
                foreach($typeMatch as $typeMatchEntry){
                    $matches = array();
                    if(!preg_match($typeMatchEntry, $MapTableColumnObject->getType(), $matches)){
                        $fails++;
                    }
                    if(isset($matches[1])){
                        self::handleMatches($matches, $MapTableColumnObject, $FormField);
                    }
                }
                if(sizeof($typeMatch) == $fails) continue;
            }

            /**
             * See if the field name matches
             * allow an array to be passed
             */
            $fails = 0;
            $matchAppend = is_array($MapTableMapColumnObject->getAppendMatch()) ? $MapTableMapColumnObject->getAppendMatch() : array($MapTableMapColumnObject->getAppendMatch());
            if(!empty($matchAppend[0])){
                foreach($matchAppend as $matchAppendEntry){
                    if(
                        substr($MapTableColumnObject->getField(), -strlen($matchAppendEntry)) != $matchAppendEntry &&
                        $matchAppendEntry != self::MATCH_ALL_CHARACTER
                    ){
                        $fails++;
                    }
                }
                if(sizeof($matchAppend) == $fails) continue;
            }

            /**
             * Set the correct template
             */
            $FormField->setTemplate($MapTableMapColumnObject->getFieldTemplate());
            $FormField->setType($MapTableMapColumnObject->getInputTemplate());

            /**
             * Stop looking - we found a match
             */
            $foundMatch = true;
            break;

        }

        /**
         * Now that we didn't find a defined mapping
         * lets see if the column is also a foreign key in
         * another table
         */
        /** @var MapTableTableObject $RelatedTable */
        if(!$foundMatch && false !== ($RelatedTable = self::tableByPrimaryKey($MapTableColumnObject))){

            /** @var MapTableContextObject $MapTableContextObject */
            $MapTableContextObject = CoreLogic::getObject(self::MapTableContextObject);
            $MapTableContextObject->setTable($RelatedTable->getName());
            $MapTableContextObject->setMapTableTableObject($RelatedTable);
            $MapTableContextObject->setSearchQuery(null);

            /** @var array $options */
            $options = array();
            /** @var array $listingsObject */
            $listings = $this->buildListings($MapTableContextObject, true);
            /** @var MapTableListingRowObject $listing */
            foreach($listings as $listing){
                $option = new FormFieldOption();
                $option->setKey($listing->getId());
                $option->setValue($listing->getName());
                array_push($options, $option);
            }

            $FormField->setTemplate('formfieldflexible');
            $FormField->setType('formselectchosen');
            $FormField->setOptions($options);

            /**
             * We found a match
             */
            $foundMatch = true;

        } else {

            /**
             * Execute the options mapper
             * if one is defined
             */
            $optionMapper = $MapTableMapColumnObject->getOptionMapper();
            if(is_callable($optionMapper)){
                $FormField->setOptions($optionMapper($MapTableColumnObject, $this->context));
            }

            /**
             * Execute the formField modifier
             * if one is defined
             */
            $formFieldModifier = $MapTableMapColumnObject->getFormFieldModifier();
            if(is_callable($formFieldModifier)){
                $FormField = $formFieldModifier($FormField, $this->context);
            }

        }

        /**
         * Return false if no match found
         */
        if(!$foundMatch) return false;

        /**
         * Build form field
         */
        $FormField->setName($MapTableColumnObject->getField());
        $FormField->setLabel($MapTableColumnObject->getLabel());
        $FormField->setCondition($MapTableColumnObject->getValidation());
        $FormField->setPlaceholder($MapTableColumnObject->getPlaceholder());
        $FormField->setHelper($MapTableColumnObject->getHelper());
        $FormField->setExtensions($MapTableColumnObject->getExtensions());

        /**
         * Return form field
         */
        return $FormField;

    }

    /**
     * Handle matches
     *
     * @param array $matches
     * @param MapTableColumnObject $MapTableColumnObject
     * @param FormField $FormField
     * @return FormField
     */
    private function handleMatches($matches = array(), MapTableColumnObject $MapTableColumnObject, FormField $FormField){

        /**
         * Lets pick the appropriate way of handling the matched pattern in the
         * column/field type
         */
        switch(true){

            /**
             * Handle Enum
             */
            case (substr($matches[0], 0, 4) == self::TYPE_ENUM):
                $options = explode('\',\'', substr(substr($matches[1], 1), 0, -1));
                $FormField->setOptions($options);
                break;

            /**
             * Handle Length
             */
            case (is_numeric($matches[1])):
                $FormField->setLength($matches[1]);
                break;

            default:
                CoreLog::debug('Unable to understand matches ' . serialize($matches));
                break;

        }

        /**
         * Return modified field
         */
        return $FormField;

    }

    /**
     * Build top form controls
     *
     * @return array
     */
    private function _buildTopFormControls(){

        /**
         * Controls
         */
        $controls = array();

        /**
         * New form field
         */
        $FormField = new FormField();

        /**
         * Set data
         */
        $data = new stdClass();
        $data->isDelete = ($this->context->getAction() == self::ACTION_TYPE_DELETE);
        $data->isNew = ($this->context->getAction() == self::ACTION_TYPE_CREATE);
        $data->isEdit = ($this->context->getAction() == self::ACTION_TYPE_EDIT);

        /**
         * Populate form field
         */
        $FormField->setName($this->context->getTable() . '_submit');
        if($data->isNew){
            $FormField->setPlaceholder(CoreLanguage::get('maptable.buttons.text.insert'));
        }else{
            $FormField->setPlaceholder(CoreLanguage::get('maptable.buttons.text.save'));
        }
        $FormField->setData($data);
        $FormField->setType('formbuttonslarge');
        $FormField->setTemplate('formfieldnaked');

        /**
         * Stack the form field
         */
        array_push($controls, $FormField);

        /**
         * Return the controls
         */
        return $controls;

    }

    /**
     * Build bottom form controls
     *
     * @return array
     */
    private function _buildBottomFormControls(){

        /**
         * Controls
         */
        $controls = array();

        /**
         * Set data
         */
        $data = new stdClass();
        $data->isDelete = ($this->context->getAction() == self::ACTION_TYPE_DELETE);
        $data->isNew = ($this->context->getAction() == self::ACTION_TYPE_CREATE);
        $data->isEdit = ($this->context->getAction() == self::ACTION_TYPE_EDIT);

        /**
         * New form field
         */
        $FormField = new FormField();
        $FormField->setName($this->context->getTable() . '_submit');
        if($data->isNew){
            $FormField->setPlaceholder(CoreLanguage::get('maptable.buttons.text.insert'));
        }else{
            $FormField->setPlaceholder(CoreLanguage::get('maptable.buttons.text.save'));
        }
        $FormField->setData($data);
        $FormField->setType('formbuttonslarge');
        $FormField->setTemplate('formfieldnaked');

        /**
         * Stack the form field
         */
        array_push($controls, $FormField);

        /**
         * Return the controls
         */
        return $controls;

    }

    /**
     * Get all records
     */
    public function getAllRecords(){

        /**
         * Return all records
         */
        return $this->MapTableRepository->getAllRecords($this->context->getTable());

    }

    /**
     * Capture submission
     */
    public function _captureSubmission(){

        /**
         * If form is submitted
         */
        if($this->context->getFormUI()->validFormSubmitted()){

            /**
             * Handle Create action
             */
            if($this->context->getAction() == self::ACTION_TYPE_CREATE || $this->context->getAction() == self::ACTION_TYPE_EDIT){
                if($this->context->getFormUI()->validateSubmission()){
    
                    /** @var array $values */
                    $values = $this->context->getFormUI()->getFormValues();
    
                    /** @var MapTableTableObject $MapTableTableObject */
                    $MapTableTableObject = $this->context->getMapTableTableObject();
    
                    /** @var MapTableColumnObject $MapTableColumnObject */
                    foreach($MapTableTableObject->getColumns() as $MapTableColumnObject){
                        $MapTableColumnObject->setSubmittedValue((isset($values[$MapTableColumnObject->getField()]) ? $values[$MapTableColumnObject->getField()] : null));
                    }
    
                    try {
    
                        /** Start a transaction */
                        CoreSqlUtils::beginTransaction();
    
                        /** persist */
                        self::persist();
    
                        /** Commit transaction */
                        CoreSqlUtils::commitTransaction();
    
                    } catch(Exception $e){
    
                        /** Roll back database changes */
                        CoreSqlUtils::rollbackTransaction();
    
                        /** Notify */
                        CoreNotification::set('Unable to save due to error. Info: ' . $e->getMessage(), CoreNotification::ERROR);
    
                    }
    
                }else{
                    CoreNotification::set('Unable to validate submission', CoreNotification::ERROR);
                }
            }

            /**
             * Handle delete
             */
            if($this->context->getAction() == self::ACTION_TYPE_DELETE){

                try {

                    /** Start a transaction */
                    CoreSqlUtils::beginTransaction();

                    /** delete */
                    self::delete();

                    /** Commit transaction */
                    CoreSqlUtils::commitTransaction();

                } catch(Exception $e){

                    /** Roll back database changes */
                    CoreSqlUtils::rollbackTransaction();

                    /** Notify */
                    CoreNotification::set('Unable to save due to error. Info: ' . $e->getMessage(), CoreNotification::ERROR);

                }

            }
            
        }

    }

    /**
     * Set values of sticky fields
     */
    private function applyStickyFields(){

        /** @var array $stickyFields */
        $stickyFields = $this->context->getStickyFields();

        /** @var MapTableTableObject $mapTableTableObject */
        $mapTableTableObject = $this->context->getMapTableTableObject();

        /** set sticky values if applicable */
        if(!empty($stickyFields)){

            /** @var MapTableColumnObject $column */
            foreach($mapTableTableObject->getColumns() as $column) {

                /** @var MapTableStickyFieldObject $stickyField */
                foreach ($stickyFields as $stickyField) {

                    if($column->getField() == $stickyField->getName()){
                        $column->setSubmittedValue($stickyField->getValue());
                    }

                }

            }
        }

    }

    /**
     * Persist
     */
    private function persist(){

        /** apply sticky fields */
        self::applyStickyFields();

        /** @var MapTableTableObject $MapTableTableObject */
        $MapTableTableObject = $this->context->getMapTableTableObject();

        /** @var string $primaryValue */
        $primaryValue = $this->context->getPrimaryValue();

        /** @var MapTableColumnObject $PrimaryKeyColumn */
        $PrimaryKeyColumn = $this->context->getPrimaryKeyColumn();

        /** Update or insert */
        if(!empty($primaryValue) && $this->MapTableRepository->recordExists($this->context->getTable(), $PrimaryKeyColumn->getField(), $primaryValue)){

            /** Update */
            $this->MapTableRepository->update($this->context);

            /** Notify */
            CoreNotification::set('Record Updated', CoreNotification::SUCCESS);

            /** Dispatch listeners */
            CoreObserver::dispatch(MapTableModule::EVENT_UPDATED, $this->context);

            /** Redirect the browser */
            CoreHeaders::setRedirect(CoreArrayUtils::getString(array(self::QUERY_PRIMARY_VALUE => $primaryValue, self::QUERY_ACTION => self::ACTION_TYPE_EDIT), array()));

        }else{

            /** Insert */
            $insertID = $this->MapTableRepository->insert($this->context);

            /** Notify */
            CoreNotification::set('Record Inserted', CoreNotification::SUCCESS);

            /** Dispatch listeners */
            CoreObserver::dispatch(MapTableModule::EVENT_INSERTED, $this->context);

            /** Redirect the browser */
            CoreHeaders::setRedirect(CoreArrayUtils::getString(array(self::QUERY_PRIMARY_VALUE => $insertID), array(self::QUERY_ACTION => null)));

        }

        /** handle associated tables */
        $associatedTables = $this->context->getAssociatedTables();
        if(!empty($associatedTables)){

            /** @var array $associatedIds */
            $associatedIds = array();

            /** @var array $params */
            $params = array();

            /** based on form type */
            if(strtolower($this->context->getFormUI()->getMethod()) == self::ATTR_POST){
                $params = $_POST;
            }else{
                $params = $_GET;
            }

            /**
             * @var pattern that contains associated table $key
             * @var $value
             */
            foreach($params as $key => $value){
                if(substr($key, 0, strlen(self::ASSOCIATED_TABLE_NAME_PREPEND)) == self::ASSOCIATED_TABLE_NAME_PREPEND) {
                    $matches = array();
                    if (preg_match(self::ASSOCIATED_TABLE_REGEX, $key, $matches) > 0) {
                        if (array_key_exists($matches[1], $associatedTables)) {
                            $associatedIds[$matches[1]][] = $matches[2];
                        }
                    }
                }
            }

            /**
             * Set empty list in case all
             * relationship items are unselected
             */
            $keys = array_keys($associatedTables);
            foreach($keys as $key){
                if(!isset($associatedIds[$key])){
                    $associatedIds[$key] = array();
                }
            }

            /**
             * If we have associated ids
             */
            if(!empty($associatedIds)){
                foreach($associatedIds as $table => $lookupIDS){
                    if (array_key_exists($table, $associatedTables)) {

                        /** @var MapTableAssociatedTableDescriptionObject $MapTableAssociatedTableDescriptionObject */
                        $MapTableAssociatedTableDescriptionObject = $associatedTables[$table];

                        if(array_key_exists($table, $this->relationshipTables)){

                            /** @var MapTableManyManyRelationshipObject $MapTableManyManyRelationshipObject */
                            $MapTableManyManyRelationshipObject = $this->relationshipTables[$table];

                            /** store the relationships */
                            $this->MapTableRepository->storeAssociations($lookupIDS, $MapTableManyManyRelationshipObject, $this->context);

                        }

                    }
                }
            }

        }

    }

    /**
     * Delete
     */
    public function delete(){

        /** Do the delete */
        $this->MapTableRepository->delete($this->context);

        /** Dispatch listeners */
        CoreObserver::dispatch(MapTableModule::EVENT_DELETED, $this->context);

        /** Redirect */
        CoreHeaders::setRedirect(CoreArrayUtils::getString(array(), array(self::QUERY_PRIMARY_VALUE => $this->context->getPrimaryValue(), self::QUERY_ACTION => self::KEY_DELETE)));

    }

    /**
     * Populate related tables
     *
     */
    private function populateRelatedTables(){

        /** @var MapTableTableObject $MapTableTableObject */
        $MapTableTableObject = $this->context->getMapTableTableObject();

        /** @var MapTableColumnObject $MapTableColumnObject */
        foreach($MapTableTableObject->getColumns() as $MapTableColumnObject){
            if($MapTableColumnObject->getKey() == self::PRIMARY_KEY) continue;
            if(false !== ($RelatedTable = self::tableByPrimaryKey($MapTableColumnObject))){
                $MapTableColumnObject->setRelatedTable($RelatedTable);
            }
        }

    }

    /**
     * Build the listings, also allow lookups of name fields on related tables if
     * all we have are references to primary keys
     *
     * @param MapTableContextObject $mapTableContextObject
     * @param bool $skipPagination
     * @return array
     */
    public function buildListings(MapTableContextObject $mapTableContextObject, $skipPagination = false){

        /** @var MapTableListingsObject $MapTableListingsObject */
        $MapTableListingsObject = $this->MapTableRepository->getListing($mapTableContextObject, ($skipPagination === true));

        /**
         * Set pagination reference in maptable context object
         */
        $this->context->setCorePaginationObject($MapTableListingsObject->getCorePaginationObject());

        /** @var MapTableTableObject $MapTableTableObject */
        $MapTableTableObject = $mapTableContextObject->getMapTableTableObject();

        /** @var array $listings */
        $listings = array();

        /** @var MapTableColumnObject $PrimaryColumn */
        $PrimaryColumn = $MapTableTableObject->getPrimaryKeyColumn();

        /** @var MapTableColumnObject $NameColumn */
        $NameColumn = $MapTableTableObject->getNameColumn();

        /** @var MapTableColumnObject $DescriptionColumn */
        $DescriptionColumn = $MapTableTableObject->getDescriptionColumn();

        /** @var MapTableColumnObject $ExtraColumn */
        $ExtraColumn = $MapTableTableObject->getExtraColumn();

        /** attempt to lookup related table */
        if(!$NameColumn){
            /** @var MapTableColumnObject $MapTableColumnObject */
            foreach($MapTableTableObject->getColumns() as $MapTableColumnObject){
                if($MapTableColumnObject->getKey() == self::PRIMARY_KEY) continue;
                if(null !== ($RelatedNameTable = $MapTableColumnObject->getRelatedTable())){
                    CoreLog::debug('Did not find name column but found associated table ' . $RelatedNameTable->getName());
                    break;
                }
            }
        }

        /** attempt to lookup alt table */
        if(!$DescriptionColumn){
            /** @var MapTableColumnObject $MapTableColumnObject */
            foreach($MapTableTableObject->getColumns() as $MapTableColumnObject){
                if(isset($RelatedNameTable) && !empty($RelatedNameTable)){
                    if($RelatedNameTable->getPrimaryKeyColumn()->getField() == $MapTableColumnObject->getField()){
                        continue;
                    }
                }
                if($MapTableColumnObject->getKey() == self::PRIMARY_KEY) continue;
                if(null !== ($RelatedDescriptionTable = $MapTableColumnObject->getRelatedTable())){
                    CoreLog::debug('Did not find description column but found associated table ' . $RelatedDescriptionTable->getName());
                    break;
                }
            }
        }

        /** attempt to lookup alt table */
        if(!$ExtraColumn){
            /** @var MapTableColumnObject $MapTableColumnObject */
            foreach($MapTableTableObject->getColumns() as $MapTableColumnObject){
                if(isset($RelatedNameTable) && !empty($RelatedNameTable)){
                    if($RelatedNameTable->getPrimaryKeyColumn()->getField() == $MapTableColumnObject->getField()) continue;
                }
                if(isset($RelatedDescriptionTable) && !empty($RelatedDescriptionTable)){
                    if($RelatedDescriptionTable->getPrimaryKeyColumn()->getField() == $MapTableColumnObject->getField()) continue;
                }
                if($MapTableColumnObject->getKey() == self::PRIMARY_KEY) continue;
                if(null !== ($RelatedExtraTable = $MapTableColumnObject->getRelatedTable())){
                    CoreLog::debug('Did not find extra column but found associated table ' . $RelatedExtraTable->getName());
                    break;
                }
            }
        }

        /** @var MapTableColumnObject $DateAddedColumn */
        $DateAddedColumn = $MapTableTableObject->getDateAddedColumn();

        /**
         * Step through listings
         */
        foreach($MapTableListingsObject->getListings() as $record){

            /** @var MapTableListingRowObject $MapTableListingRowObject */
            $MapTableListingRowObject = CoreLogic::getObject('MapTableListingRowObject');
            $MapTableListingRowObject->setId($record[$PrimaryColumn->getField()]);
            if($NameColumn) $MapTableListingRowObject->setName($record[$NameColumn->getField()]);
            if($DescriptionColumn) $MapTableListingRowObject->setDescription($record[$DescriptionColumn->getField()]);
            if($ExtraColumn) $MapTableListingRowObject->setExtra($record[$ExtraColumn->getField()]);
            if($DateAddedColumn) $MapTableListingRowObject->setDateAdded($record[$DateAddedColumn->getField()]);

            /** handle alternate/related table description lookups */
            if(isset($RelatedNameTable) && !empty($RelatedNameTable)){
                $NamePriColumn = $RelatedNameTable->getPrimaryKeyColumn();
                if($NamePriColumn) {
                    $nameRecord = $this->MapTableRepository->getRecord($RelatedNameTable->getName(), $NamePriColumn->getField(), $record[$NamePriColumn->getField()]);
                    $RelatedNameColumn = $RelatedNameTable->getNameColumn();
                    if(isset($RelatedNameColumn)) {
                        if(isset($nameRecord[$RelatedNameColumn->getField()])) {
                            $MapTableListingRowObject->setName($nameRecord[$RelatedNameColumn->getField()]);
                        }
                    }
                }
            }
            if(isset($RelatedDescriptionTable) && !empty($RelatedDescriptionTable)){
                $DescriptionPriColumn = $RelatedDescriptionTable->getPrimaryKeyColumn();
                if($DescriptionPriColumn) {
                    $descriptionRecord = $this->MapTableRepository->getRecord($RelatedDescriptionTable->getName(), $DescriptionPriColumn->getField(), $record[$DescriptionPriColumn->getField()]);
                    $RelatedDescriptionColumn = $RelatedDescriptionTable->getNameColumn();
                    if(isset($RelatedDescriptionColumn)) {
                        if(isset($descriptionRecord[$RelatedDescriptionColumn->getField()])) {
                            $MapTableListingRowObject->setDescription($descriptionRecord[$RelatedDescriptionColumn->getField()]);
                        }
                    }
                }
            }
            if(isset($RelatedExtraTable) && !empty($RelatedExtraTable)){
                $ExtraPriColumn = $RelatedExtraTable->getPrimaryKeyColumn();
                if($ExtraPriColumn) {
                    $extraRecord = $this->MapTableRepository->getRecord($RelatedExtraTable->getName(), $ExtraPriColumn->getField(), $record[$ExtraPriColumn->getField()]);
                    $RelatedExtraColumn = $RelatedExtraTable->getNameColumn();
                    if($RelatedExtraColumn) {
                        if(isset($extraRecord[$RelatedExtraColumn->getField()])) {
                            $MapTableListingRowObject->setExtra($extraRecord[$RelatedExtraColumn->getField()]);
                        }
                    }
                }
            }

            /** Stack the listing */
            array_push($listings, $MapTableListingRowObject);

        }

        return $listings;

    }

    /**
     * Populate context
     *
     * @param MapTableContextObject $MapTableContextObject
     * @return MapTableContextObject
     */
    public function populateContext(MapTableContextObject $MapTableContextObject){

        /** set context */
        $this->setContext($MapTableContextObject);

        /** Map tables */
        $this->mapTables();

        /** Populate related tables */
        $this->populateRelatedTables();

        /** return context */
        return $this->context;

    }

    /**
     * Modify action from maptable context
     *
     * @param MapTableContextObject $MapTableContextObject
     * @return MapTableActionModifierObject
     */
    public function _fromContext(MapTableContextObject $MapTableContextObject){

        /** build the context */
        self::populateContext($MapTableContextObject);

        /**
         * Make template available to template
         */
        CoreTemplate::setData($MapTableContextObject->getTemplate(), self::COLUMN_TABLE_LC, $MapTableContextObject->getTable());
        CoreTemplate::setData($MapTableContextObject->getTemplate(), self::MapTableContextObject, $MapTableContextObject);

        /** @var MapTableActionModifierObject $MapTableActionModifierObject */
        $MapTableActionModifierObject = CoreLogic::getObject(self::MapTableActionModifierObject);

        /**
         * Handle action
         */
        switch($MapTableContextObject->getAction()){

            case self::ACTION_TYPE_CREATE:
                $this->buildForm();
                $this->captureSubmission();
                $MapTableActionModifierObject->setTemplate($MapTableContextObject->getTemplate());
               break;

            case self::ACTION_TYPE_DELETE:
                $this->buildForm();
                $this->captureSubmission();
                $MapTableActionModifierObject->setTemplate($MapTableContextObject->getTemplate());
                break;

            case self::ACTION_TYPE_EDIT:
                $this->buildForm();
                $this->captureSubmission();
                $MapTableActionModifierObject->setTemplate($MapTableContextObject->getTemplate());
                break;

            default:
                $MapTableActionModifierObject->setTemplate($MapTableContextObject->getListingTemplate());
                break;

        }

        /**
         * Set listings
         */
        CoreTemplate::setData($MapTableContextObject->getListingTemplate(), self::KEY_LISTINGS, $this->buildListings($this->context));
        CoreTemplate::setData($MapTableContextObject->getListingTemplate(), self::MapTableContextObject, $this->context);
        CoreTemplate::setData($MapTableContextObject->getListingHeaderTemplate(), self::MapTableContextObject, $this->context);

        /**
         * Set pagination details
         */
        CoreTemplate::setData($MapTableContextObject->getListingPaginationTop(), self::KEY_PAGINATION, $this->context->getCorePaginationObject());
        CoreTemplate::setData($MapTableContextObject->getListingPaginationBottom(), self::KEY_PAGINATION, $this->context->getCorePaginationObject());

        /**
         * Return action modifier
         */
        return $MapTableActionModifierObject;

    }

    /**
     * @return MapTableContextObject
     */
    public function getContext()
    {
        return $this->context;
    }

}