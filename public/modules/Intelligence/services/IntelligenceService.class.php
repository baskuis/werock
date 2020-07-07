<?php

/**
 * Intelligence Service
 * Allows for tracking of data for analytical purposes
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class IntelligenceService {

    use CoreInterceptorTrait;

    /**
     * Constants
     */
    const DEVICE = 'device';
    const DEVICE_MOBILE = 'mobile';
    const DEVICE_DESKTOP = 'desktop';
    const BROWSER = 'browser';
    const PLATFORM = 'platform';
    const DESKTOP = 'desktop';
    const BROWSER_VERSION = 'browser version';
    const SPACE = ' ';
    const CITY = 'city';
    const REGION = 'region';
    const STATE = 'state';
    const COUNTRY = 'country';
    const COUNTRY_NAME = 'country_name';
    const REQUEST_URI = 'request uri';
    const PAGE_VIEW = 'page view';
    const HTTP_USER_AGENT = 'HTTP_USER_AGENT';
    const HTTP_REQUEST_URI = 'REQUEST_URI';
    const REMOTE_ADDR = 'REMOTE_ADDR';
    
	/**
	 * Intelligence Stack
	 */
	public $IntelligenceStack = array();

    /** @var bool $isCrawler */
    public $isCrawler = false;
    public $crawlerChecked = false;

    /** @var IntelligenceProcedure $IntelligenceProcedure */
    private $IntelligenceProcedure;

    /** @var MapTableService $MapTableService */
    private $MapTableService;

    /**
     * Mobile agents
     *
     * @var array
     */
    private static $mobileAgents = array(
        'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
        'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
        'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
        'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
        'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
        'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
        'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
        'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
        'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-');

    function __construct(){
        $this->IntelligenceProcedure = CoreLogic::getProcedure('IntelligenceProcedure');
        $this->MapTableService = CoreLogic::getService('MapTableService');
    }

    /**
     * Get records added
     *
     * @param IntelligenceTableRangeRequestObject $intelligenceTableRangeRequestObject
     * @return array
     */
    public function getRecordsAdded(IntelligenceTableRangeRequestObject $intelligenceTableRangeRequestObject){

        /** @var MapTableContextObject $MapTableContextObject */
        $MapTableContextObject = CoreLogic::getObject('MapTableContextObject');
        $MapTableContextObject->setTable($intelligenceTableRangeRequestObject->getTable());

        $this->MapTableService->setContext($MapTableContextObject);
        $this->MapTableService->mapTables();

        /** @var MapTableContextObject $MapTableContextObject */
        $MapTableContextObject = $this->MapTableService->getContext();

        /** @var MapTableTableObject $MapTableTableObject */
        $MapTableTableObject = $MapTableContextObject->getMapTableTableObject();

        /** @var MapTableColumnObject $DateAddedColumn */
        $DateAddedColumn = $MapTableTableObject->getDateAddedColumn();

        /** Set data added column */
        $intelligenceTableRangeRequestObject->setDataAddedField($DateAddedColumn->getField());

        try {

            /** Call procedure */
            return $this->IntelligenceProcedure->getRecordsAdded($intelligenceTableRangeRequestObject);

        } catch (IntelligenceInvalidRangeException $e){
            CoreNotification::set("Invalid range", CoreNotification::ERROR);
        } catch (IntelligenceIntervalTooSmallException $e){
            CoreNotification::set('Interval is too small', CoreNotification::ERROR);
        }

    }

    /**
     * Get intelligence data
     *
     * @param IntelligenceDataRequestObject $intelligenceDataRequestObject
     * @return mixed
     */
    public function getData(IntelligenceDataRequestObject $intelligenceDataRequestObject){

        try {

            /** Call procedure */
            return $this->IntelligenceProcedure->getData($intelligenceDataRequestObject);

        } catch (IntelligenceInvalidRangeException $e){
            CoreNotification::set("Invalid range", CoreNotification::ERROR);
        } catch (IntelligenceIntervalTooSmallException $e){
            CoreNotification::set('Interval is too small', CoreNotification::ERROR);
        }

    }

	/**
	 * Add values to intelligence stack
	 * @param string $data_name Key of data
	 * @param string $value Value to be saved, this should not have endless different values
	 * @return bool Return true when saved and false otherwise
	 */
	public function _addToIntelligenceStack($data_name = null, $value = null){

		//quick sanity checks
		if(empty($data_name)){ 		
			CoreLog::error('Did not get a data name passed.');
			return false; 
		}	
		if(empty($value)){ 
			CoreLog::error('Can not save an empty value to intelligence stack for key: ' . $data_name . '.');
			return false; 
		}

        /** @var IntelligenceEntryObject $IntelligenceEntryObject */
        $IntelligenceEntryObject = CoreLogic::getObject('IntelligenceEntryObject');
        $IntelligenceEntryObject->setVisitor(CoreVisitor::getId());
        $IntelligenceEntryObject->setUser(CoreUser::getId());
        $IntelligenceEntryObject->setData($data_name);
        $IntelligenceEntryObject->setValue($value);
        $IntelligenceEntryObject->setIsBot(self::isWebSpider());

        /**
         * Stack intelligence value
         */
        array_push($this->IntelligenceStack, $IntelligenceEntryObject);

        //return true
        return true;

	}

    /**
     * Get intelligence data
     *
     * @param null $id
     * @return bool|object
     */
    public function getIntelligenceData($id = null){
        return $this->IntelligenceProcedure->getIntelligenceData($id);
    }

	/**
	 * Inserts intelligence stack
	 * @return bool Return true when saved and false otherwise
	 */
	public function insertIntelligenceStack(){

		/**
		 * Insert intel stack
		 */
        $this->IntelligenceProcedure->insertIntelligenceStack($this->IntelligenceStack);
		
	}

	/**
	 * See if this is a spider
     *
	 * @return bool Return true when this is 'probably' a crawler
	 */
	public function isWebSpider(){

        /** No need to keep looking this up */
        if($this->crawlerChecked){
           return $this->isCrawler;
        }

        /** @var boolean crawlerChecked - checking now */
        $this->crawlerChecked = true;

        /**
         * Detect crawler
         */
        require __DIR__ . '/lib/CrawlerDetect.php';
        $CrawlerDetect = new CrawlerDetect();
        return $this->isCrawler = $CrawlerDetect->isCrawler();
	
	}
	
	/**
	 * See if this is a mobile browser
	 * @return bool Returns true if this is a mobile browser
	 */
	public function isMobileBrowser(){
	    
	    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
	    $mobile_browser = 0; 
	    if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER[self::HTTP_USER_AGENT]))){ $mobile_browser++; } 
	    if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false)){ $mobile_browser++; } 
	    if(isset($_SERVER['HTTP_X_WAP_PROFILE'])){ $mobile_browser++; } 
	    if(isset($_SERVER['HTTP_PROFILE'])){ $mobile_browser++; } 
	    $mobile_ua = strtolower(substr($_SERVER[self::HTTP_USER_AGENT],0,4));
	   	if(in_array($mobile_ua, self::$mobileAgents)){ $mobile_browser++; }
	   	if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false){ $mobile_browser++; } 
	   	if(strpos(strtolower($_SERVER[self::HTTP_USER_AGENT]), 'windows') !== false){ $mobile_browser = 0; } 
	   	if(strpos(strtolower($_SERVER[self::HTTP_USER_AGENT]), 'windows phone') !== false){ $mobile_browser++; } 
	   	return ($mobile_browser > 0);
	
	}
	
	/**
	 * Gets browser details
     *
	 * @return IntelligenceBrowserDetailsObject Returns browser details
	 */
	public function getBrowserDetails(){
		
		//get browser
		$userAgent = strtolower($_SERVER[self::HTTP_USER_AGENT]);
		
		//get browser
		if(preg_match('/opera/i', $userAgent)){ $name = 'opera'; }
		elseif(preg_match('/chrome/i', $userAgent)){ $name = 'chrome'; }
		elseif(preg_match('/webkit/i', $userAgent)){ $name = 'safari'; }
		elseif(preg_match('/msie/i', $userAgent)){ $name = 'msie'; }
		elseif(preg_match('/mozilla/i', $userAgent) && !preg_match('/compatible/', $userAgent)){ $name = 'mozilla'; } 
	    else{ $name = 'other'; } 
	    
	    //get version
	    if(preg_match('/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/i', $userAgent, $matches)){ $version = $matches[1]; }else{ $version = 'unknown'; } 
	    
	    //get platform
	    if(preg_match('/linux/i', $userAgent)){ $platform = 'linux'; }
	    elseif(preg_match('/iphone/i', $userAgent)){ $platform = 'iphone'; }
	    elseif(preg_match('/ipad/i', $userAgent)){ $platform = 'ipad'; }
	    elseif(preg_match('/iphone/i', $userAgent)){ $platform = 'iphone'; }
		elseif(preg_match('/xbox/i', $userAgent)){ $platform = 'xbox'; }
		elseif(preg_match('/android/i', $userAgent)){ $platform = 'android'; }
		elseif(preg_match('/ubuntu/i', $userAgent)){ $platform = 'ubuntu'; }
	    elseif(preg_match('/macintosh|mac os x/i', $userAgent)){ $platform = 'mac'; }
	    elseif(preg_match('/windows|win32/i', $userAgent)){ $platform = 'windows'; }
	    else{ $platform = 'ohter'; } 

        /** @var IntelligenceBrowserDetailsObject $IntelligenceBrowserDetailsObject */
        $IntelligenceBrowserDetailsObject = CoreLogic::getObject('IntelligenceBrowserDetailsObject');
        $IntelligenceBrowserDetailsObject->setBrowser($name);
        $IntelligenceBrowserDetailsObject->setVersion($version);
        $IntelligenceBrowserDetailsObject->setPlatform($platform);
        $IntelligenceBrowserDetailsObject->setUserAgent($userAgent);
        $IntelligenceBrowserDetailsObject->setIsMobile(self::isMobileBrowser());

	    //return the results
	    return $IntelligenceBrowserDetailsObject;
	
	}
	
	/**
	 * Stack visitor machine details
	 * @return bool true
	 */
	public function stackVisitorMachineDetails(){

        /** @var IntelligenceBrowserDetailsObject $IntelligenceBrowserDetailsObject */
        $IntelligenceBrowserDetailsObject = self::getBrowserDetails();
		if(isset($IntelligenceBrowserDetailsObject->version) && isset($IntelligenceBrowserDetailsObject->browser)){ self::addToIntelligenceStack(self::BROWSER_VERSION, $IntelligenceBrowserDetailsObject->version . self::SPACE . $IntelligenceBrowserDetailsObject->browser); }
		if(isset($IntelligenceBrowserDetailsObject->browser)){ self::addToIntelligenceStack(self::BROWSER, $IntelligenceBrowserDetailsObject->browser); }
		if(isset($IntelligenceBrowserDetailsObject->platform)){ self::addToIntelligenceStack(self::PLATFORM, $IntelligenceBrowserDetailsObject->platform); }
		self::addToIntelligenceStack(self::DEVICE, (($IntelligenceBrowserDetailsObject->getIsMobile() === true) ? self::DEVICE_MOBILE : self::DESKTOP));

        return true;

    }

    /**
     * Get navigation details
     *
     * @return IntelligenceNavigationDetailsObject
     */
    public function getNavigationDetails(){

        /** @var IntelligenceNavigationDetailsObject $IntelligenceNavigationDetailsObject */
        $IntelligenceNavigationDetailsObject = CoreLogic::getObject('IntelligenceNavigationDetailsObject');

        /** Set request information */
        $IntelligenceNavigationDetailsObject->setRequestURI($_SERVER[self::HTTP_REQUEST_URI]);
        $IntelligenceNavigationDetailsObject->setPageView(self::PAGE_VIEW);

        return $IntelligenceNavigationDetailsObject;

    }
	
	/**
	 * Stack navigation details
	 * @return bool true
	 */	
	public function stackNavigationDetails(){

        /** @var IntelligenceNavigationDetailsObject $IntelligenceNavigationDetailsObject */
        $IntelligenceNavigationDetailsObject = self::getNavigationDetails();

        /** Stack to intelligence */
        self::addToIntelligenceStack(self::REQUEST_URI, $IntelligenceNavigationDetailsObject->getRequestURI());
		self::addToIntelligenceStack(self::PAGE_VIEW, $IntelligenceNavigationDetailsObject->getPageView());

		return true;

    }

    /**
     * Return location details
     *
     * @return IntelligenceLocationDetailsObject|null
     */
    public function getLocationDetails(){

        /** Assertion */
        if(!function_exists('geoip_record_by_name')) return null;

        /** @var array $data */
        $data = @geoip_record_by_name($_SERVER[self::REMOTE_ADDR]);

        /** Need geoip location data */
        if(!$data){
            CoreLog::debug('Unable get location data from geoip');
            return;
        }

        /** @var IntelligenceLocationDetailsObject $IntelligenceLocationDetailsObject */
        $IntelligenceLocationDetailsObject = CoreLogic::getObject('IntelligenceLocationDetailsObject');
        $IntelligenceLocationDetailsObject->setCity((isset($data[self::CITY]) ? $data[self::CITY] : null));
        $IntelligenceLocationDetailsObject->setRegion((isset($data[self::REGION]) ? $data[self::REGION] : null));
        $IntelligenceLocationDetailsObject->setCountry((isset($data[self::COUNTRY_NAME]) ? $data[self::COUNTRY_NAME] : null));

        return $IntelligenceLocationDetailsObject;

    }
	
	/**
	 * Stack visitor location
	 * @return bool true
	 */	
	public function stackVisitorLocation(){

        /** @var IntelligenceLocationDetailsObject $IntelligenceLocationDetailsObject */
        $IntelligenceLocationDetailsObject = self::getLocationDetails();
        if(!$IntelligenceLocationDetailsObject) return false;

        /** Stack intelligence data */
        if(!empty($IntelligenceLocationDetailsObject->country)) self::addToIntelligenceStack(self::COUNTRY, $IntelligenceLocationDetailsObject->getCountry());
        if(!empty($IntelligenceLocationDetailsObject->region)){
            $region = $IntelligenceLocationDetailsObject->getRegion();
            $state = CoreGeoUtils::lookupRegion($region);
            if($region != $state){
                self::addToIntelligenceStack(self::REGION, $state);
                self::addToIntelligenceStack(self::STATE, $state);
            }else{
                self::addToIntelligenceStack(self::REGION, $region);
            }
        }
        if(!empty($IntelligenceLocationDetailsObject->city)) self::addToIntelligenceStack(self::CITY, $IntelligenceLocationDetailsObject->getCity());

		return true;

	}
	
}