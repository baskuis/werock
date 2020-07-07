<?php

class SearchSuggestionsSearchObject {

    /** @var string $search */
    public $search;

    /** @var bool $found */
    public $found;

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param mixed $search
     */
    public function setSearch($search)
    {
        $this->search = $search;
    }

    /**
     * @return bool
     */
    public function getFound()
    {
        return $this->found;
    }

    /**
     * @param bool $found
     */
    public function setFound($found)
    {
        $this->found = $found;
    }

}