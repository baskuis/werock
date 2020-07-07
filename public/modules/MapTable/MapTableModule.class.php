<?php

/**
 * Map Table Module
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MapTableModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Map Table Module';
    public static $description = 'Allow forms to be build based on sql schema.';
    public static $version = '1.0.1';
    public static $dependencies = array(
        'Form' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        )
    );

    /**
     * List of events
     */
    const EVENT_DELETED = 'MAPTABLE:EVENTS:RECORD:DELETED';
    const EVENT_INSERTED = 'MAPTABLE:EVENTS:RECORD:INSERTED';
    const EVENT_UPDATED = 'MAPTABLE:EVENTS:RECORD:UPDATED';

    /**
     * Get listeners
     *
     * @return mixed
     */
    public static function getListeners()
    {

    }

    /**
     * Get interceptors
     *
     * @return mixed
     */
    public static function getInterceptors()
    {

    }

    /**
     * Get menus
     *
     * @return mixed
     */
    public static function getMenus()
    {

    }

    /**
     * Get routes
     *
     * @return mixed
     */
    public static function getRoutes()
    {

    }

    /** @var MapTableService $MapTableService */
    private static $MapTableService;

    /** @var MapTableRepository $MapTableRepository */
    private static $MapTableRepository;

    /**
     * Module init method
     */
    public static function __init__(){

        self::$MapTableService = CoreLogic::getService('MapTableService');
        self::$MapTableRepository = CoreLogic::getRepository('MapTableRepository');

        //configure form field mapping
        self::loadFieldMappingConfiguration();

    }

    /**
     * Configure form field mapping rules
     */
    private static function loadFieldMappingConfiguration(){

        /** @var MapTableMapColumnObject $MapTableMapColumnObject */

        /**
         * Name field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('name');
        $MapTableMapColumnObject->setAppendMatch('name');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([0-9]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Host field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('host');
        $MapTableMapColumnObject->setAppendMatch('host');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([0-9]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Path field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('path');
        $MapTableMapColumnObject->setAppendMatch('path');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([0-9]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Parameters field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('params');
        $MapTableMapColumnObject->setAppendMatch('params');
        $MapTableMapColumnObject->setDataTypeMatch('/^text$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtextarea');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Title field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('title');
        $MapTableMapColumnObject->setAppendMatch('title');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([0-9]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Title field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('valuetext');
        $MapTableMapColumnObject->setAppendMatch('value');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([0-9]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Hash field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('hash');
        $MapTableMapColumnObject->setAppendMatch('hash');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([0-9]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Slug field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('slug');
        $MapTableMapColumnObject->setAppendMatch('slug');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([0-9]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * URL field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('url');
        $MapTableMapColumnObject->setAppendMatch('url');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([0-9]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputurl');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * String field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('string');
        $MapTableMapColumnObject->setAppendMatch('string');
        $MapTableMapColumnObject->setDataTypeMatch('/^text$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtextarea');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Javascript field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('javascript');
        $MapTableMapColumnObject->setAppendMatch('javascript');
        $MapTableMapColumnObject->setDataTypeMatch('/^text$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtextarea');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Price field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('price');
        $MapTableMapColumnObject->setAppendMatch('price');
        $MapTableMapColumnObject->setDataTypeMatch('/^float/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * URN field text
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('urn');
        $MapTableMapColumnObject->setAppendMatch(array('urn'));
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Text field text
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('textarea');
        $MapTableMapColumnObject->setAppendMatch(array('text', 'description'));
        $MapTableMapColumnObject->setDataTypeMatch('/^text$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtextarea');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Text field varchar
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('textvarchar');
        $MapTableMapColumnObject->setAppendMatch(array('text', 'description'));
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Date field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('date');
        $MapTableMapColumnObject->setAppendMatch(array('date'));
        $MapTableMapColumnObject->setDataTypeMatch('/^date$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputdatepicker');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Time field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('time');
        $MapTableMapColumnObject->setAppendMatch(array('time'));
        $MapTableMapColumnObject->setDataTypeMatch('/^time/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtimepicker');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Color field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('color');
        $MapTableMapColumnObject->setAppendMatch(array('color'));
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar/i');
        $MapTableMapColumnObject->setInputTemplate('forminputcolor');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Parent Id
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('parent');
        $MapTableMapColumnObject->setAppendMatch(array('parent_id', 'parent'));
        $MapTableMapColumnObject->setDataTypeMatch('/^int/i');
        $MapTableMapColumnObject->setInputTemplate('formselectchosen');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        $MapTableMapColumnObject->setOptionMapper(function(MapTableColumnObject $MapTableColumnObject, MapTableContextObject $MapTableContextObject){

            /**
             * Options
             */
            $options = array();

            /** @var MapTableTableObject $MapTableTableObject */
            $MapTableTableObject = $MapTableContextObject->getMapTableTableObject();

            /** @var MapTableColumnObject $PrimaryColumn */
            $PrimaryColumn = $MapTableTableObject->getPrimaryKeyColumn();

            /** @var MapTableColumnObject $NameColumn */
            $NameColumn = $MapTableTableObject->getNameColumn();

            /** attempt to lookup related table */
            if(!$NameColumn) {
                /** @var MapTableColumnObject $MapTableColumnObject */
                foreach ($MapTableTableObject->getColumns() as $TempMapTableColumnObject) {
                    if ($TempMapTableColumnObject->getKey() == MapTableService::PRIMARY_KEY) continue;
                    if (null !== ($RelatedNameTable = $TempMapTableColumnObject->getRelatedTable())) {
                        CoreLog::debug('Did not find name column but found associated table ' . $RelatedNameTable->getName());
                        break;
                    }
                }
            }

            /**
             * Get records from DAO
             */
            $records = self::$MapTableService->getAllRecords();

            /**
             * Resolve parents
             */
            $records = CoreArrayUtils::resolveParents($records, $PrimaryColumn->getField(), $MapTableColumnObject->getField());

            /**
             * Build the options array
             */
            if(!empty($PrimaryColumn) && !empty($MapTableColumnObject)){

                /**
                 * Build simple lookup reference
                 */
                foreach($records as $record){

                    /**
                     * Prefix
                     */
                    $prefix = '';
                    for($i = 1; $i < $record['_level']; $i++){
                        $prefix .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                    }

                    $option = new FormFieldOption();

                    /** attempt to populate */
                    if($PrimaryColumn){ $option->setKey($record[$PrimaryColumn->getField()]); }
                    if($NameColumn){ $option->setValue($prefix . $record[$NameColumn->getField()]); }

                    /** handle alternate/related table description lookups */
                    if(isset($RelatedNameTable) && !empty($RelatedNameTable)){
                        $NamePriColumn = $RelatedNameTable->getPrimaryKeyColumn();
                        if($NamePriColumn) {
                            $nameRecord = self::$MapTableRepository->getRecord($RelatedNameTable->getName(), $NamePriColumn->getField(), $record[$NamePriColumn->getField()]);
                            $RelatedNameColumn = $RelatedNameTable->getNameColumn();
                            if(isset($RelatedNameColumn)) {
                                if(isset($nameRecord[$RelatedNameColumn->getField()])) {
                                    $option->setValue($prefix . $nameRecord[$RelatedNameColumn->getField()]);
                                }
                            }
                        }
                    }

                    array_push($options, $option);

                }

            }

            /**
             * Return options
             */
            return $options;

        });
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Template picker
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('template');
        $MapTableMapColumnObject->setAppendMatch('template');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar/i');
        $MapTableMapColumnObject->setInputTemplate('formselectchosen');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        $MapTableMapColumnObject->setOptionMapper(function(MapTableColumnObject $MapTableColumnObject, MapTableContextObject $MapTableContextObject){

            $options = array();

            /** @var CoreTemplateObject $CoreTemplateObject */
            foreach(CoreTemplate::getCoreTemplates() as &$CoreTemplateObject){
                $option = new FormFieldOption();
                $option->setKey($CoreTemplateObject->getNamespace());
                $option->setValue($CoreTemplateObject->getNamespace());
                array_push($options, $option);
            }

            return $options;

        });
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Percentage
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('percentage');
        $MapTableMapColumnObject->setAppendMatch('percentage');
        $MapTableMapColumnObject->setDataTypeMatch('/int/i');
        $MapTableMapColumnObject->setInputTemplate('forminputpercentage');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Html field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('html');
        $MapTableMapColumnObject->setAppendMatch('html');
        $MapTableMapColumnObject->setDataTypeMatch('/text/i');
        $MapTableMapColumnObject->setInputTemplate('forminputhtml');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * People picker field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('people');
        $MapTableMapColumnObject->setAppendMatch('people');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([0-9]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('formfieldpeoplepicker');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Password field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('password');
        $MapTableMapColumnObject->setAppendMatch(array('password', 'pw'));
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([0-9]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputpassword');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Select enum
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('enum');
        $MapTableMapColumnObject->setAppendMatch(null);
        $MapTableMapColumnObject->setDataTypeMatch('/^enum\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('formselectchosen');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Size Text Field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('size');
        $MapTableMapColumnObject->setAppendMatch('size');
        $MapTableMapColumnObject->setDataTypeMatch('/^int\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Type Text Field
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('type');
        $MapTableMapColumnObject->setAppendMatch('type');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Visitor ID
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('visitor');
        $MapTableMapColumnObject->setAppendMatch('visitor_id');
        $MapTableMapColumnObject->setDataTypeMatch('/^int\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputvisitor');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        $MapTableMapColumnObject->setFormFieldModifier(function(FormField $formField, MapTableContextObject $mapTableContextObject){

            /**
             * Allows setting
             */
            if($formField->getValue() == 0){
                $formField->setValue(CoreVisitor::getId());
            }

            /**
             * Get visitor information
             * and populate the model accordingly
             */
            $formField->setData(array('visitor' => Corevisitor::getVisitor((int) $formField->getValue())));

            /**
             * Return the modified form field
             */
            return $formField;

        });
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Key Varchar Text Field
         *
         * or find a way to have these mapping fall to
         * the end of the list
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('keyvarchar');
        $MapTableMapColumnObject->setAppendMatch('key');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Regex Varchar Text Field
         *
         * or find a way to have these mapping fall to
         * the end of the list
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('regexvarchar');
        $MapTableMapColumnObject->setAppendMatch('regex');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputregextext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Value Textarea
         *
         * or find a way to have these mapping fall to
         * the end of the list
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('valuetextarea');
        $MapTableMapColumnObject->setAppendMatch('value');
        $MapTableMapColumnObject->setDataTypeMatch('/^text$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtextarea');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Address Field
         *
         * or find a way to have these mapping fall to
         * the end of the list
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('addressvarchar');
        $MapTableMapColumnObject->setAppendMatch('address');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Email Field
         *
         * or find a way to have these mapping fall to
         * the end of the list
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('emailvarchar');
        $MapTableMapColumnObject->setAppendMatch('email');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * City Field
         *
         * or find a way to have these mapping fall to
         * the end of the list
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('cityvarchar');
        $MapTableMapColumnObject->setAppendMatch('city');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * State Field
         *
         * or find a way to have these mapping fall to
         * the end of the list
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('state');
        $MapTableMapColumnObject->setAppendMatch('state');
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('formselectchosen');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        $MapTableMapColumnObject->setOptionMapper(function(MapTableColumnObject $MapTableColumnObject, MapTableContextObject $MapTableContextObject){
            $options = array();
            $states = CoreGeoUtils::getUSStateMap();
            foreach($states as $abbr => $state){
                $option = new FormFieldOption();
                $option->setKey($abbr);
                $option->setValue($state);
                array_push($options, $option);
            }
            return $options;
        });
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * ZipCode Field
         *
         * or find a way to have these mapping fall to
         * the end of the list
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('zipcode');
        $MapTableMapColumnObject->setAppendMatch(array('zipcode', 'zip'));
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Active Field
         *
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('active');
        $MapTableMapColumnObject->setAppendMatch(array('active', 'enabled'));
        $MapTableMapColumnObject->setDataTypeMatch('/tinyint\(1\)/i');
        $MapTableMapColumnObject->setInputTemplate('forminputboolean');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Host Field
         *
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('host');
        $MapTableMapColumnObject->setAppendMatch(array('host', 'domain'));
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Message Field
         *
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('message');
        $MapTableMapColumnObject->setAppendMatch(array('message'));
        $MapTableMapColumnObject->setDataTypeMatch('/^text$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtextarea');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Access token
         *
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('access_token');
        $MapTableMapColumnObject->setAppendMatch(array('access_token'));
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputpassword');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /**
         * Fallback Varchar Field
         *
         */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('varchar');
        $MapTableMapColumnObject->setAppendMatch(array('*'));
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar\(([^\)]+)\)$/i');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

    }

    /**
     * Run on update
     *
     * @param $previousVersion
     * @param $newVersion
     *
     * @return void
     */
    public static function __update__($previousVersion, $newVersion)
    {

    }

    /**
     * Run on enable
     *
     * @return void
     */
    public static function __enable__()
    {

    }

    /**
     * Run on disable
     *
     * @return mixed
     */
    public static function __disable__()
    {

    }

    /**
     * Run on install
     *
     */
    public static function __install__()
    {

    }

}