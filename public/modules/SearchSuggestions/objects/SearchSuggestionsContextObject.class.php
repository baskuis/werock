<?php

class SearchSuggestionsContextObject {

    /** @var string $urn */
    public $urn;

    /** @var SearchSuggestionsSearchObject $SearchSuggestionsSearchObject */
    public $SearchSuggestionsSearchObject;

    /** @var string $typeaheadtemplate */
    public $typeaheadtemplate = 'searchsuggestionstypeahead';

    /** @var string $typeaheadoptiontemplate */
    public $typeaheadoptiontemplate = 'searchsuggestionstypeaheadoption';

    /** @var string $typeaheadcontainerid */
    public $typeaheadcontainerid;

    /** @var string $typeaheadinputid */
    public $typeaheadinputid;

    /** @var int $count Max results shown */
    public $count = 12;

    /** @var bool $autofocus */
    public $autofocus = false;

    /**
     * @return mixed
     */
    public function getUrn()
    {
        return $this->urn;
    }

    /**
     * @param mixed $urn
     */
    public function setUrn($urn)
    {
        $this->urn = $urn;
    }

    /**
     * @return SearchSuggestionsSearchObject
     */
    public function getSearchSuggestionsSearchObject()
    {
        return $this->SearchSuggestionsSearchObject;
    }

    /**
     * @param SearchSuggestionsSearchObject $SearchSuggestionsSearchObject
     */
    public function setSearchSuggestionsSearchObject(SearchSuggestionsSearchObject $SearchSuggestionsSearchObject)
    {
        $this->SearchSuggestionsSearchObject = $SearchSuggestionsSearchObject;
    }

    /**
     * @return string
     */
    public function getTypeaheadtemplate()
    {
        return $this->typeaheadtemplate;
    }

    /**
     * @param string $typeaheadtemplate
     */
    public function setTypeaheadtemplate($typeaheadtemplate)
    {
        $this->typeaheadtemplate = $typeaheadtemplate;
    }

    /**
     * @return string
     */
    public function getTypeaheadoptiontemplate()
    {
        return $this->typeaheadoptiontemplate;
    }

    /**
     * @param string $typeaheadoptiontemplate
     */
    public function setTypeaheadoptiontemplate($typeaheadoptiontemplate)
    {
        $this->typeaheadoptiontemplate = $typeaheadoptiontemplate;
    }

    /**
     * @return string
     */
    public function getTypeaheadcontainerid()
    {
        return $this->typeaheadcontainerid;
    }

    /**
     * @param string $typeaheadcontainerid
     */
    public function setTypeaheadcontainerid($typeaheadcontainerid)
    {
        $this->typeaheadcontainerid = $typeaheadcontainerid;
    }

    /**
     * @return string
     */
    public function getTypeaheadinputid()
    {
        return $this->typeaheadinputid;
    }

    /**
     * @param string $typeaheadinputid
     */
    public function setTypeaheadinputid($typeaheadinputid)
    {
        $this->typeaheadinputid = $typeaheadinputid;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return boolean
     */
    public function isAutofocus()
    {
        return $this->autofocus;
    }

    /**
     * @param boolean $autofocus
     */
    public function setAutofocus($autofocus)
    {
        $this->autofocus = $autofocus;
    }

}