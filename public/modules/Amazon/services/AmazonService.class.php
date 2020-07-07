<?php

class AmazonService
{

    /** @var AmazonProcedure $AmazonProcedure */
    private $AmazonProcedure;

    function __construct()
    {
        $this->AmazonProcedure = CoreLogic::getProcedure('AmazonProcedure');
    }

    /**
     * Get search indexes
     *
     * @return array
     */
    public function getSearchIndexes()
    {
        $indexes = array();
        array_push($indexes, 'All');
        array_push($indexes, 'Baby');
        array_push($indexes, 'Beauty');
        array_push($indexes, 'Books');
        array_push($indexes, 'Music');
        array_push($indexes, 'FashionWomen');
        array_push($indexes, 'Fashion');
        array_push($indexes, 'FashionBaby');
        array_push($indexes, 'FashionBoys');
        array_push($indexes, 'FashionGirls');
        array_push($indexes, 'Toys');
        array_push($indexes, 'Electronics');
        array_push($indexes, 'GiftCards');
        array_push($indexes, 'Grocery');
        array_push($indexes, 'HealthPersonalCare');
        array_push($indexes, 'Luggage');
        array_push($indexes, 'Magazines');
        array_push($indexes, 'HomeGarden');
        array_push($indexes, 'ArtsAndCrafts');
        array_push($indexes, 'Movies');
        array_push($indexes, 'MusicalInstruments');
        array_push($indexes, 'Wine');
        array_push($indexes, 'KindleStore');
        array_push($indexes, 'LawnAndGarden');
        array_push($indexes, 'PetSupplies');
        array_push($indexes, 'Pantry');
        array_push($indexes, 'OfficeProducts');
        array_push($indexes, 'Appliances');
        array_push($indexes, 'FashionMen');
        array_push($indexes, 'UnboxVideo');
        array_push($indexes, 'MobileApps');
        array_push($indexes, 'Automotive');
        array_push($indexes, 'Wireless');
        array_push($indexes, 'Collectibles');
        array_push($indexes, 'PCHardware');
        array_push($indexes, 'MP3Downloads');
        array_push($indexes, 'Industrial');
        array_push($indexes, 'Software');
        array_push($indexes, 'SportingGoods');
        array_push($indexes, 'Tools');
        array_push($indexes, 'VideoGames');
        return array_unique($indexes);
    }

    /**
     * Search the amazon api
     *
     * @param $query
     * @param $page
     * @param $sort
     * @param $category
     * @param $tag
     * @return array|bool|mixed
     */
    public function search($query, $page, $sort = null, $category = null, $tag = null)
    {
        try {
            return $this->AmazonProcedure->search($query, $page, $sort, $category, $tag);
        } catch (AmazonConfigurationException $e) {
            CoreNotification::set('Unable to connect to the Amazon API. Info: ' . $e->getMessage(), CoreNotification::ERROR);
        } catch (UserUnauthorizedException $e) {
            CoreNotification::set('Unable to search Amazon, need valid user. Info: ' . $e->getMessage(), CoreNotification::ERROR);
        } catch (Exception $e) {
            CoreNotification::set('Unable to search Amazon due to unknown error. Info: ' . $e->getMessage(), CoreNotification::ERROR);
        }
        return false;
    }

    /**
     * Lookup product
     *
     * @param null $asin
     * @return array|bool
     */
    public function lookup($asin = null)
    {
        try {
            return $this->AmazonProcedure->lookup($asin);
        } catch (AmazonConfigurationException $e) {
            CoreNotification::set('Unable to connect to the Amazon API. Info: ' . $e->getMessage(), CoreNotification::ERROR);
        } catch (UserUnauthorizedException $e) {
            CoreNotification::set('Unable to search Amazon, need valid user. Info: ' . $e->getMessage(), CoreNotification::ERROR);
        } catch (Exception $e) {
            CoreNotification::set('Unable to lookup Amazon item due to unknown error. Info: ' . $e->getMessage(), CoreNotification::ERROR);
        }
        return false;
    }

}