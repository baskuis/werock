<?php

/**
 * Elastic search simple client
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class ElasticSearchClient {

    public $index;

    function __construct($server = 'http://localhost:9200'){
        $this->server = $server;
    }

    function call($path, $http = array()){
        $http['header'] = 'Content-Type: application/json' . "\r\n";
        $elPath = !empty($this->index) ? $this->server . '/' . $this->index . '/' . $path : $this->server . '/' . $path;
        return json_decode(file_get_contents($elPath, NULL, stream_context_create(array('http' => $http))));
    }

    function create(){
        $this->call(NULL, array('method' => 'PUT'));
    }

    function drop(){
        $this->call(NULL, array('method' => 'DELETE'));
    }

    function status(){
        return $this->call('_status');
    }

    function get($type, $id){
        return $this->call($type . '/' . $id, array('method' => 'GET'));
    }

    function count($type){
        return $this->call($type . '/_count', array('method' => 'GET', 'content' => '{ matchAll:{} }'));
    }

    function search($type, $payload = array()){
        return $this->call($type . '/_search', array('method' => 'GET', 'content' => json_encode($payload)));
    }

    function map($type, $data){
        return $this->call($type . '/_mapping', array('method' => 'PUT', 'content' => $data));
    }

    function add($type, $data){
        return $this->call($type . '/?_create', array('method' => 'POST', 'content' => json_encode($data)));
    }

    function query($type, $options = array()){
        return $this->call($type . '/_search?' . http_build_query($options));
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

}