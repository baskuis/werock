<?php

class IntelligenceDataRequestObject {

    public $key;
    public $from;
    public $to;
    public $interval;

    /**
     * @var bool $crawler
     */
    public $crawler;

    /** @var int $limit */
    public $limit = 10;

    /**
     * @param mixed $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $interval
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;
    }

    /**
     * @return mixed
     */
    public function getInterval()
    {
        if(!$this->interval){
            $this->interval = $this->to - $this->from;
        }
        return $this->interval;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return boolean
     */
    public function isCrawler()
    {
        return $this->crawler;
    }

    /**
     * @param boolean $crawler
     */
    public function setCrawler($crawler)
    {
        $this->crawler = $crawler;
    }

}