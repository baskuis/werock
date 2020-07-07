<?php

class SiteMapObject {

    const SITEMAP_SCHEMA = 'http://www.sitemaps.org/schemas/sitemap/0.9';
    const SITEMAP_LAST_MOD_DEFAULT = '2016-12-15T19:20:30+01:00';

    /** @var string $name */
    public $name;

    /** @var string $url */
    public $url;

    /** @var string $lastmod 0000-00-00 */
    public $lastmod = self::SITEMAP_LAST_MOD_DEFAULT;

    /** @var array $urls of type SiteMapEntryObject */
    public $urls = array();

    /** @var callable $builder */
    public $builder;

    /**
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name){
        $this->name = $name;
        $this->url = HTTP_PROTOCOL . DOMAIN_NAME . '/sitemaps/' . CoreStringUtils::url($this->name) . '.xml';
    }

    /**
     * @return mixed
     */
    public function getUrl(){
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getLastmod(){
        return $this->lastmod;
    }

    /**
     * @param mixed $lastmod
     */
    public function setLastmod($lastmod){
        $this->lastmod = $lastmod;
    }

    /**
     * @return array
     */
    public function getUrls(){
        usort($this->urls, function(SiteMapEntryObject $a, SiteMapEntryObject $b){
            if($a->priority == $b->priority) return 0;
            if($a->priority > $b->priority) return 1;
            return -1;
        });
        return $this->urls;
    }

    /**
     * @param array $urls
     */
    public function setUrls($urls){
        $this->urls = $urls;
    }

    /**
     * Add url to sitemap
     *
     * @param SiteMapEntryObject $siteMapEntryObject
     */
    public function addUrl(SiteMapEntryObject $siteMapEntryObject){
        array_push($this->urls, $siteMapEntryObject);
    }

    /**
     * Return sitemap index entry as string
     *
     * @return string
     */
    public function __toStringIndexEntry(){
        return
                '<sitemap>' . "\n" .
                '<loc>' . $this->url . '</loc>' . "\n" .
                '<lastmod>' . $this->lastmod . '</lastmod>' . "\n" .
                '</sitemap>';
    }

    /**
     * Return sitemap as string
     *
     * @return string
     */
    public function __toString(){
        return
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<urlset xmlns="' . self::SITEMAP_SCHEMA . '">' . "\n" .
                implode($this->urls) .
            '</urlset>' . "\n";

    }

    /**
     * @param callable $builder
     */
    public function setBuilder($builder){
        $this->builder = $builder;
    }

    /**
     * SiteMap builder
     */
    public function build(){
        if(!is_callable($this->builder)){
            CoreLog::error('No builder set for sitemap');
        }
        $this->urls = call_user_func($this->builder);
        unset($this->builder); //allows for caching of this object
    }

}