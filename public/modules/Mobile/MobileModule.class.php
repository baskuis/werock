<?php

/**
 * Mobile Module
 * adds support for mobile application development
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MobileModule implements CoreModuleInterface {

    /**
     * Module description
     */
    public static $name = 'Mobile Module';
    public static $description = 'Mobile support';
    public static $version = '1.0.0.0';
    public static $dependencies = array(
        'User' => array('min' => '1.0.0', 'max' => '1.9.9'),
        'TemplateUtils' => array('min' => '1.0.0', 'max' => '1.9.9'),
        'CrutchKit' => array('min' => '1.0.0', 'max' => '1.9.9'),
        'Intelligence' => array('min' => '1.0.0', 'max' => '1.9.9')
    );

    /** @var UserService $UserService */
    private static $UserService;

    public static function __init__(){
        self::$UserService = CoreLogic::getService('UserService');
    }

    public static function getRoutes(){
        $routes = array();

        array_push($routes, CoreControllerObject::buildMethod('/mobile/set/entry', __CLASS__, 'entryPoint', CoreControllerObject::MATCH_TYPE_STRING));
        array_push($routes, CoreControllerObject::buildApi('/api/v1/people/login', __CLASS__, 'loginUserApi', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_POST));
        array_push($routes, CoreControllerObject::buildApi('/api/v1/people/loginget', __CLASS__, 'loginUserApiGet', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));
        array_push($routes, CoreControllerObject::buildApi('/api/v1/people/create', __CLASS__, 'createUserApi', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_POST));

        array_push($routes, CoreControllerObject::buildApi('/api/v1/mobile/css', __CLASS__, 'getCssFiles', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));
        array_push($routes, CoreControllerObject::buildApi('/api/v1/mobile/crutches', __CLASS__, 'crutchesLoader', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));

        array_push($routes, CoreControllerObject::buildApi('/^\/api\/v1\/mobile\/template\/([^\/]+)\/?$/', __CLASS__, 'getTemplate', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::REQUEST_GET));
        array_push($routes, CoreControllerObject::buildMethod('/^\/mobile\/template\/([^\/]+)\/view\/?/', __CLASS__, 'getTemplateView', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::REQUEST_GET));

        return $routes;
    }

    public static function getListeners(){

    }

    public static function getInterceptors(){
        $interceptors = array();
        array_push($interceptors, new CoreInterceptorObject('CoreApi', 'getOutput', __CLASS__, 'apiJsonP', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));
        return $interceptors;
    }

    public static function getMenus(){

    }

    public static function getCssFiles(){
        if(CoreLess::haveLessMatch(CoreLess::ALL_STYLES)){
            CoreApi::setData('less', CoreLess::getCssFile(CoreLess::ALL_STYLES));
        }
        if(CoreSCSS::haveSCSSMatch(CoreSCSS::ALL_STYLES)){
            CoreApi::setData('scss', CoreSCSS::getCssFile(CoreLess::ALL_STYLES));
        }
    }

    /**
     * Get template
     *
     * @param array $params
     */
    public static function getTemplate($params = array()){

        /** @var string $templateName */
        $templateName = isset($params[1]) ? $params[1] : false;

        /** @var CoreTemplateObject $CoreTemplateObject */
        $CoreTemplateObject = CoreTemplate::getByNamespace($templateName);
        CoreApi::setData('template', $CoreTemplateObject);
        CoreApi::setData('templateView', CoreTemplate::getView($templateName));

    }

    /**
     * Get template view
     *
     * @param array $params
     */
    public static function getTemplateView($params = array()){
        /** @var string $templateName */
        $templateName = isset($params[1]) ? $params[1] : false;
        $wrap = isset($_GET['wrap']) ? $_GET['wrap'] : false;
        /** @var CoreTemplateObject $CoreTemplateObject */
        $CoreTemplateObject = CoreTemplate::getByNamespace($templateName);
        $controller = isset($_GET['controller']) ? $_GET['controller'] : false;
        $template = null;
        switch($wrap){
            case 'ionic-view':
                $template = '
                    <ion-view view-title="{{title}}">
                        <ion-content class="has-header">
                            ' . preg_replace(array('/ href="#[^"]+" /i'), array(' '), CoreTemplate::getView($CoreTemplateObject->getNamespace())) . '
                        </ion-content>
                    </ion-view>';
                break;
            case 'ionic-modal':
                $template = '
                    <ion-modal-view>
                        <ion-header-bar>
                            <h1 class="title">{{title}}</h1>
                            <div class="buttons">
                                <a class="button button-clear" ng-click="closeModal()">
                                    <i class="icon icon-right ion-android-close ion-accessory" style="color: #666;"></i>
                                </a>
                            </div>
                        </ion-header-bar>
                        <ion-content ' . ($controller ? 'ng-controller="' . $controller . '"' : '') . '>
                            ' . preg_replace(array('/ href="#[^"]+" /i'), array(' '), CoreTemplate::getView($CoreTemplateObject->getNamespace())) . '
                        </ion-content>
                    </ion-modal-view>';
                break;
            case 'jsonp':
                $callback = isset($_GET['callback']) ? $_GET['callback'] : 'callback';
                $payload = new stdClass();
                $payload->view = CoreTemplate::getView($CoreTemplateObject->getNamespace());
                $template = $callback . '(' . json_encode($payload) . ');';
                break;
            default:
                $template = preg_replace(array('/ href="#[^"]+" /i'), array(' '), CoreTemplate::getView($CoreTemplateObject->getNamespace()));
                break;
        }
        CoreRender::$output = CoreStringUtils::EMPTY_STRING;
        //CoreRender::$output .= self::loadConditionalCrutches(CoreCrutches::getMarkedCrutches(CoreCrutches::DOCUMENT_HEAD));
        CoreRender::$output .= $template;
        //if($includeScript){
        //    CoreRender::$output .= CoreHtmlUtils::script($CoreTemplateObject->getScript(), array('type' => 'text/javascript'));
        //}
        //CoreRender::$output .= self::loadConditionalCrutches(CoreCrutches::getMarkedCrutches(CoreCrutches::DOCUMENT_BODY));

    }

    /**
     * Load crutches
     *
     * @param array $params
     */
    public static function crutchesLoader($params = array()){
        $templatesString = isset($_GET['templates']) ? $_GET['templates'] : null;
        $excludeString = isset($_GET['excludes']) ? $_GET['excludes'] : null;
        $templates = explode(',', $templatesString);
        $excludes = explode(',', $excludeString);
        foreach($templates as $template){ CoreTemplate::getView($template); }
        $headCrutches = CoreCrutches::getMarkedCrutches(CoreCrutches::DOCUMENT_HEAD);
        $bodyCrutches = CoreCrutches::getMarkedCrutches(CoreCrutches::DOCUMENT_BODY);
        foreach($excludes as $exclude){
            if(isset($headCrutches[$exclude])){
                unset($headCrutches[$exclude]);
            }
            if(isset($bodyCrutches[$exclude])){
                unset($bodyCrutches[$exclude]);
            }
        }
        CoreRender::$output = CoreStringUtils::EMPTY_STRING;
        CoreRender::renderCrutches($headCrutches);
        CoreApi::setData('head', CoreRender::$output);
        CoreRender::$output = CoreStringUtils::EMPTY_STRING;
        CoreRender::renderCrutches($bodyCrutches);
        CoreApi::setData('body', CoreRender::$output);
    }

    /**
     * Load conditional crutches
     *
     * @param array $crutches
     * @return string
     */
    private static function loadConditionalCrutches($crutches = array()){
        $output = CoreStringUtils::EMPTY_STRING;
        if(!empty($crutches)){
            /** @var CoreCrutchObject $crutch */
            foreach($crutches as $crutch){
                if(!empty($crutch)) {
                    foreach($crutch as $crutchAsset) {
                        if (get_class($crutchAsset) == CoreCrutchObject::class) {
                            $attr = array();
                            if ($crutchAsset->hasAttr()) {
                                $attr = $crutchAsset->getAttr();
                                foreach ($attr as $attrKey => &$attrValue) {
                                    if ($attrValue == CoreCrutches::CRUTCH_FILE_PLACEHOLDER) {
                                        $attrValue = str_replace(CoreCrutches::CRUTCH_FILE_PLACEHOLDER, HTTP_PROTOCOL . DOMAIN_NAME . $crutchAsset->getWebPath() . $crutchAsset->getFile() . CoreCrutches::PATH_VERSION . $crutchAsset->getVersion(), $attrValue);
                                    }
                                }
                            }

                            $output .= '
                        <script type="text/javascript">
                            ;(function(document){
                                var list = document.getElementsByTagName("' . $crutchAsset->getTag() . '");
                                var i = list.length, flag = false;
                                while (i--) {
                                    if (';
                            foreach ($attr as $key => $value) {
                                $output .= 'list[i].' . $key . ' == "' . $value . '" && ';
                            }
                            $output .= 'true) {
                                        flag = true;
                                    }
                                }
                                if (!flag) {
                                    var tag = document.createElement("' . $crutchAsset->getTag() . '");' . "\n";
                            foreach ($attr as $key => $value) {
                                $output .= 'tag.' . $key . '="' . $value . '";' . "\n";
                            }
                            $output .= '
                                    console.log("loading", tag);
                                    document.getElementsByTagName("' . ($crutchAsset->getType() == CoreCrutches::DOCUMENT_HEAD ? 'head' : 'body') . '")[0].appendChild(tag);
                                }else{
                                    console.log("already have", list);
                                }
                            })(document);
                        </script>
                    ';
                        }
                    }
                }
            }
        }
        return $output;
    }

    /**
     * Modify api response when requesting jsonp
     *
     * @param $response
     * @return string
     */
    public static function apiJsonP($response){
        $type = isset($_GET['type']) ? $_GET['type'] : false;
        if($type !== false && $type === 'jsonp') {
            $callback = isset($_GET['callback']) ? $_GET['callback'] : 'callback';
            return $callback . '(' . $response . ');';
        }
        return $response;
    }

    /**
     * Set entry point
     *
     * Simply set entry token cookie
     *
     */
    public static function entryPoint(){
        CoreSecurity::generateAccessToken();
    }

    /**
     * Api User Create
     */
    public static function createUserApi(){
        /** @var UserTemplateObject $UserTemplateObject */
        $UserTemplateObject = CoreObjectUtils::applyRow('UserTemplateObject', json_decode(file_get_contents('php://input'), true));
        $created = self::$UserService->create($UserTemplateObject);
        if(!$created){
            CoreApi::$status = 401;
        }else{
            CoreApi::$status = 200;
        }
        CoreApi::setData('created', $created);
    }

    /**
     * Api User Login
     */
    public static function loginUserApi(){
        /** @var UserAuthenticationObject $UserAuthenticationObject */
        $UserAuthenticationObject = CoreObjectUtils::applyRow('UserAuthenticationObject', json_decode(file_get_contents('php://input'), true));
        $authenticated = self::$UserService->authenticate($UserAuthenticationObject);
        if(!$authenticated){
            CoreApi::$status = 401;
        }else{
            CoreApi::$status = 200;
        }
        CoreApi::setData('authenticated', $authenticated);
    }

    /**
     * Expose GET api for login
     * needed for jsonp implementations of login
     * TODO: Find alternate solution - preventing pw in url
     */
    public static function loginUserApiGet(){
        $UserAuthenticationObject = CoreObjectUtils::applyRow('UserAuthenticationObject', $_GET);
        $authenticated = self::$UserService->authenticate($UserAuthenticationObject);
        if(!$authenticated){
            CoreApi::$status = 401;
        }else{
            CoreApi::$status = 200;
        }
        CoreApi::setData('authenticated', $authenticated);
    }

    public static function __install__(){

    }

    public static function __update__($previousVersion, $newVersion){

    }

    public static function __enable__(){

    }

    public static function __disable__(){

    }

}