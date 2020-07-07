<?php

/**
 * ElasticSearch repository
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class ElasticSearchRepository {

    /** @var ElasticSearchClient $ElasticSearchClient */
    public $ElasticSearchClient;

    private function init(){
        if(!$this->ElasticSearchClient) {
            require 'lib/ElasticSearchClient.php';
            $this->ElasticSearchClient = new ElasticSearchClient(CoreModule::getProp('ElasticSearchModule', 'elasticsearch:hosts', 'http://localhost:9200'));
        }
    }

    public function simpleSearch($index = null, $type = null, $query = null, $options = array()){
        self::init();
        $this->ElasticSearchClient->setIndex($index);
        $noptions = array();
        $noptions['q'] = $query;
        $noptions['from'] = isset($options['from']) ? (int) $options['from'] : 0;
        $noptions['size'] = isset($options['size']) ? (int) $options['size'] : 12;
        $noptions['sort'] = isset($options['sort']) ? $options['sort'] : 'fr:desc';
        return $this->ElasticSearchClient->query($type, $noptions);
    }

    public function get($index = null, $type = null, $id = null){
        self::init();
        $this->ElasticSearchClient->setIndex($index);
        return $this->ElasticSearchClient->get($type, $id);
    }

    public function add($index = null, $type = null, $data = array()) {
        self::init();
        $this->ElasticSearchClient->setIndex($index);
        return $this->ElasticSearchClient->add($type, $data);
    }

    public function search(ElasticSearchQueryObject $ElasticSearchQueryObject){
        self::init();
        $this->ElasticSearchClient->setIndex($ElasticSearchQueryObject->getIndex());
        return $this->ElasticSearchClient->search($ElasticSearchQueryObject->getType(), $ElasticSearchQueryObject->getPayload());
    }

    public function rawSearch($index = null, $type = null, $payload = array()){
        self::init();
        $this->ElasticSearchClient->setIndex($index);
        return $this->ElasticSearchClient->search($type, $payload);
    }

    public function status(){
        self::init();
        return $this->ElasticSearchClient->status();
    }

}