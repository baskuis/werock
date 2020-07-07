<?php

class SiteMapIndexObject {

    const SITEMAP_INDEX = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    public $sitemaps = array();

    /**
     * @return array
     */
    public function getSitemaps()
    {
        return $this->sitemaps;
    }

    /**
     * @param array $sitemaps
     */
    public function setSitemaps($sitemaps)
    {
        $this->sitemaps = $sitemaps;
    }

    /**
     * Add sitemap
     *
     * @param SiteMapObject $siteMapObject
     */
    public function addSitemap(SiteMapObject $siteMapObject){
        array_push($this->sitemaps, $siteMapObject);
    }

    public function __toString()
    {
        $output =
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<sitemapindex xmlns="' . self::SITEMAP_INDEX . '">' . "\n";
        foreach($this->sitemaps as $sitemap){
            $output .= $sitemap->__toStringIndexEntry();
        }
        $output .=
            '</sitemapindex>';
        return $output;
    }

}