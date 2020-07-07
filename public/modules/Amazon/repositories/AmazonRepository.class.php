<?php

/**
 * Class AmazonRepository
 * Handles connecting and retrieving data from the amazon API
 *
 */
class AmazonRepository {

    const AMAZON_ALL_CATEGORIES = 'All';
    const AMAZON_APAIIO_AUTOLOADER = 'autoloader.php';
    private $AMAZON_RESPONSE_GROUPS = array('Large', 'Small');

    /** @var bool $amazonLibraryLoaded */
    private static $amazonLibraryLoaded = false;

    /** @var bool $amazonConfLoaded */
    private static $amazonConfLoaded = false;

    /** @var ApaiIO\Configuration\GenericConfiguration $GenericConfiguration  */
    private $GenericConfiguration;

    /** @var ApaiIO\ApaiIO $ApaiIO */
    private $ApaiIO;

    /**
     * Construct
     *
     */
    function __construct(){
        self::loadLib();
    }

    /**
     * Load lib
     * Register the auto-loader
     *
     */
    private function loadLib(){
        if(!self::$amazonLibraryLoaded)
            require __DIR__ . CoreFilesystemUtils::SLASH . 'lib' . CoreFilesystemUtils::SLASH . self::AMAZON_APAIIO_AUTOLOADER;
        self::$amazonLibraryLoaded = true;
    }

    /**
     * Load amazon configuration
     * Initialize Amazon partner configuration
     *
     * @throws AmazonConfigurationException
     */
    private function instantiateApi(){
        if(!self::$amazonConfLoaded) {
            try {
                $this->GenericConfiguration = new ApaiIO\Configuration\GenericConfiguration();
                $this->GenericConfiguration
                    ->setCountry(CoreModule::getProp('AmazonModule', AmazonModule::AMAZON_SETTINGS_COUNTRY_KEY, ''))
                    ->setAccessKey(CoreModule::getProp('AmazonModule', AmazonModule::AMAZON_SETTINGS_ACCESS_KEY_KEY, ''))
                    ->setSecretKey(CoreModule::getProp('AmazonModule', AmazonModule::AMAZON_SETTINGS_SECRET_KEY_KEY, ''))
                    ->setAssociateTag(CoreModule::getProp('AmazonModule', AmazonModule::AMAZON_SETTINGS_ASSOCIATE_TAG_KEY, ''));
                $this->ApaiIO = new ApaiIO\ApaiIO($this->GenericConfiguration);
            } catch(Exception $e){
                throw new AmazonConfigurationException($e);
            }
        }
        self::$amazonConfLoaded = true;
    }

    /**
     * Lookup single product
     *
     * @param null $ASIN
     * @return array
     * @throws AmazonApiException
     * @throws AmazonConfigurationException
     */
    public function lookup($ASIN = null){

        self::instantiateApi();

        /** @var Lookup $Lookup */
        $Lookup = new ApaiIO\Operations\Lookup();
        $Lookup->setItemId($ASIN);
        $Lookup->setIdType('ASIN');
        $Lookup->setResponseGroup('Large');

        /**
         * Run the search operation
         */
        $formattedResponse = null;
        try {
            $formattedResponse = $this->ApaiIO->runOperation($Lookup);
        } catch(Exception $e){
            throw new AmazonApiException($e);
        }

        /**
         * Build the response
         */
        $response = array();
        $xml = simplexml_load_string($formattedResponse);

        if(isset($xml->Items->Item)) {
            foreach ($xml->Items->Item as $item) {

                /**
                 * Skip when image not present
                 */
                if(!isset($item->LargeImage->URL)) continue;

                /** add to response */
                array_push($response, self::mapItem($item));

                break;
            }
        }
        return $response;

    }

