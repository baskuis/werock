<?php

class MapTableListingsObject {

    /** @var array $listings */
    public $listings;

    /** @var CorePaginationObject $CorePaginationObject */
    public $CorePaginationObject;

    /**
     * @param \CorePaginationObject $CorePaginationObject
     */
    public function setCorePaginationObject($CorePaginationObject)
    {
        $this->CorePaginationObject = $CorePaginationObject;
    }

    /**
     * @return \CorePaginationObject
     */
    public function getCorePaginationObject()
    {
        return $this->CorePaginationObject;
    }

    /**
     * @param array $listings
     */
    public function setListings($listings)
    {
        $this->listings = $listings;
    }

    /**
     * @return array
     */
    public function getListings()
    {
        return $this->listings;
    }

}