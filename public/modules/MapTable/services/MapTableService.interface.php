<?php

/**
 * Map Table Service Interface
 * this interface allows for overloading of the underlying implementation
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
interface MapTableServiceInterface {

    /**
     * Add special field mapping - this will load appropriate template and validation for field type based
     * on mapping defined in parameter object
     *
     * @param MapTableMapColumnObject $MapTableMapColumnObject
     * @return mixed
     */
    public function addMapping(MapTableMapColumnObject $MapTableMapColumnObject);

    /**
     * Set context for maptable process - this will include table name and
     * relevant templates and settings
     *
     * @param MapTableContextObject $MapTableContextObject
     * @return mixed
     */
    public function setContext(MapTableContextObject $MapTableContextObject);

    /**
     * Potentially a sticky field can be added that filters records by a certain column
     * for instance when you want only records owned by a certain user to show
     *
     * @param MapTableStickyFieldObject $MapTableStickyFieldObject
     * @return mixed
     */
    public function setStickyField(MapTableStickyFieldObject $MapTableStickyFieldObject);

    /**
     * This method starts the mapping process
     *
     * @return mixed
     */
    public function mapTables();

    /**
     * This method builds the form configuration
     * this is statically stored and will render when templates render
     *
     * @return mixed
     */
    public function buildForm();

    /**
     * Getter for all records in this table
     *
     * @return mixed
     */
    public function getAllRecords();

    /**
     * Builder for listings - listing is a normalized view of a table row
     *
     * @return mixed
     */
    public function buildListings();

    /**
     * This will receive the modifying action object - this will build the form configuration,
     * tell the action what template to render
     * and will load the browse data in the model
     *
     * @param MapTableContextObject $MapTableContextObject
     * @return MapTableActionModifierObject
     */
    public function fromContext(MapTableContextObject $MapTableContextObject);

}