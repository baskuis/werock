<?php

/**
 * SiteMap Service
 * keeps track of - and builds sitemaps and a sitemap index
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class SiteMapService {

    /** @var array $sitemaps */
    public $sitemaps = array();

    const SITEMAP_INDEX_CACHE_KEY = 'sitemaps.index';

    /** @var SiteMapIndexObject $SiteMapIndexObject */
    public $SiteMapIndexObject;

    /**
     * Render sitemap index
     *
     * @return string
     */
    public function renderSiteMapIndex(){
        $this->buildSiteMaps();
        return $this->SiteMapIndexObject->__toString();
    }

    /**
     * Render sitemap
     *
     * @param null $name
     * @return bool|string
     */
    public function renderSiteMap($name = null){
        $this->buildSiteMaps();
        $buildSiteMaps = $this->SiteMapIndexObject->getSitemaps();
        if(!empty($buildSiteMaps)){
            /** @var SiteMapObject $SiteMapObject */
            foreach($buildSiteMaps as $SiteMapObject){
                if(CoreStringUtils::compare($name, $SiteMapObject->getName())){
                    return $SiteMapObject->__toString();
                }
            }
        }
        return false;
    }

    /**
     * Build sitemaps
     * and sitemap index
     *
     */
    public function buildSiteMaps(){
        /** attempt to restore from cache */
        if(false === ($this->SiteMapIndexObject = CoreCache::getCache(self::SITEMAP_INDEX_CACHE_KEY, true))) {
            if (!empty($this->sitemaps)) {

                /** @var SiteMapIndexObject $SiteMapIndexObject */
                $this->SiteMapIndexObject = CoreLogic::getObject('SiteMapIndexObject');

                /** @var SiteMapObject $siteMapObject */
                foreach ($this->sitemaps as $siteMapObject) {
                    $siteMapObject->build();
                    $this->SiteMapIndexObject->addSitemap($siteMapObject);
                }
            }
            CoreCache::saveCache(self::SITEMAP_INDEX_CACHE_KEY, $this->SiteMapIndexObject, 600, true);
        }
    }

    /**
     * Register a sitemap
     *
     * @param SiteMapObject $siteMapObject
     */
    public function addSiteMap(SiteMapObject $siteMapObject){
        array_push($this->sitemaps, $siteMapObject);
    }

}