<?php

class SiteMapEntryObject {

    public $loc;
    public $lastmod;
    public $changefreq;
    public $priority;

    const ALWAYS = 'always';
    const HOURLY = 'hourly';
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';
    const YEARLY = 'yearly';
    const NEVER = 'never';

    /**
     * @return mixed
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @param mixed $loc
     */
    public function setLoc($loc)
    {
        $this->loc = $loc;
    }

    /**
     * @return mixed
     */
    public function getLastmod()
    {
        return $this->lastmod;
    }

    /**
     * Date last modified in 0000-00-00
     *
     * @param mixed $lastmod
     */
    public function setLastmod($lastmod)
    {
        $this->lastmod = $lastmod;
    }

    /**
     * @return mixed
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * @param mixed $changefreq
     */
    public function setChangefreq($changefreq)
    {
        $this->changefreq = $changefreq;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Priority number from 0-1
     *
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * Creates <url> block
     *
     * @return string
     */
    public function __toString()
    {
        return
            '<url>' . "\n" .
                (!empty($this->loc) ? '<loc>' . $this->loc . '</loc>' . "\n" : CoreStringUtils::EMPTY_STRING) .
                (!empty($this->lastmod) ? '<lastmod>' . date('Y-m-d', strtotime($this->lastmod)) . '</lastmod>' . "\n" : CoreStringUtils::EMPTY_STRING) .
                (!empty($this->changefreq) ? '<changefreq>' . $this->changefreq . '</changefreq>' . "\n" : CoreStringUtils::EMPTY_STRING) .
                (!empty($this->priority) ? '<priority>' . number_format($this->priority, 2) . '</priority>' . "\n" : CoreStringUtils::EMPTY_STRING) .
	        '</url>' . "\n";
    }


}