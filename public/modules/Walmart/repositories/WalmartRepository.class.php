<?php

/**
 * Class WalmartRepository
 *
 */
class WalmartRepository {

    const URL = '';
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_POST = 'POST';
    const REQUEST_METHOD_PUT = 'PUT';
    const REQUEST_METHOD_DELETE = 'DELETE';
    const WALMART_PRIVATE_KEY = '';
    const WALMART_CONSUMER_ID = '';
    const KEY_BEGIN_MARKER = "-----BEGIN PRIVATE KEY-----";
    const KEY_END_MARKER = "-----END PRIVATE KEY-----";
    const NEW_LINE = "\n";

    const OPEN_API_KEY = 'g4at845gkxzuu4rw8bgq56wf';

    function __construct(){
        /** TODO: create admin page where open api key can be set */
    }

    /**
     * Lookup by UPC
     *
     * @param null $upc
     * @return mixed
     * @throws Exception
     */
    public function upcLookup($upc = null){
        $url = 'http://api.walmartlabs.com/v1/items?apiKey=' . self::OPEN_API_KEY . '&upc=' . $upc;
        $response = CoreRemoteUtils::getRemoteContents($url, $_REQUEST['HTTP_USERAGENT']);
        $return = json_decode($response);
        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                throw new Exception('Maximum stack depth exceeded');
                break;
            case JSON_ERROR_STATE_MISMATCH:
                throw new Exception('Underflow or the modes mismatch');
                break;
            case JSON_ERROR_CTRL_CHAR:
                throw new Exception('Unexpected control character found');
                break;
            case JSON_ERROR_SYNTAX:
                throw new Exception('Syntax error, malformed JSON');
                break;
            case JSON_ERROR_UTF8:
                throw new Exception('Malformed UTF-8 characters, possibly incorrectly encoded');
                break;
        }
        return $return;
    }

    /**
     * Search items with search query
     *
     * @param null $search
     * @param int $start
     * @param null $sort
     * @param null $category
     * @return mixed
     * @throws Exception
     */
    public function searchItems($search = null, $start = 0, $sort = null, $category = null) {
        $url = 'http://api.walmartlabs.com/v1/search?query=' . urlencode($search) . '&format=json&apiKey=' . self::OPEN_API_KEY;
        if(!empty($start)){
            $url .= '&start=' . (int) $start;
        }
        if(!empty($sort)){
            $url .= '&sort=' . rawurlencode($sort);
        }
        if(!empty($category) && $category != 'All'){
            $url .= '&categoryId=' . rawurlencode($category);
        }
        $response = CoreRemoteUtils::getRemoteContents($url, $_REQUEST['HTTP_USERAGENT']);
        $return = json_decode($response);
        switch (json_last_error()) {
            case JSON_ERROR_DEPTH:
                throw new Exception('Maximum stack depth exceeded');
                break;
            case JSON_ERROR_STATE_MISMATCH:
                throw new Exception('Underflow or the modes mismatch');
                break;
            case JSON_ERROR_CTRL_CHAR:
                throw new Exception('Unexpected control character found');
                break;
            case JSON_ERROR_SYNTAX:
                throw new Exception('Syntax error, malformed JSON');
                break;
            case JSON_ERROR_UTF8:
                throw new Exception('Malformed UTF-8 characters, possibly incorrectly encoded');
                break;
        }
        return $return;
    }

    /**
     * Get request headers
     *
     * @param $url
     * @param $requestMethod
     * @param $timestamp
     * @return array
     */
    private function getRequestHeaders($url, $requestMethod, $timestamp) {
        return [
            "WM_SVC.NAME" => "Walmart Service Name", //Yes Walmart Gateway API
            "WM_QOS.CORRELATION_ID" => "An ID to correlate your calls with the Walmart system", //Yes	An alphanumeric value
            "WM_SEC.TIMESTAMP" => microtime(), //Yes 1443748249449
            "WM_SEC.AUTH_SIGNATURE" => self::getWalmartAuthSignature($url, $requestMethod, $timestamp),
            "WM_CONSUMER.CHANNEL.TYPE" => "A unique ID to track the consumer request by channel", //Mandatory for V3, Optional for V2	0f3e4dd4-0514-4346-b39d… Get the value from  CSPSupport@wal-mart.com
            "Accept" => "The returned data format", //No application/xml
            "WM_CONSUMER.ID" => ""
        ];
    }

    /**
     * Get Walmart Auth Signature
     *
     * @param $url
     * @param $requestMethod
     * @param $timestamp
     * @return null|string
     */
    private function getWalmartAuthSignature($url, $requestMethod = null, $timestamp = null){
        $AuthData = self::WALMART_PRIVATE_KEY . self::NEW_LINE;
        $AuthData .= $url . self::NEW_LINE;
        $AuthData .= $requestMethod . self::NEW_LINE;
        $AuthData .= $timestamp . self::NEW_LINE;
        $Pem = self::convertPkcs8ToPem(base64_decode(self::WALMART_CONSUMER_ID));
        $PrivateKey = openssl_pkey_get_private($Pem);
        $Hash = defined("OPENSSL_ALGO_SHA256") ? OPENSSL_ALGO_SHA256 : "sha256";
        if (!openssl_sign($AuthData, $Signature, $PrivateKey, $Hash)) { return null; }
        return base64_encode($Signature);
    }

    /**
     * Convert Pkcs8 to Pem format
     *
     * @param $der
     * @return string
     */
    private function convertPkcs8ToPem($der = null){
        $key = base64_encode($der);
        $pem = self::KEY_BEGIN_MARKER . self::NEW_LINE;
        $pem .= chunk_split($key, 64, self::NEW_LINE);
        $pem .= self::KEY_END_MARKER . self::NEW_LINE;
        return $pem;
    }

}