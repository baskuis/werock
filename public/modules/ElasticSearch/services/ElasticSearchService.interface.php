<?php

/**
 * ElasticSearch Manager Interface
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
interface ElasticSearchServiceInterface {

    /**
     * Get elastic search status
     *
     * @return mixed
     */
    public function status();

    /**
     * Perform elastic search
     *
     * @param null $index
     * @param null $type
     * @param null $query
     * @param array $options
     *
     * @return ElasticSearchResultsObject
     */
    public function simpleSearch($index = null, $type = null, $query = null, $options = array());

    /**
     * Perform elastic search
     *
     * @param ElasticSearchQueryObject $ElasticSearchQueryObject
     * @return mixed
     */
    public function search(ElasticSearchQueryObject $ElasticSearchQueryObject);

    /**
     * Get elastic search record
     *
     * @param null $index
     * @param null $type
     * @param null $id
     * @return mixed
     */
    public function get($index = null, $type = null, $id = null);

    /**
     * Perform raw search
     *
     * @param null $index
     * @param null $type
     * @param array $payload
     * @return mixed
     */
    public function rawSearch($index = null, $type = null, $payload = array());

}