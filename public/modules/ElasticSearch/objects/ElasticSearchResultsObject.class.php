<?php

class ElasticSearchResultsObject {

    /** @var bool $timeout */
    public $timeout;

    /** @var bool $valid */
    public $valid;

    /** @var array $results */
    public $results = array();

    /** @var int $duration */
    public $duration;

    /** @var int $size */
    public $size = 0;

    /** @var int $total */
    public $total;

    /**
     * Populate the object
     *
     * @param null $object
     */
    public function load($object = null){

        $object = CoreArrayUtils::objectToArray($object);

        $this->timeout = isset($object['timed_out']) ? (bool) $object['timed_out'] : false;
        $this->duration = isset($object['took']) ? (int) $object['took'] : false;
        $this->total = isset($object['hits']['total']) ? (int) $object['hits']['total'] : false;
        foreach($object['hits']['hits'] as $entry){
            /** @var ElasticSearchResultObject $ElasticSearchResultObject */
            $ElasticSearchResultObject = CoreLogic::getObject('ElasticSearchResultObject');
            $ElasticSearchResultObject->load($entry);
            array_push($this->results, $ElasticSearchResultObject);
            $this->size++;
        }

        $this->valid = !empty($this->total);

    }

    /**
     * @return boolean
     */
    public function isTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param boolean $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @param boolean $valid
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param array $results
     */
    public function setResults($results)
    {
        $this->results = $results;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

}