    /**
     * Search for amazon products
     *
     * @param null $query
     * @param int $page
     * @param string $sort
     * @param string $category
     * @return array
     * @throws AmazonApiException
     * @throws AmazonConfigurationException
     */
    public function search($query = null, $page = 1, $sort = 'relevancerank', $category = self::AMAZON_ALL_CATEGORIES){

        /**
         * Instantiate the Amazon API
         */
        self::instantiateApi();

        if(empty($category)){
            $category = self::AMAZON_ALL_CATEGORIES;
        }

        /**
         * Build the search operation
         */
        $Search = new ApaiIO\Operations\Search();
        $Search->setCategory($category);
        $Search->setKeywords($query);
        $Search->setPage($page);
        $Search->setResponseGroup($this->AMAZON_RESPONSE_GROUPS); //Large needed for product images
        if($category != self::AMAZON_ALL_CATEGORIES && $category != 'Blended') $Search->setSort($sort);

        /**
         * Run the search operation
         */
        $formattedResponse = null;
        try {
            $formattedResponse = $this->ApaiIO->runOperation($Search);
        } catch(Exception $e){
            throw new AmazonApiException($e);
        }

        /**
         * Build the response
         */
        $response = array();
        $xml = simplexml_load_string($formattedResponse);

        if(isset($xml->Items->Request->Errors->Error)){
            $Search = new ApaiIO\Operations\Search();
            $Search->setCategory(self::AMAZON_ALL_CATEGORIES);
            $Search->setKeywords($query);
            $Search->setPage($page);
            $Search->setResponseGroup($this->AMAZON_RESPONSE_GROUPS);
            $formattedResponse = null;
            try {
                $formattedResponse = $this->ApaiIO->runOperation($Search);
            } catch(Exception $e){
                throw new AmazonApiException($e);
            }
            $xml = simplexml_load_string($formattedResponse);
            if(isset($xml->Items->Request->Errors->Error)){
                throw new AmazonApiException($xml->Items->Request->Errors->Error->Message->__toString());
            }
        }

        if(isset($xml->Items->Item)) {
            foreach ($xml->Items->Item as $item) {

                /**
                 * Skip when image not present
                 */
                if(!isset($item->LargeImage->URL)) continue;

                /** add to response */
                array_push($response, self::mapItem($item));

            }
        }

        return $response;

    }

    /**
     * Map Item
     *
     * @param $item
     * @return AmazonSearchResultObject
     * @throws AmazonApiException
     */
    private function mapItem($item){

        /**
         * Fail when response is not of expected format
         */
        if(
            !isset($item->ASIN) ||
            !isset($item->ItemAttributes->Title) ||
            !isset($item->DetailPageURL)
        ){
            throw new AmazonApiException('Invalid response from Amazon, missing required Item data.');
        }

        /** @var AmazonSearchResultObject $AmazonSearchResultObject */
        $AmazonSearchResultObject = CoreLogic::getObject('AmazonSearchResultObject');
        $AmazonSearchResultObject->setAsin($item->ASIN->__toString());
        $AmazonSearchResultObject->setTitle($item->ItemAttributes->Title->__toString());
        $description = null;
        if(isset($item->EditorialReviews->EditorialReview->Content)){
            $description = $item->EditorialReviews->EditorialReview->Content->__toString();
        }
        if(empty($description) && isset($item->ItemAttributes->Feature) && is_array($item->ItemAttributes->Feature)){
            $description = implode('. ', $item->ItemAttributes->Feature);
        }

        /**
         * Set amazon images
         */
        if(isset($item->ImageSets->ImageSet)){
            $images = array();
            foreach($item->ImageSets->ImageSet as $image){
                array_push($images, $image->LargeImage);
            }
            $AmazonSearchResultObject->setImages($images);
        }

        /**
         * Strip html
         */
        $description = strip_tags(CoreStringUtils::encodeStringToUTF8($description));

        /**
         * Limit string
         */
        $description = CoreStringUtils::limitString($description, 500);

        $AmazonSearchResultObject->setDescription($description);
        $AmazonSearchResultObject->setUrl($item->DetailPageURL->__toString());
        $AmazonSearchResultObject->setImage($item->LargeImage->URL->__toString());
        if(isset($item->ItemAttributes->ListPrice->Amount)) {
            $AmazonSearchResultObject->setPrice($item->ItemAttributes->ListPrice->Amount / 100);
        }else{
            if(isset($item->OfferSummary->LowestNewPrice->Amount)){
                $AmazonSearchResultObject->setPrice($item->OfferSummary->LowestNewPrice->Amount / 100);
            }
        }

        /**
         * Customer review iframe url
         */
        if(isset($item->CustomerReviews->IFrameURL) && isset($item->CustomerReviews->HasReviews) && CoreStringUtils::evaluateBoolean($item->CustomerReviews->HasReviews->__toString())) {
            $AmazonSearchResultObject->setReviewsUrl($item->CustomerReviews->IFrameURL->__toString());
        }

        /**
         * Related products
         */
        if(isset($item->SimilarProducts->SimilarProduct) && !empty($item->SimilarProducts->SimilarProduct)){
            $products = array();
            foreach($item->SimilarProducts->SimilarProduct as $entry){
                $product = new stdClass();
                $product->asin = $entry->ASIN->__toString();
                $product->title = $entry->Title->__toString();
                array_push($products, $product);
            }
            if(!empty($products)){
                $AmazonSearchResultObject->setRelatedProducts($products);
            }
        }

        return $AmazonSearchResultObject;

    }

}