<?php

/**
 * ElasticSearch Manager
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class ElasticSearchService implements ElasticSearchServiceInterface {

    /** @var ElasticSearchProcedure $ElasticSearchProcedure */
    private $ElasticSearchProcedure;

    private function build(){

        if(empty($this->ElasticSearchProcedure)) {
            $this->ElasticSearchProcedure = CoreLogic::getProcedure('ElasticSearchProcedure');
        }

    }

    public function status(){

        self::build();

        return $this->ElasticSearchProcedure->status();

    }

    public function rawSearch($index = null, $type = null, $payload = array()){

        self::build();

        return $this->ElasticSearchProcedure->rawSearch($index, $type, $payload);

    }

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
    public function simpleSearch($index = null, $type = null, $query = null, $options = array()){

        self::build();

        return $this->ElasticSearchProcedure->simpleSearch($index, $type, $query, $options);

    }

    public function search(ElasticSearchQueryObject $ElasticSearchQueryObject){

        self::build();

        return $this->ElasticSearchProcedure->search($ElasticSearchQueryObject);

    }

    public function get($index = null, $type = null, $id = null){

        self::build();

        return $this->ElasticSearchProcedure->get($index, $type, $id);

    }

    public function add($index = null, $type = null, $data = null){

        self::build();

        return $this->ElasticSearchProcedure->add($index, $type, $data);

    }

}