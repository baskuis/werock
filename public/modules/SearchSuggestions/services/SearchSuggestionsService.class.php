<?php

/**
 * Search suggestions service
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class SearchSuggestionsService implements SearchSuggestionsServiceInterface {

    /**
     * Cache
     */
    const CACHE_SEARCH_SUGGESTIONS_KEY = 'searchSuggestions';
    const CACHE_RECENT_KEY = '::recent';
    const CACHE_POPULAR_KEY = '::popular';
    const CACHE_ONE_DAY = 86400;
    const CACHE_RELATED_KEY = '::related';
    const CACHE_RELATED_NAMESPACE = 'related';
    
    /**
     * Constants
     */
    const CONST_TEXT = 'text';
    const CONST_FOUND = 'found';
    const CONST_COUNT = 'count';
    const CONST_WRAPPER_PREPEND = 'wrapper_for_';
    const CONST_SEARCH_SUGGESTIONS_OBJECT = 'SearchSuggestionsSuggestionObject';
    const CONST_SEARCH_SUGGESTIONS_CONTEXT_OBJECT = 'SearchSuggestionsContextObject';
    
    /** @var SearchSuggestionsRepository $SearchSuggestionsRepository */
    private $SearchSuggestionsRepository;

    public function __construct(){
        if(empty($this->SearchSuggestionsRepository)){
            $this->SearchSuggestionsRepository = CoreLogic::getRepository('SearchSuggestionsRepository');
        }
    }

    /**
     * Build search suggestions template
     *
     * @param SearchSuggestionsContextObject $searchSuggestionsContextObject
     * @return mixed
     */
    public function build(SearchSuggestionsContextObject $searchSuggestionsContextObject){
        return CoreTemplate::render($searchSuggestionsContextObject->getTypeaheadtemplate(), $searchSuggestionsContextObject);
    }

    /**
     * From context
     *
     * @param FormField $formField
     * @param null $urn
     * @return SearchSuggestionsContextObject
     */
    public function fromFormField(FormField $formField, $urn = null){

        /** assertion */
        if(empty($urn)) CoreLog::error('Need urn to create SearchSuggestionsContextObject');

        /** @var SearchSuggestionsContextObject $SearchSuggestionsContextObject */
        $SearchSuggestionsContextObject = CoreLogic::getObject(self::CONST_SEARCH_SUGGESTIONS_CONTEXT_OBJECT);

        /** build search suggestions context */
        $SearchSuggestionsContextObject->setUrn($urn);
        $SearchSuggestionsContextObject->setTypeaheadcontainerid(self::CONST_WRAPPER_PREPEND . $formField->getName());
        $SearchSuggestionsContextObject->setTypeaheadinputid($formField->getName());

        return $SearchSuggestionsContextObject;

    }

    /**
     * Save search
     *
     * @param SearchSuggestionsContextObject $searchSuggestionsContextObject
     */
    public function saveSearch(SearchSuggestionsContextObject $searchSuggestionsContextObject){

        /**
         * Run assertions
         */
        try {
            if(
                is_numeric($searchSuggestionsContextObject->getSearchSuggestionsSearchObject()->getSearch()) ||
                $searchSuggestionsContextObject->getSearchSuggestionsSearchObject()->getSearch() == '' ||
                strlen($searchSuggestionsContextObject->getSearchSuggestionsSearchObject()->getSearch()) < 3
            ){
                return;
            }
        } catch(Exception $e){
            CoreLog::debug('Unable to assert valid search string');
        }

        /** search suggestions DAO */
        $this->SearchSuggestionsRepository->insertSearch($searchSuggestionsContextObject);

    }

    /**
     * Get popular suggestions
     *
     * @param null $urn
     * @param int $limit
     * @return array
     */
    public function popular($urn = null, $limit = 12){

        /**
         * Generating cache key
         */
        $cacheKey = self::CACHE_SEARCH_SUGGESTIONS_KEY . self::CACHE_POPULAR_KEY . $urn . $limit;

        /**
         * return cached value
         */
        if(!empty($list = CoreCache::getCache($cacheKey, true, null, false))) {
            return $list;
        }

        /** @var array $suggestions */
        if(!empty($urn)){
            $suggestions = $this->SearchSuggestionsRepository->getPopularSuggestionsByUrn($urn, $limit);
        } else {
            $suggestions = $this->SearchSuggestionsRepository->getPopularSuggestions($limit);
        }

        /** @var array $list */
        $list = array();

        /** handle suggestions */
        if(!empty($suggestions)) {
            foreach ($suggestions as $suggestion) {

                if (!isset($suggestion[self::CONST_TEXT])) continue;
                if (!isset($suggestion[self::CONST_COUNT])) continue;

                /** @var SearchSuggestionsSuggestionObject $SearchSuggestionsSuggestionObject */
                $SearchSuggestionsSuggestionObject = CoreLogic::getObject(self::CONST_SEARCH_SUGGESTIONS_OBJECT);
                $SearchSuggestionsSuggestionObject->setText(strtolower($suggestion[self::CONST_TEXT]));
                $SearchSuggestionsSuggestionObject->setCount($suggestion[self::CONST_COUNT]);

                array_push($list, $SearchSuggestionsSuggestionObject);

            }
        }

        /**
         * Cache list
         */
        CoreCache::saveCache($cacheKey, $list, self::CACHE_ONE_DAY, true, null, false);

        return $list;

    }

    /**
     * Get recent searches
     *
     * @param null $urn
     * @param int $limit
     * @return array
     */
    public function recent($urn = null, $limit = 12){

        /**
         * Generating cache key
         */
        $cacheKey = self::CACHE_SEARCH_SUGGESTIONS_KEY . self::CACHE_RECENT_KEY . $urn . $limit;

        /**
         * return cached value
         */
        if(false !== ($list = CoreCache::getCache($cacheKey, true, null, false))) {
            return $list;
        }

        /** @var array $searches */
        if(!empty($urn)) {
            $searches = $this->SearchSuggestionsRepository->getRecentSearchesByUrn($urn, $limit);
        } else {
            $searches = $this->SearchSuggestionsRepository->getRecentSearches($limit);
        }

        /** @var array $list */
        $list = array();

        /** handle suggestions */
        if(!empty($searches)) {
            foreach ($searches as $search) {

                if (!isset($search[self::CONST_TEXT])) continue;
                if (!isset($search[self::CONST_COUNT])) continue;

                /** @var SearchSuggestionsSuggestionObject $SearchSuggestionsSuggestionObject */
                $SearchSuggestionsSuggestionObject = CoreLogic::getObject(self::CONST_SEARCH_SUGGESTIONS_OBJECT);
                $SearchSuggestionsSuggestionObject->setText(strtolower($search[self::CONST_TEXT]));
                $SearchSuggestionsSuggestionObject->setCount($search[self::CONST_COUNT]);

                array_push($list, $SearchSuggestionsSuggestionObject);

            }
        }

        /**
         * Cache list
         */
        CoreCache::saveCache($cacheKey, $list, self::CACHE_ONE_DAY, true, null, false);

        return $list;

    }

    /**
     * Suggest
     *
     * @param string $text
     * @param string $urn
     * @param int $limit
     * @return array
     */
    public function suggest($text = null, $urn = null, $limit = 8){

        //append spelling suggestions
        $text = $this->appendSpellingSuggestions($text);

        $cacheKey = self::CACHE_SEARCH_SUGGESTIONS_KEY . $text . $urn . $limit;

        /**
         * Get personalized suggestions
         */
        $rows = $this->SearchSuggestionsRepository->getPersonalizedSuggestions($text, $urn, CoreUser::getId(), CoreVisitor::getId(), 5);
        $personalized = self::handleSuggestions($rows, $text, true);

        /**
         * Get generic suggestions
         */
        if(false === $generic = CoreCache::getCache($cacheKey, true, null, false)){
            $generic = !empty($text) ? self::related($text, $urn, $limit, false) : array();
            CoreCache::saveCache($cacheKey, $generic, self::CACHE_ONE_DAY, true, null, false);
        }

        /**
         * Only show 2 personalized results
         * if we have generic results
         *
         * otherwise show more personalized searches
         */
        if(!empty($generic)){
            $personalized = array_splice($personalized, 0, 2);
        }

        /**
         * Merge results
         *
         * enforce uniqueness
         */
        $suggestions = array();
        if(!empty($personalized)){
            foreach($personalized as $p){
                $suggestions[] = $p;
            }
        }
        if(!empty($generic)){
            /** @var SearchSuggestionsSuggestionObject $g */
            foreach($generic as &$g){
                $found = false;
                if(!empty($suggestions)){
                    /** @var SearchSuggestionsSuggestionObject $s */
                    foreach($suggestions as &$s){
                        if($s->getText() == $g->getText()) $found = true;
                    }
                }
                if(!$found) $suggestions[] = $g;
            }
        }

        $suggestions = (sizeof($suggestions) > $limit) ? array_splice($suggestions, 0, $limit) : $suggestions;

        return $suggestions;

    }

    /**
     * Handle suggestions
     *
     * @param array $suggestions
     * @param null $text
     * @param bool $personalized
     * @return array
     */
    private function handleSuggestions($suggestions = array(), $text = null, $personalized = false){

        /** @var array $list */
        $list = array();

        /** handle suggestions */
        if(!empty($suggestions)){
            foreach($suggestions as $suggestion){

                if(!isset($suggestion[self::CONST_TEXT])) continue;
                if(!isset($suggestion[self::CONST_COUNT])) continue;
                if(!isset($suggestion[self::CONST_FOUND])) continue;

                /** @var SearchSuggestionsSuggestionObject $SearchSuggestionsSuggestionObject */
                $SearchSuggestionsSuggestionObject = CoreLogic::getObject(self::CONST_SEARCH_SUGGESTIONS_OBJECT);
                $SearchSuggestionsSuggestionObject->setText(strtolower($suggestion[self::CONST_TEXT]));
                $SearchSuggestionsSuggestionObject->setCount($suggestion[self::CONST_COUNT]);
                $SearchSuggestionsSuggestionObject->setFound(CoreStringUtils::evaluateBoolean($suggestion[self::CONST_FOUND]));
                $SearchSuggestionsSuggestionObject->setSearch($text);
                $SearchSuggestionsSuggestionObject->setPersonalized($personalized);

                array_push($list, $SearchSuggestionsSuggestionObject);

            }
        }

        return $list;

    }

    /**
     * Sort suggestions
     *
     * @param array $suggestions
     * @return bool
     */
    private function sortSuggestions($suggestions = array()){
        usort($suggestions, function (SearchSuggestionsSuggestionObject $a, SearchSuggestionsSuggestionObject $b){
            return strcmp($a->getCount(), $b->getCount());
        });
        return $suggestions;
    }

    /**
     * Append spelling suggestions to broaden search
     *
     * @param $query
     * @return string
     */
    private function appendSpellingSuggestions($query){
        $words = preg_split ("/\\s+/", $query);
        $pl = pspell_new(CoreLanguage::$language);
        foreach($words as $word) {
            if (!pspell_check($pl, $word)) {
                $suggestions = pspell_suggest($pl, $word);
                if(sizeof($suggestions) > 4) array_splice($suggestions, 4);
                foreach ($suggestions as $suggestion) {
                    if(strpos($suggestion, '\'') > 0 || strpos($suggestion, ' ') > 0 || strpos($suggestion, '-')) continue; //skip
                    $query .= ' ' . $suggestion;
                }
            }
        }
        return $query;
    }

    /**
     * Related terms
     *
     * @param string $text
     * @param string $urn
     * @param int $requested
     * @param boolean $strip
     *
     * @return array
     */
    public function related($text = null, $urn = null, $requested = 8, $strip = true){

        /** assertion */
        if(empty($text)) CoreLog::error('Need text string to get suggestions');
        $cacheKey = self::CACHE_SEARCH_SUGGESTIONS_KEY . self::CACHE_RELATED_KEY . $text . ':' . $urn . ':' . $requested . ':' . $strip;
        if(false === ($related = CoreCache::getCache($cacheKey, true, self::CACHE_RELATED_NAMESPACE))) {

            /** @var array $related */
            $related = array();

            /**
             * Keep reducing until satisfied
             */
            $words = explode(' ', $text);
            usort($words, function ($a, $b) {
                return strlen($b) - strlen($a);
            });
            for ($i = sizeof($words); $i >= 0; $i--) {

                /** @var string $query */
                $query = implode(' ', array_slice($words, 0, $i));

                /** stop here */
                if (empty($query)) break;

                /** @var array $rows */
                if ($urn == null) {
                    $suggestions = $this->SearchSuggestionsRepository->getFuzzySuggestionsGlobal($query);
                } else {
                    $suggestions = $this->SearchSuggestionsRepository->getFuzzySuggestions($query, $urn);
                }

                /** add the set $related */
                $related = array_merge($related, self::handleSuggestions($suggestions, $query));

                /** unique array $related */
                $related = array_unique($related);

                /** @var SearchSuggestionsSuggestionObject $item */
                if ($strip) {
                    foreach ($related as $key => &$item) {
                        if (strtolower($item->getOriginal()) == strtolower($text)) {
                            unset($related[$key]);
                        }
                    }
                }

                /** no need to keep looking */
                if (sizeof($related) > $related) break;

            }

            /**
             * Return requested number of related searches
             */
            $related = (sizeof($related) > $requested) ? array_splice($related, 0, $requested) : $related;
            CoreCache::saveCache($cacheKey, $related, 7200, true, self::CACHE_RELATED_NAMESPACE);
        }
        return $related;
    }

}