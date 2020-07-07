<?php

/**
 * Map Table Repository
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MapTableRepository {

    /**
     * SQL Components
     */
    const SQL_COMPONENT_TABLE = '{{table}}';
    const SQL_COMPONENT_PRIMARY_KEY = '{{primaryKey}}';
    const SQL_COMPONENT_PRIMARY_VALUE = '{{primaryValue}}';

    /**
     * Sql Statements
     */
    const SQL_ALL_TABLES = 'SHOW TABLE STATUS';
    const SQL_ALL_COLUMNS = 'SHOW FULL COLUMNS FROM {{table}}';
    const SQL_ALL_INDEXES = 'SHOW INDEXES FROM {{table}}';
    const SQL_ALL_RECORDS = 'SELECT * FROM {{table}}';
    const SQL_GET_RECORD = 'SELECT * FROM {{table}} WHERE {{primaryKey}} = \'{{primaryValue}}\'';

    /**
     * Get all tables
     *
     * @return Array
     */
    public function getAllTables(){
        return CoreSqlUtils::rows(self::SQL_ALL_TABLES);
    }

    /**
     * Get all columns for a table
     *
     * @param null $table
     * @return Array
     */
    public function getAllColumns($table = null){
        return CoreSqlUtils::rows(str_replace(self::SQL_COMPONENT_TABLE, $table, self::SQL_ALL_COLUMNS));
    }

    /**
     * Get all indexes for a table
     *
     * @param null $table
     * @return Array
     */
    public function getAllIndexes($table = null){
        return CoreSqlUtils::rows(str_replace(self::SQL_COMPONENT_TABLE, $table, self::SQL_ALL_INDEXES));
    }

    /**
     * Get all records from table
     *
     * @param null $table
     * @return Array
     */
    public function getAllRecords($table = null){
        return CoreSqlUtils::rows(str_replace(self::SQL_COMPONENT_TABLE, $table, self::SQL_ALL_RECORDS));
    }

    /**
     * Get all associated records
     *
     * @param MapTableContextObject $mapTableContextObject
     * @param MapTableManyManyRelationshipObject $manyManyRelationshipObject
     * @return Array
     */
    public function getAllAssociatedRecords(MapTableContextObject $mapTableContextObject, MapTableManyManyRelationshipObject $manyManyRelationshipObject){

        /** @var MapTableColumnObject $constraintColumn */
        $constraintColumn = $manyManyRelationshipObject->getConstraintColumn();
        $constraintValue = null;
        if(isset($constraintColumn->field)) {
            $row = $mapTableContextObject->getRow();
            if(isset($row[$constraintColumn->getField()])){
                $constraintValue = $row[$constraintColumn->getField()];
            }
        }

        $sql = '
            SELECT
                ' . $manyManyRelationshipObject->getLookupTable()->getName() . '.*,
                ' . $manyManyRelationshipObject->getLookupTable()->getName() . '.' . $manyManyRelationshipObject->getLookupTable()->getPrimaryKeyColumn()->getField() . ' AS id,
                ' . $manyManyRelationshipObject->getRelationshipTable()->getName() . '.' . $manyManyRelationshipObject->getLookupTable()->getPrimaryKeyColumn()->getField() . ' AS rel_id
            FROM
                ' . $manyManyRelationshipObject->getLookupTable()->getName() . '
            LEFT JOIN
                ' . $manyManyRelationshipObject->getRelationshipTable()->getName() . '
            ON
                (
                    ' . $manyManyRelationshipObject->getRelationshipTable()->getName() . '.' . $manyManyRelationshipObject->getLookupTable()->getPrimaryKeyColumn()->getField() . ' = ' . $manyManyRelationshipObject->getLookupTable()->getName() . '.' . $manyManyRelationshipObject->getLookupTable()->getPrimaryKeyColumn()->getField() . '
                AND
                    ' . $manyManyRelationshipObject->getRelationshipTable()->getName() . '.' . $mapTableContextObject->getMapTableTableObject()->getPrimaryKeyColumn()->getField() . ' =
                    (
                        SELECT ' . $mapTableContextObject->getMapTableTableObject()->getPrimaryKeyColumn()->getField() . '
                        FROM ' . $mapTableContextObject->getMapTableTableObject()->getName() . '
                        WHERE ' . $mapTableContextObject->getMapTableTableObject()->getPrimaryKeyColumn()->getField() . ' = ' . CoreSqlUtils::quote($mapTableContextObject->getPrimaryValue()) . '
                         ' . self::buildStickyString($mapTableContextObject, false) . '
                    )
                )
            ' . (isset($constraintColumn->field) ? '
            WHERE
                ' . $manyManyRelationshipObject->getLookupTable()->getName() . '.' . $constraintColumn->getField() . ' = ' . CoreSqlUtils::quote($constraintValue) . '
            ' : '') . '
            GROUP BY
                ' . $manyManyRelationshipObject->getLookupTable()->getName() . '.' . $manyManyRelationshipObject->getLookupTable()->getPrimaryKeyColumn()->getField() . '
        ';

        return CoreSqlUtils::rows($sql);

    }

    /**
     * Build sticky string
     *
     * @param MapTableContextObject $mapTableContextObject
     * @return string
     */
    private function buildStickyString(MapTableContextObject $mapTableContextObject, $addWhere = false){

        /** @var string $stickySql */
        $stickySql = '';
        $stickyFields = $mapTableContextObject->getStickyFields();
        if(!empty($stickyFields)){
            if($addWhere) $stickySql = ' WHERE 1 = 1 ';
            /** @var MapTableStickyFieldObject $stickyField */
            foreach($stickyFields as $stickyField){
                $stickySql .= ' AND ' . $mapTableContextObject->getTable() . '.' . $stickyField->getName() . ' = ' . CoreSqlUtils::quote($stickyField->getValue()) . ' ';
            }
        }

        return $stickySql;

    }

    /**
     * Get a record
     *
     * @param MapTableContextObject $mapTableContextObject
     * @return Array
     */
    public function getRecordFromContext(MapTableContextObject $mapTableContextObject){

        /** @var MapTableTableObject $mapTableTableObject */
        $mapTableTableObject = $mapTableContextObject->getMapTableTableObject();
        if(empty($mapTableTableObject)) CoreLog::error('No table object!');

        /** @var MapTableColumnObject $mapTableColumnObject */
        $mapTableColumnObject = $mapTableTableObject->getPrimaryKeyColumn();
        if(empty($mapTableColumnObject)) CoreLog::error('No primary key column!');

        /** @var string $stickySql */
        $stickySql = self::buildStickyString($mapTableContextObject);

        return CoreSqlUtils::row(str_replace(
            array(self::SQL_COMPONENT_TABLE, self::SQL_COMPONENT_PRIMARY_KEY, self::SQL_COMPONENT_PRIMARY_VALUE),
            array($mapTableTableObject->getName(), $mapTableColumnObject->getField(), $mapTableContextObject->getPrimaryValue()),
            self::SQL_GET_RECORD . $stickySql
        ));

    }

    /**
     * Store associations
     * honor sticky fields to prevent injection of unauthorized entries
     *
     * @param array $ids
     * @param MapTableManyManyRelationshipObject $MapTableManyManyRelationshipObject
     * @param MapTableContextObject $MapTableContextObject
     * @return bool
     */
    public function storeAssociations(array $ids, MapTableManyManyRelationshipObject $MapTableManyManyRelationshipObject, MapTableContextObject $MapTableContextObject){

        if($MapTableManyManyRelationshipObject){

            /** @var MapTableTableObject $RelationShipTable */
            $RelationShipTable = $MapTableManyManyRelationshipObject->getRelationshipTable();

            /** @var MapTableColumnObject $PrimaryKeyReference */
            $PrimaryKeyReference = $MapTableContextObject->getMapTableTableObject()->getPrimaryKeyColumn();

            /** @var MapTableTableObject $LookupReference */
            $LookupReference = $MapTableManyManyRelationshipObject->getLookupTable();

            /** @var MapTableColumnObject $constraintColumn */
            $constraintColumn = $MapTableManyManyRelationshipObject->getConstraintColumn();
            $constraintValue = null;
            if(isset($constraintColumn->field)) {
                /** @var MapTableTableObject $MapTableTableObject */
                $MapTableTableObject = $MapTableContextObject->getMapTableTableObject();
                /** @var MapTableColumnObject $MapTableColumnObject */
                foreach ($MapTableTableObject->getColumns() as $MapTableColumnObject) {
                    if ($MapTableColumnObject->getField() == $constraintColumn->getField()) {
                        $constraintValue = $MapTableColumnObject->getSubmittedValue();
                    }
                }
            }

            /** @var  $sql */
            $sql = '
                DELETE FROM
                    ' . $RelationShipTable->getName() . '
                WHERE
                    ' . $PrimaryKeyReference->getField() . ' =
                    (
                        SELECT ' . $PrimaryKeyReference->getField() . '
                        FROM ' . $MapTableContextObject->getMapTableTableObject()->getName() . '
                        WHERE ' . $PrimaryKeyReference->getField() . ' = ' . CoreSqlUtils::quote($MapTableContextObject->getPrimaryValue()) . '
                        ' . self::buildStickyString($MapTableContextObject, false) . '
                    );';

            /** flush existing rows */
            CoreSqlUtils::delete($sql);

            /** insert fresh relationships */
            if(!empty($ids)){

                /** @var string $sql build the insert block */
                $sql = 'INSERT INTO ' . $RelationShipTable->getName() . ' (
                    ' . $PrimaryKeyReference->getField() . ',
                    ' . $LookupReference->getPrimaryKeyColumn()->getField() . '
                    ' . (isset($constraintColumn->field) ? ', ' . $constraintColumn->getField() : '') . '
                ) VALUES ';

                /** @var array $insertBlocks */
                $insertBlocks = array();
                foreach($ids as $id){
                    array_push($insertBlocks, '
                    (
                        (
                            SELECT ' . $PrimaryKeyReference->getField() . '
                            FROM ' . $MapTableContextObject->getMapTableTableObject()->getName() . '
                            WHERE ' . $PrimaryKeyReference->getField() . ' = ' . CoreSqlUtils::quote($MapTableContextObject->getPrimaryValue()) . '
                            ' . self::buildStickyString($MapTableContextObject, false) . '
                        ),
                        ' . CoreSqlUtils::quote($id) . '
                        ' . (isset($constraintColumn->field) ? ', ' . CoreSqlUtils::quote($constraintValue) : '') . '
                    )');
                }

                /** add in , */
                $sql .= implode(',', $insertBlocks);

                /** insert relationship rows */
                CoreSqlUtils::query($sql);

            }
        }

        return true;

    }

    /**
     * Get a record
     *
     * @param null $table
     * @param null $primaryKey
     * @param null $primaryValue
     * @return Array
     */
    public function getRecord($table = null, $primaryKey = null, $primaryValue = null){
        return CoreSqlUtils::row(str_replace(
            array(self::SQL_COMPONENT_TABLE, self::SQL_COMPONENT_PRIMARY_KEY, self::SQL_COMPONENT_PRIMARY_VALUE),
            array($table, $primaryKey, $primaryValue),
            self::SQL_GET_RECORD
        ));
    }

    /**
     * Does record exist
     *
     * @param null $table
     * @param null $primaryKey
     * @param null $primaryValue
     * @return bool
     */
    public function recordExists($table = null, $primaryKey = null, $primaryValue = null) {
        $row = self::getRecord($table, $primaryKey, $primaryValue);
        return !empty($row);
    }

    /**
     * Check to see if autogenerated
     *
     * @param MapTableColumnObject $mapTableColumnObject
     * @return bool
     */
    private function isAutoGeneratedColumn(MapTableColumnObject $mapTableColumnObject){

        // if this a primary key
        if($mapTableColumnObject->getKey() == 'PRI') return true;

        // if a timestamp with default CURRENT_TIMESTAMP
        if($mapTableColumnObject->getDefault() == 'CURRENT_TIMESTAMP' && $mapTableColumnObject->getType() == 'timestamp') return true;

        return false;

    }

    /**
     * Check if only when record created
     *
     * @param MapTableColumnObject $mapTableColumnObject
     * @return bool
     */
    private function isCreateOnlyGeneratedColumn(MapTableColumnObject $mapTableColumnObject) {

        // if this is a date_added column
        if(
            substr($mapTableColumnObject->getType(), 0, 8) == 'datetime' &&
            substr($mapTableColumnObject->getField(), -11) == '_date_added'
        ) return true;

        return false;

    }

    /**
     * Update record in database
     *
     * @param MapTableContextObject $MapTableContextObject
     * @return bool
     * @throws Exception
     */
    public function update(MapTableContextObject $MapTableContextObject){

        $this->validateIndexConstraints($MapTableContextObject);

        // context table
        $MapTableTableObject = $MapTableContextObject->getMapTableTableObject();

        // update
        $sql = 'UPDATE ' . $MapTableTableObject->getName() . ' SET ';

        /** @var MapTableColumnObject $MapTableColumnObject */
        foreach($MapTableTableObject->getColumns() as $MapTableColumnObject){

            // skip autogenerated columns
            if(self::isAutoGeneratedColumn($MapTableColumnObject)) continue;

            // skip create only column
            if(self::isCreateOnlyGeneratedColumn($MapTableColumnObject)) continue;

            if($MapTableContextObject->getMapTableTableObject()->getDateAddedColumn())

            $sql_value = $MapTableColumnObject->getSubmittedValue();
            if (
                false !== stripos($MapTableColumnObject->getType(), 'tinyint') ||
                false !== stripos($MapTableColumnObject->getType(), 'bigint') ||
                false !== stripos($MapTableColumnObject->getType(), 'int')
            ) {
                $sql_value = (int) $MapTableColumnObject->getSubmittedValue();
            }
            $sql .= $MapTableColumnObject->getField() . '=' . CoreSqlUtils::quote($sql_value) . ',';

        }

        // strip last comma
        $sql = rtrim($sql, ",");

        // add where clause
        $sql .= ' WHERE ' . $MapTableTableObject->getPrimaryKeyColumn()->getField() . ' = ' . CoreSqlUtils::quote($MapTableContextObject->getPrimaryValue());

        // perform update
        return CoreSqlUtils::update($sql);

    }

    /**
     * Validate index constraints
     *
     * @param MapTableContextObject $MapTableContextObject
     * @throws Exception
     */
    private function validateIndexConstraints(MapTableContextObject $MapTableContextObject){

        $MapTableTableObject = $MapTableContextObject->getMapTableTableObject();

        $indexes = $MapTableContextObject->getMapTableTableObject()->getIndexes();
        if(!empty($indexes)){

            /** @var array $uniqueIndexes */
            $uniqueIndexes = array();

            /** @var MapTableIndexObject $index */
            foreach($indexes as $index){

                /**
                 * Throw exception if a unique index is defined and violated
                 */
                if($index->getKeyName() != 'PRIMARY' && !CoreStringUtils::evaluateBoolean($index->getNonUnique())){

                    if(!isset($uniqueIndexes[$index->getKeyName()])){
                        $uniqueIndexes[$index->getKeyName()] = array();
                    }
                    array_push($uniqueIndexes[$index->getKeyName()], $index->getColumnName());

                }

            }

            /**
             * Check for unique index constraint violations
             */
            if(!empty($uniqueIndexes)){
                foreach($uniqueIndexes as $uniqueIndex){
                    $uniqueFields = $index->getColumnName();
                    $sanitySql = 'SELECT COUNT(*) AS existing FROM ' . $MapTableContextObject->getTable() . ' WHERE ';
                    $uniqueFields = is_array($uniqueFields) ? $uniqueFields : array($uniqueFields);
                    foreach($uniqueIndex as $uniqueField){
                        /** @var MapTableColumnObject $MapTableColumnObject */
                        foreach($MapTableTableObject->getColumns() as $MapTableColumnObject) {
                            if($MapTableColumnObject->getField() == $uniqueField) {
                                $sanitySql .= ' ' . $uniqueField . ' = ' . CoreSqlUtils::quote($MapTableColumnObject->getSubmittedValue()) . ' AND ';

                            }
                        }
                    }

                    $primaryValue = $MapTableContextObject->getPrimaryValue();
                    if(!empty($primaryValue)){
                        $sanitySql .= $MapTableTableObject->getPrimaryKeyColumn()->getField() . ' != ' . CoreSqlUtils::quote($primaryValue);
                    }else{
                        $sanitySql .= ' 1 = 1 ';
                    }
                    $test = CoreSqlUtils::row($sanitySql);
                    if(isset($test['existing']) && $test['existing'] > 0){

                        /**
                         * Throw exception to prevent an unique index constraint error
                         * when attempting to write to the database
                         */
                        throw new MapTableNonUniqueIndexViolationException(CoreLanguage::get('maptable.duplicate.record.index.violation.message') . ' Collision on ' . implode(' and ', $uniqueIndex));

                    }

                }
            }

        }

    }

    /**
     * Insert a record
     *
     * @param MapTableContextObject $MapTableContextObject
     * @return int
     * @throws Exception
     */
    public function insert(MapTableContextObject $MapTableContextObject){

        $this->validateIndexConstraints($MapTableContextObject);

        $MapTableTableObject = $MapTableContextObject->getMapTableTableObject();

        $sql = 'INSERT INTO ' . $MapTableTableObject->getName() . ' (';

        $DateAddedColumn = $MapTableTableObject->getDateAddedColumn();

        /** @var MapTableColumnObject $MapTableColumnObject */
        foreach($MapTableTableObject->getColumns() as $MapTableColumnObject){

            // skip autogenerated columns
            if(self::isAutoGeneratedColumn($MapTableColumnObject)) continue;

            // build the column block
            $sql .= $MapTableColumnObject->getField() . ',';

        }

        $sql = rtrim($sql, ",");

        $sql .= ') VALUES (';

        /** @var MapTableColumnObject $MapTableColumnObject */
        foreach($MapTableTableObject->getColumns() as $MapTableColumnObject){

            // skip autogenerated columns
            if(self::isAutoGeneratedColumn($MapTableColumnObject)) continue;

            // handle date added
            if($DateAddedColumn){
                if($DateAddedColumn->getField() == $MapTableColumnObject->getField()){
                    $sql .= 'NOW(),';
                    continue;
                }
            }

            // build the value block
            $sql .= CoreSqlUtils::quote($MapTableColumnObject->getSubmittedValue()) . ',';

        }

        $sql = rtrim($sql, ",");

        $sql .= ')';

        return CoreSqlUtils::insert($sql);

    }

    /**
     * Delete the row
     *
     * @param MapTableContextObject $MapTableContextObject
     * @return bool
     */
    public function delete(MapTableContextObject $MapTableContextObject){

        /** @var MapTableTableObject $MapTableTableObject */
        $MapTableTableObject = $MapTableContextObject->getMapTableTableObject();

        /** @var MapTableColumnObject $PrimaryColumn */
        $PrimaryColumn = $MapTableTableObject->getPrimaryKeyColumn();

        /** @var string $stickySql */
        $stickySql = self::buildStickyString($MapTableContextObject, false);

        /** @var string $sql */
        $sql = '
            DELETE FROM
                ' . $MapTableTableObject->getName() . '
            WHERE
                ' . $PrimaryColumn->getField() . ' = ' . CoreSqlUtils::quote($MapTableContextObject->getPrimaryValue()) . '
                ' . $stickySql . '
            LIMIT
                1';

        return CoreSqlUtils::delete($sql);

    }

    /**
     * Get Listing
     *
     * @param MapTableContextObject $MapTableContextObject
     * @param bool|false $skipPagination
     * @return MapTableListingsObject
     */
    public function getListing(MapTableContextObject $MapTableContextObject, $skipPagination = false){

        /**
         * Build select query
         */
        $sql = 'SELECT * FROM ' . $MapTableContextObject->getTable() . ' ';

        /** @var MapTableTableObject $MapTableTableObject */
        $MapTableTableObject = $MapTableContextObject->getMapTableTableObject();

        /** @var boolean $where */
        $where = false;

        /**
         * If we are having a search
         */
        if($MapTableContextObject->haveSearch()){

            /** @var Array $indexes */
            $indexes = $MapTableTableObject->getIndexes();

            /** @var Array $searchColumns */
            $searchColumns = array();

            if(!empty($indexes)){
                /** @var MapTableIndexObject $MapTableIndexObject */
                foreach($indexes as $MapTableIndexObject){

                    /** Add the lookup columns for full text search */
                    if($MapTableIndexObject->getIndexType() == 'FULLTEXT') {
                        array_push($searchColumns, $MapTableIndexObject->getColumnName());
                    }

                }
            }

            if(!empty($searchColumns)) {
                $sql .= '
                    WHERE
                        MATCH
                            (' . implode(',', $searchColumns) . ')
                        AGAINST
                            (' . CoreSqlUtils::quote($MapTableContextObject->getSearchQuery()) . ' IN NATURAL LANGUAGE MODE)
                ';
                $where = true;
            }else{

                /** @var MapTableColumnObject $NameColumn */
                $NameColumn = $MapTableTableObject->getNameColumn();

                if(!empty($NameColumn)) {

                    $sql .= '
                    WHERE
                        CONCAT(" ", ' . $MapTableTableObject->getNameColumn()->getField() . ', " ")
                            LIKE
                                ' . CoreSqlUtils::quote('%' . $MapTableContextObject->getSearchQuery() . '%') . '
                    ';
                    $where = true;

                }else{

                    /** @var array $additionalWhereClauses */
                    $additionalWhereClauses = array();

                    /** @var MapTableColumnObject $column */
                    foreach($MapTableContextObject->getMapTableTableObject()->getColumns() as $column){

                        /** potentially join a related table */
                        if($column->getRelatedTable() !== null) {

                            /** @var MapTableColumnObject $ColumnName */
                            $ColumnName = $column->getRelatedTable()->getNameColumn();

                            /** Join related table if it has a name column */
                            if(!empty($ColumnName)) {

                                $sql .= '
                                    LEFT JOIN
                                        ' . $column->getRelatedTable()->getName() . '
                                    ON
                                        (
                                            ' . $MapTableTableObject->getName() . '.' . $column->getField() . ' = ' . $column->getRelatedTable()->getName() . '.' . $column->getField() . '
                                        )
                                    ';

                                array_push($additionalWhereClauses, ' ' . $column->getRelatedTable()->getName() . '.' . $column->getField() . ' IS NOT NULL AND CONCAT(" ", ' . $column->getRelatedTable()->getName() . '.' . $ColumnName->getField() . ', " ") LIKE ' . CoreSqlUtils::quote('%' . $MapTableContextObject->getSearchQuery() . '%') . ' ');

                            }

                        }
                    }

                    /** add the where clauses on name fields on associated tables */
                    $sql .= '
                    WHERE (' . implode(' OR ', $additionalWhereClauses) . ') ';

                    $where = true;

                }

            }

        }

        /** @var string $stickySql */
        $sql .= self::buildStickyString($MapTableContextObject, !$where);

        /** @var MapTableColumnObject $MapTableColumnObject */
        $MapTableColumnObject = $MapTableTableObject->getDateAddedColumn();

        /**
         * Sort descending
         * this will show most recent records first
         *
         */
        if($MapTableColumnObject){
            $sql .= '
                ORDER BY ' . $MapTableColumnObject->getField() . ' DESC
            ';
        }

        if(!$skipPagination){

            /**
             * Handle pagination
             */
            CorePagination::setSql($sql);
            $CorePaginationObject = CorePagination::runQuery();

            /** @var MapTableListingsObject $MapTableListingsObject */
            $MapTableListingsObject = CoreLogic::getObject('MapTableListingsObject');
            $MapTableListingsObject->setListings($CorePaginationObject->getRows());
            $MapTableListingsObject->setCorePaginationObject($CorePaginationObject);

            return $MapTableListingsObject;

        }else{

            /** @var MapTableListingsObject $MapTableListingsObject */
            $MapTableListingsObject = CoreLogic::getObject('MapTableListingsObject');
            $MapTableListingsObject->setListings(CoreSqlUtils::rows($sql));
            $MapTableListingsObject->setCorePaginationObject(null);

            return $MapTableListingsObject;

        }

    }

}