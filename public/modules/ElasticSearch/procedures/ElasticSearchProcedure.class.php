<?php

/**
 * ElasticSearch Proxy
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class ElasticSearchProcedure {

    /** @var ElasticSearchRepository $ElasticSearchRepository */
    private $ElasticSearchRepository;

    function __construct(){
        $this->ElasticSearchRepository = CoreLogic::getRepository('ElasticSearchRepository');
    }

    /**
     * Get elastic search status
     *
     * @return mixed
     */
    public function status(){
        return $this->ElasticSearchRepository->status();
    }

    /**
     * Search elastic search
     *
     * @param null $index
     * @param null $type
     * @param null $query
     * @param array $options
     * @return ElasticSearchResultsObject
     */
    public function simpleSearch($index = null, $type = null, $query = null, $options = array()){

        /** @var array $response */
        $response = $this->ElasticSearchRepository->simpleSearch($index, $type, $query, $options);

        /** @var ElasticSearchResultsObject $ElasticSearchResultsObject */
        $ElasticSearchResultsObject = CoreLogic::getObject('ElasticSearchResultsObject');
        $ElasticSearchResultsObject->load($response);

        return $ElasticSearchResultsObject;

    }

    /**
     * Advanced search
     *
     * @param ElasticSearchQueryObject $ElasticSearchQueryObject
     * @return ElasticSearchResultsObject
     */
    public function search(ElasticSearchQueryObject $ElasticSearchQueryObject){

        /** @var array $response */
        $response = $this->ElasticSearchRepository->search($ElasticSearchQueryObject);

        /** @var ElasticSearchResultsObject $ElasticSearchResultsObject */
        $ElasticSearchResultsObject = CoreLogic::getObject('ElasticSearchResultsObject');
        $ElasticSearchResultsObject->load($response);

        return $ElasticSearchResultsObject;

    }

    /**
     * Raw search
     *
     * @param null $index
     * @param null $type
     * @param array $payload
     * @return ElasticSearchResultsObject
     */
    public function rawSearch($index = null, $type = null, $payload = array()){

        /** @var array $response */
        $response =  $this->ElasticSearchRepository->rawSearch($index, $type, $payload);

        /** @var ElasticSearchResultsObject $ElasticSearchResultsObject */
        $ElasticSearchResultsObject = CoreLogic::getObject('ElasticSearchResultsObject');
        $ElasticSearchResultsObject->load($response);

        return $ElasticSearchResultsObject;

    }

    /**
     * Get record by id
     *
     * @param null $index
     * @param null $type
     * @param null $id
     * @return ElasticSearchResultObject
     */
    public function get($index = null, $type = null, $id = null){

        /** @var array $response */
        $response = $this->ElasticSearchRepository->get($index, $type, $id);

        /** @var ElasticSearchResultsObject $ElasticSearchResultsObject */
        $ElasticSearchResultObject = CoreLogic::getObject('ElasticSearchResultObject');
        $ElasticSearchResultObject->load($response);

        return $ElasticSearchResultObject;

    }

    /**
     * Add new record
     *
     * @param null $index
     * @param null $type
     * @param null $data
     * @return ElasticSearchResultObject
     */
    public function add($index = null, $type = null, $data = null){

        /** @var array $response */
        $response = $this->ElasticSearchRepository->add($index, $type, $data);

        var_dump($response);

        if(isset($response) && isset($response->_id)) {
            return $this->get($index, $type, $response->_id);
        } else {
            CoreNotification::set('Unable to save to search index', CoreNotification::ERROR);
        }
        return null;

    }

}