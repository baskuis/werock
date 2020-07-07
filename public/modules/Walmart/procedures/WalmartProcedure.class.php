<?php

/**
 * Class WalmartProcedure
 *
 */
class WalmartProcedure {

    /** @var WalmartRepository $WalmartRepository */
    private $WalmartRepository;

    /** @var SearchSuggestionsService $SearchSuggestionsService */
    private $SearchSuggestionsService;

    const PAGE_SIZE = 10;

    function __construct(){
        $this->WalmartRepository = CoreLogic::getRepository('WalmartRepository');
        $this->SearchSuggestionsService = CoreLogic::getService('SearchSuggestionsService');
    }

    /**
     * Search
     *
     * @param null $query
     * @param int $page
     * @param null $sort
     * @param null $category
     * @param null $tag
     * @return array
     * @throws Exception
     */
    public function search($query = null, $page = 0, $sort = null, $category = null, $tag = null){

        $results = array();

        /**
         * Map some values which are used by amazon
         */
        switch($sort) {
            case('relevancerank'):
                $sort = 'relevance';
                break;
            case('price'):
                $sort = 'price';
                break;
            case('salesrank'):
                $sort = 'bestseller';
                break;
            case('reviewrank'):
                $sort = 'customerRating';
                break;
        }

        /**
         * Get results from api
         */
        $walmartResults = $this->WalmartRepository->searchItems($query, (($page - 1) * self::PAGE_SIZE), $sort, $category);

        /**
         * Map items
         */
        if(isset($walmartResults->items) && !empty($walmartResults->items)){
            foreach($walmartResults->items as $walmartResult){
                /** @var WalmartItemObject $WalmartItemObject */
                $WalmartItemObject = CoreLogic::getObject('WalmartItemObject');
                $WalmartItemObject->setUrl($this->stripOriginalUrl($walmartResult->productUrl));
                $WalmartItemObject->setTitle($walmartResult->name);
                $WalmartItemObject->setDescription($walmartResult->shortDescription);
                $WalmartItemObject->setPrice($walmartResult->salePrice);
                $WalmartItemObject->setImage($walmartResult->largeImage);
                $WalmartItemObject->setAsin($walmartResult->upc);
                array_push($results, $WalmartItemObject);
            }
        }

        /** @var SearchSuggestionsContextObject $SearchSuggestionsContextObject */
        $SearchSuggestionsContextObject = CoreLogic::getObject('SearchSuggestionsContextObject');
        if(!empty($tag)) {
            $SearchSuggestionsContextObject->setUrn(WalmartModule::WALMART_SUGGESTIONS_KEY . ':' . $tag);
        }else{
            $SearchSuggestionsContextObject->setUrn(WalmartModule::WALMART_SUGGESTIONS_KEY);
        }

        /** @var SearchSuggestionsSearchObject $SearchSuggestionsSearchObject */
        $SearchSuggestionsSearchObject = CoreLogic::getObject('SearchSuggestionsSearchObject');
        $SearchSuggestionsSearchObject->setSearch($query);
        $SearchSuggestionsSearchObject->setFound(!empty($results));
        $SearchSuggestionsContextObject->setSearchSuggestionsSearchObject($SearchSuggestionsSearchObject);
        $this->SearchSuggestionsService->saveSearch($SearchSuggestionsContextObject);

        return $results;

    }

    private function stripOriginalUrl($url = null){
        $url = urldecode(str_replace('http://c.affil.walmart.com/t/api02?l=', '', $url));
        return substr($url, 0, strrpos($url, '?'));
    }

    /**
     * Lookup
     *
     * @param null $upc
     * @return mixed|null
     * @throws Exception
     */
    public function lookup($upc = null){
        $result = null;
        $walmartResult = $this->WalmartRepository->upcLookup($upc);
        if(!empty($walmartResult)){
            $result = $walmartResult;
        }
        return $result;
    }

}