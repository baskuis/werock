<?php

class MessageFilterObject {

    public $limit = 20;
    public $start = 0;
    public $search = null;
    public $tags = array();

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return (int) $this->limit;
    }

    /**
     * @param null $search
     */
    public function setSearch($search)
    {
        $this->search = $search;
    }

    /**
     * @return null
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param int $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return (int) $this->start;
    }

    /**
     * @param array $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash(){
        return md5(__CLASS__ . $this);
    }

}