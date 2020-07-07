<?php

/**
 * Search suggestions procedure
 *
 * Class SearchSuggestionsProcedure
 */
class SearchSuggestionsProcedure {

    /** @var SearchSuggestionsRepository $SearchSuggestionsRepository */
    private $SearchSuggestionsRepository;

    function __construct(){
        $this->SearchSuggestionsRepository = CoreLogic::getRepository('SearchSuggestionsRepository');
    }

}