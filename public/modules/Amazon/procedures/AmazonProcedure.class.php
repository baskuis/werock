<?php

/**
 * Amazon API interaction procedures
 *
 * Class AmazonProcedure
 */
class AmazonProcedure
{

    /** @var AmazonRepository $AmazonRepository */
    private $AmazonRepository;

    /** @var SearchSuggestionsService $SearchSuggestionsService */
    private $SearchSuggestionsService;

    /** @var UserService $UserService */
    private $UserService;

    function __construct()
    {
        $this->AmazonRepository = CoreLogic::getRepository('AmazonRepository');
        $this->SearchSuggestionsService = CoreLogic::getService('SearchSuggestionsService');
        $this->UserService = CoreLogic::getService('UserService');
    }

    /**
     * Search amazon
     *
     * @param $query
     * @param $page
     * @param $sort
     * @param $category
     * @param $tag
     * @return array
     * @throws AmazonApiException
     * @throws AmazonConfigurationException
     * @throws UserUnauthorizedException
     */
    public function search($query, $page, $sort, $category, $tag){

        /** Need to be logged in */
        if (!$this->UserService->activeUser()) {
            throw new UserUnauthorizedException('Login required');
        }

        /**
         * Search two pages
         */
        $results = $this->AmazonRepository->search($query, $page, $sort, $category);

        /** @var SearchSuggestionsContextObject $SearchSuggestionsContextObject */
        $SearchSuggestionsContextObject = CoreLogic::getObject('SearchSuggestionsContextObject');
        if (!empty($tag)) {
            $SearchSuggestionsContextObject->setUrn(AmazonModule::AMAZON_SUGGESTIONS_KEY . ':' . $tag);
        } else {
            $SearchSuggestionsContextObject->setUrn(AmazonModule::AMAZON_SUGGESTIONS_KEY);
        }

        /** @var SearchSuggestionsSearchObject $SearchSuggestionsSearchObject */
        $SearchSuggestionsSearchObject = CoreLogic::getObject('SearchSuggestionsSearchObject');
        $SearchSuggestionsSearchObject->setSearch($query);
        $SearchSuggestionsSearchObject->setFound(!empty($results));
        $SearchSuggestionsContextObject->setSearchSuggestionsSearchObject($SearchSuggestionsSearchObject);
        $this->SearchSuggestionsService->saveSearch($SearchSuggestionsContextObject);

        return $results;

    }

    /**
     * Lookup by ASIN
     *
     * @param null $ASIN
     * @return array
     * @throws AmazonApiException
     * @throws AmazonConfigurationException
     * @throws UserUnauthorizedException
     */
    public function lookup($ASIN = null)
    {

        /** Need to be logged in */
        if (!$this->UserService->activeUser()) {
            throw new UserUnauthorizedException('Login required');
        }

        $product = $this->AmazonRepository->lookup($ASIN);
        return $product;

    }

}