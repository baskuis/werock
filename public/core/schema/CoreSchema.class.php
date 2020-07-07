<?php

/**
 * Core Schema
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
class CoreSchema {

    /**
     * Schema file
     */
    const SCHEMA_FILE = "schema.json";
    
    /**
     * Constants
     */
    const PATH_SLASH = '/';
    
    /**
     * Load Schema at this path
     *
     * @param null $path
     * @param boolean $force
     * @throws Exception
     */
    public static function load($path = null, $force = false){

        //stop here
        if(!FORCE_DEV_MODE && !BUILD_SCHEMA && !$force) return;

        //skip if not found
        if(!is_file(DOCUMENT_ROOT . $path . self::PATH_SLASH . self::SCHEMA_FILE)) return;

        //get proposed schema
        $schemaString = file_get_contents(DOCUMENT_ROOT . $path . self::PATH_SLASH . self::SCHEMA_FILE);

        //attempt to decode the schema
        if(null === ($schema = json_decode($schemaString))){

            //handle json parsing error
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    // no errors
                    break;
                case JSON_ERROR_DEPTH:
                    throw new Exception('Unable to decode contents of schema file: ' . DOCUMENT_ROOT . $path . self::PATH_SLASH . self::SCHEMA_FILE . ' Issue: Maximum stack depth exceeded');
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    throw new Exception('Unable to decode contents of schema file: ' . DOCUMENT_ROOT . $path . self::PATH_SLASH . self::SCHEMA_FILE . ' Issue: Underflow or the modes mismatch');
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    throw new Exception('Unable to decode contents of schema file: ' . DOCUMENT_ROOT . $path . self::PATH_SLASH . self::SCHEMA_FILE . ' Issue: Unexpected control character found');
                    break;
                case JSON_ERROR_SYNTAX:
                    throw new Exception('Unable to decode contents of schema file: ' . DOCUMENT_ROOT . $path . self::PATH_SLASH . self::SCHEMA_FILE . ' Issue: Syntax error, malformed JSON');
                    break;
                case JSON_ERROR_UTF8:
                    throw new Exception('Unable to decode contents of schema file: ' . DOCUMENT_ROOT . $path . self::PATH_SLASH . self::SCHEMA_FILE . ' Issue: Malformed UTF-8 characters, possibly incorrectly encoded');
                    break;
                default:
                    throw new Exception('Unable to decode contents of schema file: ' . DOCUMENT_ROOT . $path . self::PATH_SLASH . self::SCHEMA_FILE . ' Issue: Unknown error');
                    break;
            }

            throw new Exception('Unable to decode contents of schema file: ' . DOCUMENT_ROOT . $path . self::PATH_SLASH . self::SCHEMA_FILE . ' Issue: ' . json_last_error());

        }

        /** Assure schema is not empty - suggest removal */
        if(empty($schema)) throw new Exception('Please remove empty schema file: ' . DOCUMENT_ROOT . $path . self::PATH_SLASH . self::SCHEMA_FILE);

        /** Build table references */
        foreach($schema as $table){

            //assertions
            if(!isset($table->table)) throw new Exception('Need table reference');
            if(!isset($table->fields) || empty($table->fields)) throw new Exception('Need fields');

            //populate table reference
            $CoreSchemaTableObject = new CoreSchemaTableObject();
            $CoreSchemaTableObject->setTable((isset($table->table) ? $table->table : null));
            $CoreSchemaTableObject->setCollation((isset($table->collation) ? $table->collation : null));
            $CoreSchemaTableObject->setEngine((isset($table->engine) ? $table->engine : null));
            $CoreSchemaTableObject->setRowFormat((isset($table->row_format) ? $table->row_format : null));
            $CoreSchemaTableObject->setComments((isset($table->comments) ? $table->comments : null));

            //populate fields
            foreach($table->fields as $field){

                //populate fields
                $CoreSchemaTableColumnObject = new CoreSchemaTableColumnObject();
                $CoreSchemaTableColumnObject->setName((isset($field->name) ? $field->name : null));
                $CoreSchemaTableColumnObject->setType((isset($field->type) ? $field->type : null));
                $CoreSchemaTableColumnObject->setNull((isset($field->null) ? $field->null : null));
                $CoreSchemaTableColumnObject->setDefault((isset($field->default) ? $field->default : null));
                $CoreSchemaTableColumnObject->setExtra((isset($field->extra) ? $field->extra : null));
                $CoreSchemaTableColumnObject->setComments((isset($field->comments) ? $field->comments : null));

                //add field
                $CoreSchemaTableObject->addField($CoreSchemaTableColumnObject);

            }

            //populate indexes
            if(isset($table->indexes) && !empty($table->indexes)){
                foreach($table->indexes as $index){

                    //populate index
                    $CoreSchemaTableKeyObject = new CoreSchemaTableKeyObject();
                    $CoreSchemaTableKeyObject->setName((isset($index->name) ? $index->name : null));
                    $CoreSchemaTableKeyObject->setType((isset($index->type) ? $index->type : null));
                    $CoreSchemaTableKeyObject->setFields((isset($index->fields) ? $index->fields : null));
                    $CoreSchemaTableKeyObject->setMethod((isset($index->method) ? $index->method : null));
                    $CoreSchemaTableKeyObject->setComment((isset($index->comments) ? $index->comments : null));

                    //add index
                    $CoreSchemaTableObject->addIndex($CoreSchemaTableKeyObject);

                }
            }

            /** Parse */
            self::parse($CoreSchemaTableObject);

        }

    }

    /**
     * Create json representation of table
     *
     * @param null $table
     * @return object
     * @throws CoreSchemaTableNotFoundException
     * @throws Exception
     */
    public static function tableToJson($table = null){

        /** Choose type */
        switch(CoreData::$type){

            /**
             * Parse MySql
             */
            case CoreData::TYPE_MYSQL:
                return self::interpretMysql($table);
                break;

            /**
             * Throw Exception when datatype is not recognized
             */
            default:
                throw new Exception('Unable to determine interpretation for SQL with type: ' . CoreData::$type);
                break;

        }

    }

    /**
     * Interpret a mysql table
     *
     * @param null $table
     * @return object
     * @throws CoreSchemaTableNotFoundException
     * @throws Exception
     */
    private static function interpretMysql($table = null){

        /** Assertion */
        if(!CoreSqlUtils::exists($table)) throw new CoreSchemaTableNotFoundException('Unable to find table ' . $table);

        /** @var object $interpretation */
        $interpretation = new stdClass();

        //get table status
        $CoreSchemaTableStatusObject = new CoreSchemaTableStatusObject();
        $status = CoreSqlUtils::row("SHOW TABLE STATUS FROM `" . MYSQL_DATABASE . "` WHERE Name = " . CoreSqlUtils::quote($table));
        if(!$status) throw new Exception('Unable to get status for table ' . $table . ' in ' . MYSQL_DATABASE);
        $CoreSchemaTableStatusObject->setName($status['Name']);
        $CoreSchemaTableStatusObject->setAutoIncrement($status['Auto_increment']);
        $CoreSchemaTableStatusObject->setAvgRowLength($status['Avg_row_length']);
        $CoreSchemaTableStatusObject->setCollation($status['Collation']);
        $CoreSchemaTableStatusObject->setChecksum($status['Checksum']);
        $CoreSchemaTableStatusObject->setCheckTime($status['Check_time']);
        $CoreSchemaTableStatusObject->setComment($status['Comment']);
        $CoreSchemaTableStatusObject->setCreateOptions($status['Create_options']);
        $CoreSchemaTableStatusObject->setCreateTime($status['Create_time']);
        $CoreSchemaTableStatusObject->setDataFree($status['Data_free']);
        $CoreSchemaTableStatusObject->setDataLength($status['Data_length']);
        $CoreSchemaTableStatusObject->setDataMaxLength($status['Data_max_length']);
        $CoreSchemaTableStatusObject->setEngine($status['Engine']);
        $CoreSchemaTableStatusObject->setIndexLength($status['Index_length']);
        $CoreSchemaTableStatusObject->setRowFormat($status['Row_format']);
        $CoreSchemaTableStatusObject->setVersion($status['Version']);

        /**
         * Create interpretation of table
         */
        $interpretation->table = $CoreSchemaTableStatusObject->getName();
        $interpretation->engine = $CoreSchemaTableStatusObject->getEngine();
        $interpretation->collation = $CoreSchemaTableStatusObject->getCollation();
        $interpretation->row_format = $CoreSchemaTableStatusObject->getRowFormat();

        //get existing columns
        $existingFields = array();
        $columns = CoreSqlUtils::rows("SHOW FULL COLUMNS FROM " . $table);
        if(empty($columns)) throw new Exception('Unable to read columns from table ' . $table);
        foreach($columns as $column){

            /** @var object $column_interpretation */
            $column_interpretation = new stdClass();

            //create existing column reference
            $ExistingColumn = new CoreSchemaTableColumnObject();
            $ExistingColumn->setName($column['Field']);
            $ExistingColumn->setType($column['Type']);
            $ExistingColumn->setNull($column['Null']);
            $ExistingColumn->setDefault((substr($column['Type'], 0, 4) == 'enum') ? '\'' . $column['Default'] . '\'' : $column['Default']);
            $ExistingColumn->setExtra($column['Extra']);
            $ExistingColumn->setComments($column['Comment']);

            /**
             * Create schema interpretation
             */
            $column_interpretation->name = $ExistingColumn->getName();
            $column_interpretation->type = $ExistingColumn->getType();
            $column_interpretation->null = $ExistingColumn->getNull();
            $column_interpretation->default = $ExistingColumn->getDefault();
            $column_interpretation->extra = $ExistingColumn->getExtra();
            $column_interpretation->comments = $ExistingColumn->getComments();

            /** stack it */
            array_push($existingFields, $column_interpretation);

        }

        /** @var array fields */
        $interpretation->fields = $existingFields;

        //get existing indexes
        $existingIndexes = array();
        $indexes = CoreSqlUtils::rows("SHOW INDEXES FROM " . $table);
        if(!empty($indexes)){
            foreach($indexes as $index){

                //create index reference
                $CoreSchemaTableIndexObject = new CoreSchemaTableIndexObject();
                $CoreSchemaTableIndexObject->setNonUnique($index['Non_unique']);
                $CoreSchemaTableIndexObject->setKeyName($index['Key_name']);
                $CoreSchemaTableIndexObject->setSeqInIndex($index['Seq_in_index']);
                $CoreSchemaTableIndexObject->setColumnName($index['Column_name']);
                $CoreSchemaTableIndexObject->setCollation($index['Collation']);
                $CoreSchemaTableIndexObject->setCardinality($index['Cardinality']);
                $CoreSchemaTableIndexObject->setSubPart($index['Sub_part']);
                $CoreSchemaTableIndexObject->setPacked($index['Packed']);
                $CoreSchemaTableIndexObject->setNull($index['Null']);
                $CoreSchemaTableIndexObject->setIndexType($index['Index_type']);
                $CoreSchemaTableIndexObject->setComment($index['Comment']);
                $CoreSchemaTableIndexObject->setIndexComment($index['Index_comment']);

                //keep existing index reference
                if(!isset($existingIndexes[$CoreSchemaTableIndexObject->getKeyName()])){
                    $existingIndexes[$CoreSchemaTableIndexObject->getKeyName()] = $CoreSchemaTableIndexObject;
                }else{
                    $existingIndexes[$CoreSchemaTableIndexObject->getKeyName()]->setColumnName($index['Column_name']);
                }

            }

            /**
             * Now their are organized
             * lets step through the list
             *
             * @var CoreSchemaTableIndexObject $existingIndex
             */
            $adding_existing_indexes = array();
            foreach($existingIndexes as $existingIndex){

                /** @var object $index_interpretation */
                $index_interpretation = new stdClass();

                /**
                 * Interpret name type and method
                 */
                switch($existingIndex->getKeyName()){
                    case 'PRIMARY':
                        $index_interpretation->name = '';
                        $index_interpretation->type = 'PRIMARY';
                        break;
                    default:
                        $index_interpretation->name = $existingIndex->getKeyName();
                        break;
                }

                switch($existingIndex->getIndexType()){
                    case 'FULLTEXT':
                        $index_interpretation->type = 'FULLTEXT';
                        $index_interpretation->method = '';
                        break;
                    default:
                        if(!$index_interpretation->type) $index_interpretation->type = 'INDEX';
                        if(!$index_interpretation->method) $index_interpretation->method = 'BTREE';
                        break;
                }

                if($existingIndex->getNonUnique() == '0' && empty($index_interpretation->type)){
                    $index_interpretation->type = 'UNIQUE';
                }

                /**
                 * Set the column names
                 */
                $index_interpretation->fields = $existingIndex->getColumnName();

                /** Stack the index interpretation */
                array_push($adding_existing_indexes, $index_interpretation);

            }

            /** @var array indexes */
            $interpretation->indexes = $adding_existing_indexes;

        }

        return $interpretation;

    }

    /**
     * Parse core schema table object
     *
     * @param CoreSchemaTableObject $CoreSchemaTableObject
     * @throws Exception
     */
    private static function parse(CoreSchemaTableObject $CoreSchemaTableObject){

        /** Choose type */
        switch(CoreData::$type){

            /**
             * Parse MySql
             */
            case CoreData::TYPE_MYSQL:
                self::parseMysql($CoreSchemaTableObject);
                break;

            /**
             * Throw Exception when datatype is not recognized
             */
            default:
                throw new Exception('Unable to determine interpretation for SQL with type: ' . CoreData::$type);
                break;

        }

    }

    /**
     * Parse a mysql schema
     *
     * @param CoreSchemaTableObject $CoreSchemaTableObject
     * @throws Exception
     */
    private static function parseMysql(CoreSchemaTableObject $CoreSchemaTableObject){

        /**
         * If update
         */
        if(CoreSqlUtils::exists($CoreSchemaTableObject->getTable())){

            //get existing columns
            $existingFields = array();
            $columns = CoreSqlUtils::rows("SHOW FULL COLUMNS FROM " . $CoreSchemaTableObject->getTable());
            if(empty($columns)) throw new Exception('Unable to read columns from table ' . $CoreSchemaTableObject->getTable());
            foreach($columns as $column){

                //create existing column reference
                $ExistingColumn = new CoreSchemaTableColumnObject();
                $ExistingColumn->setName($column['Field']);
                $ExistingColumn->setType($column['Type']);
                $ExistingColumn->setNull($column['Null']);
                $ExistingColumn->setDefault($column['Default']);
                $ExistingColumn->setExtra($column['Extra']);
                $ExistingColumn->setComments($column['Comment']);

                //store existing reference
                $existingFields[$ExistingColumn->getName()] = $ExistingColumn;

            }

            //get existing indexes
            $existingIndexes = array();
            $indexes = CoreSqlUtils::rows("SHOW INDEXES FROM " . $CoreSchemaTableObject->getTable());
            if(!empty($indexes)){
                foreach($indexes as $index){

                    //create index reference
                    $CoreSchemaTableIndexObject = new CoreSchemaTableIndexObject();
                    $CoreSchemaTableIndexObject->setNonUnique($index['Non_unique']);
                    $CoreSchemaTableIndexObject->setKeyName($index['Key_name']);
                    $CoreSchemaTableIndexObject->setSeqInIndex($index['Seq_in_index']);
                    $CoreSchemaTableIndexObject->setColumnName($index['Column_name']);
                    $CoreSchemaTableIndexObject->setCollation($index['Collation']);
                    $CoreSchemaTableIndexObject->setCardinality($index['Cardinality']);
                    $CoreSchemaTableIndexObject->setSubPart($index['Sub_part']);
                    $CoreSchemaTableIndexObject->setPacked($index['Packed']);
                    $CoreSchemaTableIndexObject->setNull($index['Null']);
                    $CoreSchemaTableIndexObject->setIndexType($index['Index_type']);
                    $CoreSchemaTableIndexObject->setComment($index['Comment']);
                    $CoreSchemaTableIndexObject->setIndexComment($index['Index_comment']);

                    //keep existing index reference
                    if(!isset($existingIndexes[$CoreSchemaTableIndexObject->getKeyName()])){
                        $existingIndexes[$CoreSchemaTableIndexObject->getKeyName()] = $CoreSchemaTableIndexObject;
                    }else{
                        $existingIndexes[$CoreSchemaTableIndexObject->getKeyName()]->setColumnName($index['Column_name']);
                    }

                }
            }

            //handle fields
            $fields = $CoreSchemaTableObject->getFields();
            $last_column_name = null;
            /** @var CoreSchemaTableColumnObject $field */
            foreach($fields as $field){

                /**
                 * See if we need to modify an existing column
                 */
                if(isset($existingFields[$field->getName()])){

                    /**
                     * See if the column has been updated
                     */
                    $column_changed = false;
                    /** @var CoreSchemaTableColumnObject $existingField */
                    $existingField = $existingFields[$field->getName()];
                    switch(true){
                        case ($field->getType() != $existingField->getType()):
                            $column_changed = true;
                            break;
                        case (CoreStringUtils::evaluateBoolean($field->getNull()) != CoreStringUtils::evaluateBoolean($existingField->getNull())):
                            $column_changed = true;
                            break;
                        case (str_replace('\'', null, $field->getDefault()) != str_replace('\'', null, $existingField->getDefault())
                            && !($field->getDefault() == 'NULL' && $existingField->getDefault() == '')):
                            $column_changed = true;
                            break;
                        case (strtolower($field->getExtra()) != strtolower($existingField->getExtra())):
                            $column_changed = true;
                            break;
                        case ($field->getComments() != $existingField->getComments()):
                            $column_changed = true;
                            break;
                   }

                    /**
                     * Alter column
                     */
                    if($column_changed){

                        /** @var string $sql */
                        $sql = "
                            ALTER TABLE `" . MYSQL_DATABASE . "`.`" . $CoreSchemaTableObject->getTable() . "` " .
                            "CHANGE COLUMN `" . $field->getName() . "`
                            `" . $field->getName() . "`
                            " . $field->getType() . "
                            " . (CoreStringUtils::evaluateBoolean($field->getNull()) ? 'NULL' : 'NOT NULL') . "
                            " . (!empty($field->extra) ? $field->getExtra() : "") . "
                            " . (!empty($field->default) ? " DEFAULT " . $field->getDefault() : "") .
                            " COMMENT " . CoreSqlUtils::quote($field->getComments());

                        /** Retain order */
                        if($last_column_name){
                            $sql .= " AFTER `" . $last_column_name . "`";
                        }

                        /** Run the alter table column query */
                        CoreSqlUtils::query($sql);

                    }

                /**
                 * Add column to database
                 */
                }else{

                    /** @var string $sql */
                    $sql = "
                        ALTER TABLE `" . MYSQL_DATABASE . "`.`" . $CoreSchemaTableObject->getTable() . "` " .
                        "ADD COLUMN
                        `" . $field->getName() . "`
                        " . $field->getType() . "
                        " . (CoreStringUtils::evaluateBoolean($field->getNull()) ? 'NULL' : 'NOT NULL') . "
                        " . (!empty($field->extra) ? $field->getExtra() : "") . "
                        " . (!empty($field->default) ? " DEFAULT " . $field->getDefault() : "") .
                        " COMMENT " . CoreSqlUtils::quote($field->getComments());

                    /** Retain order */
                    if($last_column_name){
                        $sql .= " AFTER `" . $last_column_name . "`";
                    }

                    /** Run the alter table all column query */
                    CoreSqlUtils::query($sql);

                }

                //last column
                $last_column_name = $field->getName();

            }

            //indexes
            $indexes = $CoreSchemaTableObject->getIndexes();

            if(!empty($indexes)){
                /** @var CoreSchemaTableKeyObject $index */
                foreach($indexes as $index){

                    /**
                     * First lets see if we need to update an existing index
                     */
                    $name = $index->getName();
                    if(empty($name)){
                        $name = $index->getType();
                    }
                    if(isset($existingIndexes[$name])){

                        /**
                         * See if the index needs to be updated
                         */
                        $index_changed = false;

                        /** @var CoreSchemaTableIndexObject $existingIndex */
                        $existingIndex = $existingIndexes[$name];

                        /**
                         * See if index has been changed
                         */
                        switch(true){
                            case ($index->getFields() != $existingIndex->getColumnName()):
                                $index_changed = true;
                                break;
                            case (strtoupper($index->getType()) == 'INDEX' && $index->getMethod() != '' && $index->getMethod() != $existingIndex->getIndexType()):
                                $index_changed = true;
                                break;
                            case ($index->getComment() != $existingIndex->getIndexComment()):
                                $index_changed = true;
                                break;
                            case(!CoreStringUtils::evaluateBoolean($existingIndex->getNonUnique()) !== (strtoupper($index->getType()) == 'UNIQUE' || $existingIndex->getKeyName() == 'PRIMARY')):
                                $index_changed = true;
                                break;
                        }

                        /**
                         * Need to update the index
                         */
                        if($index_changed){

                            /**
                             * Build the alter table query now
                             */
                            $sql = "
                                ALTER IGNORE TABLE `" . MYSQL_DATABASE . "`.`" . $CoreSchemaTableObject->getTable() . "` " .
                                "DROP " . strtoupper((strtoupper($index->getType()) == 'PRIMARY' ? "PRIMARY KEY" : "INDEX")) .
                                (!empty($index->name) ? "`" . $index->getName() . "`" : "") . "," .
                                "ADD " . strtoupper((strtoupper($index->getType()) == 'PRIMARY' ? "PRIMARY KEY" : $index->getType())) . " " .
                                (!empty($index->name) ? "`" . $index->getName() . "`" : "") .
                                (!empty($index->method) ? " USING " . $index->getMethod() : "") .
                                " (`" . implode("`,`", $index->getFields()) . "`) " .
                                (!empty($index->comment) ? " COMMENT " . CoreSqlUtils::quote($index->getComment()) : "");

                            /**
                             * Run the modify index query now
                             */
                            CoreSqlUtils::query($sql);

                        }

                    /**
                     * Else lets add the index to this table
                     */
                    }else{

                        /**
                         * Build the alter table query now
                         */
                        $sql = "
                            ALTER IGNORE TABLE `" . MYSQL_DATABASE . "`.`" . $CoreSchemaTableObject->getTable() . "` " .
                            "ADD " . strtoupper((strtoupper($index->getType()) == 'PRIMARY' ? "PRIMARY KEY" : $index->getType())) . " " .
                            (!empty($index->name) ? "`" . $index->getName() . "`" : "") .
                            (!empty($index->method) ? " USING " . $index->getMethod() : "") .
                            " (`" . implode("`,`", $index->getFields()) . "`) " .
                            (!empty($index->comment) ? " COMMENT " . CoreSqlUtils::quote($index->getComment()) : "");

                        /**
                         * Run the modify index query now
                         * adding this index
                         */
                        CoreSqlUtils::query($sql);

                    }

                }
            }

        /**
         * If create
         */
        }else{

            //create table
            $sql = "CREATE TABLE `" . $CoreSchemaTableObject->getTable() . "` (";

            /**
             * Create example:
             *
             *  CREATE TABLE `werock`.`<table_name>` (
                `test_id` int(3) NOT NULL AUTO_INCREMENT,
                `test_name` varchar(85),
                `test_description` text,
                `test_date_added` datetime,
                PRIMARY KEY (`test_id`),
                FULLTEXT `x_text` (`test_name`, `test_description`) comment '',
                INDEX `x_added` USING BTREE (`test_date_added`) comment ''
                ) ENGINE=`MyISAM` COMMENT='';
             */

            //build fields
            $fields = $CoreSchemaTableObject->getFields();
            /** @var CoreSchemaTableColumnObject $field */
            foreach($fields as $field){
                $sql .= "
                    `" . $field->getName() . "`
                    " . $field->getType() . "
                    " . (CoreStringUtils::evaluateBoolean($field->getNull()) ? 'NULL' : 'NOT NULL') . "
                    " . (!empty($field->extra) ? $field->getExtra() : "") . "
                    " . (!empty($field->default) ? " DEFAULT " . $field->getDefault() : "") .
                    " COMMENT " . CoreSqlUtils::quote($field->getComments()) . ",\n";
            }

            //indexes
            $indexes = $CoreSchemaTableObject->getIndexes();
            if(!empty($indexes)){
                /** @var CoreSchemaTableKeyObject $index */
                foreach($indexes as $index){
                    $sql .=
                        (strtoupper($index->getType()) == 'PRIMARY' ? "PRIMARY KEY" : strtoupper($index->getType())) . " " .
                        (!empty($index->name) ? "`" . $index->getName() . "`" : "") .
                        (!empty($index->method) ? " USING " . $index->getMethod() : "") .
                        " (`" . implode("`,`", $index->getFields()) . "`) " .
                        (!empty($index->comment) ? " COMMENT " . CoreSqlUtils::quote($index->getComment()) : "") . ",\n";
                }
            }

            //trim trailing comma and wrap up statement
            $sql = rtrim($sql, ",\n") . "
                )
                ENGINE=" . $CoreSchemaTableObject->getEngine() . "
                AUTO_INCREMENT=" . DEFAULT_AUTO_INCREMENT_BASE . "
                DEFAULT CHARSET=" . $CoreSchemaTableObject->getCollation() . "
                COMMENT " . CoreSqlUtils::quote($CoreSchemaTableObject->getComments());

            /**
             * CREATE THE TABLE
             */
            CoreSqlUtils::query($sql);

        }

    }

}