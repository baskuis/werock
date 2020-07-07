<?php

class ElasticSearchResultObject {

    /** @var string $id */
    public $id;

    /** @var float $score */
    public $score;

    /** @var int $sort */
    public $sort;

    /** @var string $index */
    public $index;

    /** @var string $type */
    public $type;

    /** @var object $object */
    public $object;

    /** @var bool $valid */
    public $valid;

    /**
     * Populate object
     *
     * @param null $object
     */
    public function load($object = null){

        $object = CoreArrayUtils::objectToArray($object);

        $this->id = isset($object['_id']) ? $object['_id'] : null;
        $this->score = isset($object['_score']) ? $object['_score'] : null;
        $this->sort = isset($object['sort']) ? $object['sort'] : null;
        $this->index = isset($object['_index']) ? $object['_index'] : null;
        $this->type = isset($object['_type']) ? $object['_type'] : null;
        $this->object = isset($object['_source']) ? $object['_source'] : null;

        $this->valid = (!empty($this->id));

    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return float
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param float $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param string $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param object $object
     */
    public function setObject($object)
    {
        $this->object = $object;
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

}