<?php

/**
 * User Module
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserModule implements CoreModuleInterface {
	
	/**
	 * Module description
	 */
	public static $name = 'User Module';
	public static $description = '';
	public static $version = '1.0.0.37';
	public static $dependencies = array(
		'Form' => array(
			'min' => '1.0.0',
			'max' => '1.9.9'
		),
        'Email' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'Intelligence' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'MapTable' => array(
            'min' => '1.0.0',
            'max' => '1.9.9'
        ),
        'Language' => array(
			'min' => '1.0.0',
			'max' => '1.9.9'
		)
	);

    /**
     * User events
     */
    const USER_EVENT_CREATE_SUCCESS = 'user:create:success';
    const USER_EVENT_CREATE_FAILED = 'user:create:failed';
    const USER_EVENT_LOGIN_SUCCESS = 'user:login:success';
    const USER_EVENT_LOGIN_FAILED = 'user:login:failed';
    const USER_EVENT_LOGOUT = 'user:logout';
    const USER_EVENT_CURRENT_USER_NOT_FOUND = 'user:current:lookup:failed';

    /**
     * Intelligence
     */
    const USER_INTELLIGENCE_CREATE_KEY = 'user create';
    const USER_INTELLIGENCE_LOGIN_KEY = 'user login';
    const USER_INTELLIGENCE_SUCCESS = 'success';
    const USER_INTELLIGENCE_FAILED = 'failed';

    /**
     * Constants
     */
    const CONST_REQUESTED_URI_SESSION_KEY = 'REQUESTED_URI';

    /**
     * System groups
     */
    const SYSTEM_GROUP_FULL_SYSTEM_ADMINS = 'system.group.full.sys.admin';
    const SYSTEM_GROUP_GUESTS = 'system.group.guests';
    const SYSTEM_GROUP_REGISTERED_USERS = 'system.group.registered.users';

    /**
     * Entitlement levels
     */
    const ENTITLEMENT_FULL_SYSTEM_ADMIN = 'entitlement.global.full.system.admin';
    const ENTITLEMENT_SYSTEM_ADMIN = 'entitlement.global.system.admin';
    const ENTITLEMENT_MANAGE_USERS = 'entitlement.global.manage.users';

    /**
     * Entitlement types
     */
    const ENTITLEMENT_TYPE_SYSTEM = 'system';
    const ENTITLEMENT_TYPE_MAPTABLE = 'maptable';
    const ENTITLEMENT_TYPE_MAPTABLE_OBJECT = 'maptable.object';

    /**
     * MapTable constants
     */
    const MAPTABLE_BUTTONS_TEMPLATE = 'formbuttonslarge';
    const MAPTABLE_CANCEL_TEMPLATE = 'formcancellarge';
    
    /**
     * MapTable entitlements
     */
    const ENTITLEMENT_MAPTABLE_VIEW = 'maptable:view';
    const ENTITLEMENT_MAPTABLE_CONTRIBUTE = 'maptable:contribute';
    const ENTITLEMENT_MAPTABLE_EDIT = 'maptable:edit';
    const ENTITLEMENT_MAPTABLE_DELETE = 'maptable:delete';
    const ENTITLEMENT_MAPTABLE_CREATE = 'maptable:create';

	/**
	 * Remembered user
	 */
	const REMEMBERED_USER_VIEW_KEY = 'rememberedUser';

    /**
     * @var array
     */
    private static $protectedMapTableObjects = array('werock_users', 'werock_groups', 'werock_group_members');

    /** @var UserService $UserService */
    private static $UserService;

    /** @var UserRepository $UserRepository */
    private static $UserRepository;

    /** @var UserEntitlementService $UserEntitlementService */
    private static $UserEntitlementService;

    /** @var MapTableService $MapTableService */
    private static $MapTableService;

    /** @var UserGroupService $UserGroupService */
    private static $UserGroupService;

    /** @var IntelligenceService $IntelligenceService */
    private static $IntelligenceService;
    
    /** RememberedUserObject $RememberedUserObject */
    public static $RememberedUserObject;

    /** @var bool $requireUserEmailActive */
    public static $requireUserEmailActive = true;

    /**
     * Get listeners
     *
     * @return mixed
     */
    public static function getListeners()
    {

        $listeners = array();

        /**
         * Handle successful login
         */
        array_push($listeners, new CoreObserverObject(self::USER_EVENT_LOGIN_SUCCESS, __CLASS__, 'setRedirectUponAuthentication'));
        array_push($listeners, new CoreObserverObject(self::USER_EVENT_CREATE_SUCCESS, __CLASS__, 'setRedirectUponAuthentication'));

        /**
         * Load current user in action context
         */
        array_push($listeners, new CoreObserverObject(CoreController::CONTROLLER_EVENT_ACTION_BEFORE_EXECUTE, __CLASS__, 'currentUserActionContext'));

        array_push($listeners, new CoreObserverObject(CoreController::CONTROLLER_EVENT_ACTION_BEFORE_EXECUTE, __CLASS__, 'areEntitlementsRelevantForRelatedTable'));

        /**
         * MapTable listener
         */
        if(DEV_MODE || CACHING_ENABLED){
            array_push($listeners, new CoreObserverObject(MapTableModule::EVENT_UPDATED, __CLASS__, 'clearCaches'));
            array_push($listeners, new CoreObserverObject(MapTableModule::EVENT_INSERTED, __CLASS__, 'clearCaches'));
            array_push($listeners, new CoreObserverObject(MapTableModule::EVENT_DELETED, __CLASS__, 'clearCaches'));
        }

        /**
         * Send activation email confirmation
         */
        array_push($listeners, new CoreObserverObject(MapTableModule::EVENT_INSERTED, __CLASS__, 'sendActivationEmail'));

        /**
         * Track important user events
         */
        array_push($listeners, new CoreObserverObject(UserModule::USER_EVENT_CREATE_SUCCESS, __CLASS__, 'userCreateSuccess'));
        array_push($listeners, new CoreObserverObject(UserModule::USER_EVENT_LOGIN_SUCCESS, __CLASS__, 'userLoginSuccess'));
        array_push($listeners, new CoreObserverObject(UserModule::USER_EVENT_CREATE_FAILED, __CLASS__, 'userCreateFailed'));
        array_push($listeners, new CoreObserverObject(UserModule::USER_EVENT_LOGIN_FAILED, __CLASS__, 'userLoginFailed'));

        return $listeners;

    }

    /**
     * Get interceptors
     *
     * @return mixed
     */
    public static function getInterceptors()
    {

        $interceptors = array();

        /**
         * Set current user data on user manager
         * before handling the request
         */
        array_push($interceptors, new CoreInterceptorObject('CoreInit', 'identify', __CLASS__, 'setCurrentUser', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));
        array_push($interceptors, new CoreInterceptorObject('CoreInit', 'identify', __CLASS__, 'setRememberedUser', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));

        /**
         * Handle unauthorized access
         */
        array_push($interceptors, new CoreInterceptorObject('UserUnauthorizedException', 'handle', __CLASS__, 'handleUnauthorized', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));

        /**
         * Make table data available to entitlement picker
         */
        array_push($interceptors, new CoreInterceptorObject('MapTableService', 'fromContext', __CLASS__, 'mapTableFromContext', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));

        /**
         * Map-Table interceptors
         */
        array_push($interceptors, new CoreInterceptorObject('MapTableService', 'captureSubmission', __CLASS__, 'submitMaptableInterceptor', CoreInterceptorObject::INTERCEPTOR_TYPE_BEFORE));
        array_push($interceptors, new CoreInterceptorObject('MapTableService', 'buildFormField', __CLASS__, 'buildFormField', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));
        array_push($interceptors, new CoreInterceptorObject('MapTableService', 'buildTopFormControls', __CLASS__, 'buildTopFormControls', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));
        array_push($interceptors, new CoreInterceptorObject('MapTableService', 'buildBottomFormControls', __CLASS__, 'buildBottomFormControls', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));
        array_push($interceptors, new CoreInterceptorObject('MapTableContextObject', 'getRelatedTable', __CLASS__, 'getRelatedTable', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));
        array_push($interceptors, new CoreInterceptorObject('MapTableContextObject', 'getAssociatedTables', __CLASS__, 'getAssociatedTables', CoreInterceptorObject::INTERCEPTOR_TYPE_AFTER));

        return $interceptors;

    }

    /**
     * Get menus
     *
     * @return mixed
     */
    public static function getMenus()
    {

    }

    /**
     * Get routes
     *
     * @return mixed
     */
    public static function getRoutes()
    {

        $routes = array();

        /**
         * Core toMethod
         * register actions
         * for this module
         */
        array_push($routes, CoreControllerObject::buildMethod('/do/logout', __CLASS__, 'logout', CoreControllerObject::MATCH_TYPE_STRING));

        /**
         * API
         */
        $route = new CoreControllerObject('/^\/api\/v1\/people\/([0-9]+)\/?$/i', __CLASS__, 'getUser', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::GROUP_API);
        $route->setType(CoreControllerObject::TYPE_METHOD);
        array_push($routes, $route);

        $route = new CoreControllerObject('/api/v1/people/logout', __CLASS__, 'doLogout', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_API);
        $route->setRequestMethod(CoreControllerObject::REQUEST_GET);
        $route->setType(CoreControllerObject::TYPE_METHOD);
        array_push($routes, $route);

        $route = new CoreControllerObject('/api/v1/people/me', __CLASS__, 'getMe', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_API);
        $route->setType(CoreControllerObject::TYPE_METHOD);
        array_push($routes, $route);

        $route = new CoreControllerObject('/api/v1/people/search', __CLASS__, 'getUsers', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_API);
        $route->setType(CoreControllerObject::TYPE_METHOD);
        array_push($routes, $route);

        $route = new CoreControllerObject('/api/v1/groups', __CLASS__, 'getGroups', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_API);
        $route->setType(CoreControllerObject::TYPE_METHOD);
        array_push($routes, $route);

        $route = new CoreControllerObject('/^\/api\/v1\/groups\/([0-9]+)\/?$/i', __CLASS__, 'getGroup', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::GROUP_API);
        $route->setType(CoreControllerObject::TYPE_METHOD);
        array_push($routes, $route);

        $route = new CoreControllerObject('/api/v1/entitlements/create', __CLASS__, 'createObjectGroupEntitlement', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_API);
        $route->setType(CoreControllerObject::TYPE_METHOD);
        array_push($routes, $route);

        $route = new CoreControllerObject('/api/v1/entitlements/delete', __CLASS__, 'deleteObjectGroupEntitlements', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_API);
        $route->setType(CoreControllerObject::TYPE_METHOD);
        array_push($routes, $route);

        $route = new CoreControllerObject('/^\/api\/v1\/entitlements\/bytype\/([^\/]*)\/?$/i', __CLASS__, 'getEntitlements', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::GROUP_API);
        $route->setType(CoreControllerObject::TYPE_METHOD);
        array_push($routes, $route);

        /**
         * Re-send activation email
         */
        $route = new CoreControllerObject('/api/v1/people/me/activation/resend', __CLASS__, 'resendActivationEmail', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::GROUP_API);
        $route->setType(CoreControllerObject::TYPE_METHOD);
        $route->setRequestMethod(CoreControllerObject::REQUEST_GET);
        array_push($routes, $route);

        /**
         * Email exists endpoint
         */
        array_push($routes, CoreControllerObject::buildApi('/^\/api\/v1\/people\/emailexists\/([^\/]*)\/?$/i', __CLASS__, 'emailExists', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::REQUEST_GET));

        /**
         * Username exists endpoint
         */
        array_push($routes, CoreControllerObject::buildApi('/^\/api\/v1\/people\/usernameexists\/([^\/]*)\/?$/i', __CLASS__, 'usernameExists', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::REQUEST_GET));

        /**
         * Gain access via access token
         */
        array_push($routes, CoreControllerObject::buildMethod('/^\/do\/access\/([^\/]*)\/?$/i', __CLASS__, 'accessToken', CoreControllerObject::MATCH_TYPE_REGEX, CoreControllerObject::REQUEST_GET));

        return $routes;

    }

    /**
	 * UserRegisterAction listeners, toMethod
	 */	
	public static function __init__(){

        /**
         * Assure definitions exist
         * for APC un-serializing
         */
        CoreLogic::getObject('UserGroupMemberObject', false);
        CoreLogic::getObject('UserGroupObject', false);
        CoreLogic::getObject('UserSystemGroupObject', false);
        CoreLogic::getObject('UserObject', false);

        /**
         * Get instances of services etc
         */
        self::$UserService = CoreLogic::getService('UserService');
        self::$UserRepository = CoreLogic::getRepository('UserRepository');
        self::$UserEntitlementService = CoreLogic::getService('UserEntitlementService');
        self::$MapTableService = CoreLogic::getService('MapTableService');
        self::$UserGroupService = CoreLogic::getService('UserGroupService');
        self::$IntelligenceService = CoreLogic::getService('IntelligenceService');

        /**
         * Add MapTable mappings
         */
        self::addFieldMappings();

        /**
         * Add entitlements
         */
        self::registerEntitlements();

        /**
         * Add groups
         */
        self::registerSystemGroups();

        /**
         * Activation reminders
         */
        $job = new CoreScheduleJobObject();
        $job->cron(CoreScheduleJobObject::SCHEDULE_EVERY_FIVE_MINUTES);
        $job->setModule(__CLASS__);
        $job->setMethod('sendActivationReminders');
        CoreSchedule::add($job);

	}

    /**
     * User create success listener
     */
    public static function userCreateSuccess(){
        self::$IntelligenceService->addToIntelligenceStack(self::USER_INTELLIGENCE_CREATE_KEY, self::USER_INTELLIGENCE_SUCCESS);
    }

    /**
     * User create failed listener
     */
    public static function userCreateFailed(){
        self::$IntelligenceService->addToIntelligenceStack(self::USER_INTELLIGENCE_CREATE_KEY, self::USER_INTELLIGENCE_FAILED);
    }

    /**
     * User login success listener
     */
    public static function userLoginSuccess(){
        self::$IntelligenceService->addToIntelligenceStack(self::USER_INTELLIGENCE_LOGIN_KEY, self::USER_INTELLIGENCE_SUCCESS);
    }

    /**
     * User login failed listener
     */
    public static function userLoginFailed(){
        self::$IntelligenceService->addToIntelligenceStack(self::USER_INTELLIGENCE_LOGIN_KEY, self::USER_INTELLIGENCE_FAILED);
    }

    /**
     * Build top form controls interceptor - this will hide form controls when relevant
     *
     * @param $return
     * @param null $params
     * @param MapTableService $mapTableManager
     * @return bool
     * @throws UserEntitlementNotFoundException
     */
    public static function buildTopFormControls($return, $params = null, MapTableService $mapTableManager){

        if(!$return) return false;

        /**
         * If object entitlements are not relevant
         */
        if(in_array($mapTableManager->getContext()->getTable(), self::$protectedMapTableObjects)){
            return $return;
        }

        /** check for view access */
        if(!(bool) self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $mapTableManager->getContext()->getTable(), self::ENTITLEMENT_MAPTABLE_VIEW)){
            return false;
        }

        switch ($mapTableManager->getContext()->action) {
            case 'create':
                if (!self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $mapTableManager->getContext()->getTable(), self::ENTITLEMENT_MAPTABLE_CREATE)) {
                    /** @var FormField $FormField */
                    foreach($return as &$FormField){
                        if($FormField->getType() == self::MAPTABLE_BUTTONS_TEMPLATE) {
                            $FormField->setType(self::MAPTABLE_CANCEL_TEMPLATE);
                        }
                    }
                }
                break;
            case 'edit':
                if (!self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $mapTableManager->getContext()->getTable(), self::ENTITLEMENT_MAPTABLE_EDIT)) {
                    /** @var FormField $FormField */
                    foreach($return as &$FormField){
                        if($FormField->getType() == self::MAPTABLE_BUTTONS_TEMPLATE) {
                            $FormField->setType(self::MAPTABLE_CANCEL_TEMPLATE);
                        }
                    }
                }
                break;
        }

        return $return;

    }

    /**
     * Build bottom form controls interceptor - this will hide form controls when relevant
     *
     * @param $return
     * @param null $params
     * @param MapTableService $mapTableManager
     * @return bool
     * @throws UserEntitlementNotFoundException
     */
    public static function buildBottomFormControls($return, $params = null, MapTableService $mapTableManager){

        if(!$return) return false;

        /**
         * If object entitlements are not relevant
         */
        if(in_array($mapTableManager->getContext()->getTable(), self::$protectedMapTableObjects)){
            return $return;
        }

        /** check for view access */
        if(!(bool) self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $mapTableManager->getContext()->getTable(), self::ENTITLEMENT_MAPTABLE_VIEW)){
            return false;
        }

        switch ($mapTableManager->getContext()->action) {
            case 'create':
                if (!self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $mapTableManager->getContext()->getTable(), self::ENTITLEMENT_MAPTABLE_CREATE)) {
                    foreach($return as &$FormField){
                        if($FormField->getType() == self::MAPTABLE_BUTTONS_TEMPLATE) {
                            $FormField->setType(self::MAPTABLE_CANCEL_TEMPLATE);
                        }
                    }
                }
                break;
            case 'edit':
                if (!self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $mapTableManager->getContext()->getTable(), self::ENTITLEMENT_MAPTABLE_EDIT)) {
                    foreach($return as &$FormField){
                        if($FormField->getType() == self::MAPTABLE_BUTTONS_TEMPLATE) {
                            $FormField->setType(self::MAPTABLE_CANCEL_TEMPLATE);
                        }
                    }
                }
                break;
        }

        return $return;

    }

    /**
     * We are intercepting the getRelatedTable logic
     * to prevent showing the block where related tables are shown when the user
     * does not have view access un the under laying object
     *
     * @param $return
     * @param null $params
     * @param MapTableContextObject $mapTableContextObject
     * @return bool
     */
    public static function getRelatedTable($return, $params = null, MapTableContextObject $mapTableContextObject)
    {

        if (!$return) return false;

        if (!isset($params[0])) CoreLog::error('Need value in $params[0]');

        /**
         * If object entitlements are not relevant
         */
        if (in_array($params[0], self::$protectedMapTableObjects)) {
            return $return;
        }

        /** check for view access */
        if (!(bool)self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $params[0], self::ENTITLEMENT_MAPTABLE_VIEW)) {
            return false;
        }

        return $return;

    }

    /**
     * Strip out associated table references .. if the user does not have view privileges
     * on the under laying object
     *
     * TODO: Only have access to the associated table description object - better to infer from the relationship containing table (and it's entitlements)
     *
     * @param $return
     * @param null $params
     * @param MapTableContextObject $mapTableContextObject
     * @return mixed
     */
    public static function getAssociatedTables($return, $params = null, MapTableContextObject $mapTableContextObject){
        if(!empty($return)){

            /** @var MapTableAssociatedTableDescriptionObject $MapTableAssociatedTableDescriptionObject */
            foreach($return as $table => $MapTableAssociatedTableDescriptionObject){
                if (!(bool)self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $MapTableAssociatedTableDescriptionObject->getTable(), self::ENTITLEMENT_MAPTABLE_VIEW)) {
                    unset($return[$table]);
                }
            }

        }
        return $return;
    }

    /**
     * Perform logout
     *
     */
    public static function doLogout(){
        CoreApi::setData('logout', self::$UserService->logout());
    }

    /**
     * Current user action context
     *
     * @param null $Action
     */
    public static function currentUserActionContext($Action = null){

        /** @var bool activeUser */
        $Action->activeUser = self::$UserService->activeUser();

        /** @var UserObject currentUser */
        $Action->currentUser = self::$UserService->getCurrentUser();

    }

    /**
     * Get current user
     */
    public static function getMe(){

        /** set user */
        CoreApi::setData('user', self::$UserService->getCurrentUser());

    }

    /**
     * Modify action of related table
     *
     * @param null $Action
     */
    public static function areEntitlementsRelevantForRelatedTable($Action = null){
        if(isset($Action->MapTableContextObject->table)){
            if(in_array($Action->MapTableContextObject->table, self::$protectedMapTableObjects)) {
                CoreController::$currentAction->suppressEntitlementPicker = true;
            }
        }
    }

    /**
     * Intercept build form field. We can take the opportunity to make the field as disabled if needed.
     *
     * @param $return
     * @param null $params
     * @param MapTableService $mapTableManager
     * @return bool|FormField
     * @throws UserEntitlementNotFoundException
     */
    public static function buildFormField($return, $params = null, MapTableService $mapTableManager){

        if(!$return) return false;

        /**
         * If object entitlements are not relevant
         */
        if(in_array($mapTableManager->getContext()->getTable(), self::$protectedMapTableObjects)){
            return $return;
        }

        if(get_class($return) == FormField::class){

            /** check for view access */
            if(!(bool) self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $mapTableManager->getContext()->getTable(), self::ENTITLEMENT_MAPTABLE_VIEW)){
                return false;
            }

            switch ($mapTableManager->getContext()->action) {
                case 'create':
                    if (!self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $mapTableManager->getContext()->getTable(), self::ENTITLEMENT_MAPTABLE_CREATE)){
                        /** @var FormField $return */
                        $return->setDisabled(true);
                    }
                    break;
                case 'edit':
                    if (!self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $mapTableManager->getContext()->getTable(), self::ENTITLEMENT_MAPTABLE_EDIT)){
                        /** @var FormField $return */
                        $return->setDisabled(true);
                    }
                    break;
            }

        }

        return $return;

    }

    /**
     * Clear caches
     *
     * @param MapTableContextObject $mapTableContextObject
     */
    public static function clearCaches(MapTableContextObject $mapTableContextObject){
        switch($mapTableContextObject->getTable()){
            case 'werock_groups':
                CoreCache::invalidateNamespace('user:groups');
                CoreCache::invalidateNamespace('user:group:' . $mapTableContextObject->getPrimaryValue());
                break;
            case 'werock_group_members':
                CoreCache::invalidateNamespace('user:groups');
                break;
            case 'werock_users':
                CoreCache::invalidateNamespace('user:groups');
                break;
        }
    }

    /**
     * Send activation email when email updated
     *
     * @param MapTableContextObject $mapTableContextObject
     */
    public static function sendActivationEmail(MapTableContextObject $mapTableContextObject){
        switch($mapTableContextObject->getTable()){
            case 'werock_user_emails':

                try {

                    /** @var MapTableColumnObject $MapTableColumnObjectEmail */
                    $MapTableColumnObjectEmail = $mapTableContextObject->getMapTableTableObject()->getColumns()['werock_user_email_value'];
                    /** @var MapTableColumnObject $MapTableColumnObjectUserId */
                    $MapTableColumnObjectUserId = $mapTableContextObject->getMapTableTableObject()->getColumns()['werock_user_id'];

                    $UpdatedUser = self::$UserService->getUser((int)$MapTableColumnObjectUserId->getSubmittedValue());
                    if (!empty($UpdatedUser)) {
                        self::$UserRepository->assignEmail($UpdatedUser, $MapTableColumnObjectEmail->getSubmittedValue());
                        CoreNotification::set('Sent confirmation email', CoreNotification::SUCCESS);
                    }

                } catch(Exception $e){
                    CoreNotification::set('Unable to send confirmation email', CoreNotification::ERROR);
                }

            break;
        }
    }

    /**
     * Check entitlements and stop modification of record
     * likely this request was spoofed
     *
     * @param null $params
     * @param MapTableService $mapTableManager
     * @return null
     * @throws UserUnauthorizedException
     */
    public static function submitMaptableInterceptor($params = null, MapTableService $mapTableManager){

        /**
         * If object entitlements are not relevant
         */
        if(in_array($mapTableManager->getContext()->getTable(), self::$protectedMapTableObjects)){
            return $params;
        }

        /**
         * If form is submitted check entitlements
         */
        if($mapTableManager->getContext()->getFormUI()->validFormSubmitted()) {

            switch ($mapTableManager->getContext()->action) {
                case 'create':
                    if (!self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $mapTableManager->getContext()->getTable(), self::ENTITLEMENT_MAPTABLE_CREATE)) {
                        CoreNotification::set('You are not allowed to create objects of this kind', CoreNotification::ERROR);
                        throw new UserUnauthorizedException();
                    }
                    break;
                case 'edit':
                    if (!self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $mapTableManager->getContext()->getTable(), self::ENTITLEMENT_MAPTABLE_EDIT)) {
                        CoreNotification::set('You are not allowed to edit objects of this kind', CoreNotification::ERROR);
                        throw new UserUnauthorizedException();
                    }
                    break;
                case 'delete':
                    if (!self::$UserEntitlementService->hasObjectEntitlement(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT . '.' . $mapTableManager->getContext()->getTable(), self::ENTITLEMENT_MAPTABLE_DELETE)) {
                        CoreNotification::set('You are not allowed to delete objects of this kind', CoreNotification::ERROR);
                        throw new UserUnauthorizedException();
                    }
                    break;
            }
        }

        return $params;

    }

    /**
     * Run on fromContext on MapTableService
     *
     * @param MapTableActionModifierObject $mapTableActionModifierObject
     * @param $params
     * @return MapTableActionModifierObject
     * @throws Exception
     */
    public static function mapTableFromContext(MapTableActionModifierObject $mapTableActionModifierObject, $params){

        /**
         * Make table context available to object entitlements picker
         */
        CoreTemplate::setData('objectentitlementspicker', 'MapTableContextObject', $params[0]);

        return $mapTableActionModifierObject;

    }

    /**
     * Get entitlements
     */
    public static function getEntitlements($param = array()){

        /** @var string $type */
        $type = isset($param[1]) ? $param[1] : null;

        /** populate optional parameters */
        $groupId = isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0;
        $groupUrn = isset($_GET['group_urn']) ? $_GET['group_urn'] : null;
        $objectUrn = isset($_GET['object_urn']) ? $_GET['object_urn'] : null;

        /** @var bool $access */
        $access = self::$UserEntitlementService->userHasEntitlement(CoreUser::getUser(), self::$UserEntitlementService->getEntitlement(UserModule::ENTITLEMENT_FULL_SYSTEM_ADMIN));

        if($access) {

            /** @var array $entitlements */
            $entitlements = array();
            $UserEntitlementsContextObject = null;
            if ((!empty($groupId) || !empty($groupUrn)) && !empty($objectUrn)) {
                /** @var UserEntitlementsContextObject $UserEntitlementsContextObject */
                $UserEntitlementsContextObject = CoreLogic::getObject('UserEntitlementsContextObject');
                $UserEntitlementsContextObject->setGroupId($groupId);
                $UserEntitlementsContextObject->setGroupUrn($groupUrn);
                $UserEntitlementsContextObject->setObjectUrn($objectUrn);
            }

            /** @var array $entitlements */
            $entitlements = !empty($type) ? self::$UserEntitlementService->getEntitlementByType($type, $UserEntitlementsContextObject) : self::$UserEntitlementService->getEntitlements($UserEntitlementsContextObject);

            /** set data */
            CoreApi::setData('entitlements', $entitlements);

        }else{

            CoreNotification::set('Not authorized to view entitlements', CoreNotification::ERROR);

        }

    }

    /**
     * Register system groups
     */
    private static function registerSystemGroups(){

        /**
         * Create security group for full system admins
         */

        /** @var UserSystemGroupObject $UserSystemGroupObject */
        $UserSystemGroupObject = CoreLogic::getObject('UserSystemGroupObject');
        $UserSystemGroupObject->setUrn(self::SYSTEM_GROUP_FULL_SYSTEM_ADMINS);
        $UserSystemGroupObject->setSuper(true);
        $UserSystemGroupObject->setName(CoreLanguage::get('system.group.full.admin.name'));
        $UserSystemGroupObject->setDescription(CoreLanguage::get('system.group.full.admin.description'));
        $UserSystemGroupObject->setCheckMember(function(UserObject $userObject){
            if(!$userObject) return false;
            return ($userObject->getId() == DEFAULT_AUTO_INCREMENT_BASE);
        });
        $UserSystemGroupObject->setCheckOwner(function(UserObject $userObject){
            if(!$userObject) return false;
            return ($userObject->getId() == DEFAULT_AUTO_INCREMENT_BASE);
        });
        $UserSystemGroupObject->addEntitlement(self::$UserEntitlementService->getEntitlement(self::ENTITLEMENT_FULL_SYSTEM_ADMIN));
        self::$UserGroupService->addSystemGroup($UserSystemGroupObject);

        /**
         * Create security group for registered users
         */
        $UserSystemGroupObject = CoreLogic::getObject('UserSystemGroupObject');
        $UserSystemGroupObject->setUrn(self::SYSTEM_GROUP_REGISTERED_USERS);
        $UserSystemGroupObject->setName(CoreLanguage::get('system.group.registered.users.name'));
        $UserSystemGroupObject->setDescription(CoreLanguage::get('system.group.registered.users.description'));
        $UserSystemGroupObject->setCheckMember(function(UserObject $userObject){
            return ($userObject != null);
        });
        $UserSystemGroupObject->setCheckOwner(function(UserObject $userObject){
            return false;
        });
        self::$UserGroupService->addSystemGroup($UserSystemGroupObject);

        /**
         * Create security group for guests users
         */
        $UserSystemGroupObject = CoreLogic::getObject('UserSystemGroupObject');
        $UserSystemGroupObject->setUrn(self::SYSTEM_GROUP_GUESTS);
        $UserSystemGroupObject->setName(CoreLanguage::get('system.group.guests.name'));
        $UserSystemGroupObject->setDescription(CoreLanguage::get('system.group.guests.description'));
        $UserSystemGroupObject->setCheckMember(function(UserObject $userObject){
            return ($userObject == null);
        });
        $UserSystemGroupObject->setCheckOwner(function(UserObject $userObject){
            return false;
        });
        self::$UserGroupService->addSystemGroup($UserSystemGroupObject);

    }

    /**
     * Register entitlements
     */
    private static function registerEntitlements(){

        /**
         * Add the full system admin entitlement
         */

        /** @var UserEntitlementObject $FullSystemUserEntitlementObject */
        $FullSystemUserEntitlementObject = CoreLogic::getObject('UserEntitlementObject');
        $FullSystemUserEntitlementObject->setUrn(self::ENTITLEMENT_FULL_SYSTEM_ADMIN);
        $FullSystemUserEntitlementObject->setName(CoreLanguage::get('user.entitlement.full.sys.admin.name'));
        $FullSystemUserEntitlementObject->setDescription(CoreLanguage::get('user.entitlement.full.sys.admin.description'));
        $FullSystemUserEntitlementObject->addParent(null);
        $FullSystemUserEntitlementObject->setType(self::ENTITLEMENT_TYPE_SYSTEM);

        /** register system admin */
        self::$UserEntitlementService->addEntitlement($FullSystemUserEntitlementObject);


        /**
         * Add the system admin entitlement
         */

        /** @var UserEntitlementObject $SystemUserEntitlementObject */
        $SystemUserEntitlementObject = CoreLogic::getObject('UserEntitlementObject');
        $SystemUserEntitlementObject->setUrn(self::ENTITLEMENT_SYSTEM_ADMIN);
        $SystemUserEntitlementObject->setName(CoreLanguage::get('user.entitlement.sys.admin.name'));
        $SystemUserEntitlementObject->setDescription(CoreLanguage::get('user.entitlement.sys.admin.description'));
        $SystemUserEntitlementObject->addParent($FullSystemUserEntitlementObject);
        $SystemUserEntitlementObject->setType(self::ENTITLEMENT_TYPE_SYSTEM);

        /** register system admin */
        self::$UserEntitlementService->addEntitlement($SystemUserEntitlementObject);


        /**
         * Add the manage users entitlement
         */

        /** @var UserEntitlementObject $ManageUsersUserEntitlementObject */
        $ManageUsersUserEntitlementObject = CoreLogic::getObject('UserEntitlementObject');
        $ManageUsersUserEntitlementObject->setUrn(self::ENTITLEMENT_MANAGE_USERS);
        $ManageUsersUserEntitlementObject->setName(CoreLanguage::get('user.entitlement.manage.users.name'));
        $ManageUsersUserEntitlementObject->setDescription(CoreLanguage::get('user.entitlement.manage.users.description'));
        $ManageUsersUserEntitlementObject->addParent($FullSystemUserEntitlementObject);
        $ManageUsersUserEntitlementObject->setType(self::ENTITLEMENT_TYPE_SYSTEM);

        /** register system admin */
        self::$UserEntitlementService->addEntitlement($ManageUsersUserEntitlementObject);


        /**
         * Add mapTable entitlements
         */

        /** @var UserEntitlementObject $UserCreateEntitlementObject */
        $UserCreateEntitlementObject = CoreLogic::getObject('UserEntitlementObject');
        $UserCreateEntitlementObject->setUrn(self::ENTITLEMENT_MAPTABLE_CREATE);
        $UserCreateEntitlementObject->setName('Create Records');
        $UserCreateEntitlementObject->setDescription('Ability to create records');
        $UserCreateEntitlementObject->addParent($FullSystemUserEntitlementObject);
        $UserCreateEntitlementObject->setType(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT);
        self::$UserEntitlementService->addEntitlement($UserCreateEntitlementObject);

        /** @var UserEntitlementObject $UserEditEntitlementObject */
        $UserEditEntitlementObject = CoreLogic::getObject('UserEntitlementObject');
        $UserEditEntitlementObject->setUrn(self::ENTITLEMENT_MAPTABLE_EDIT);
        $UserEditEntitlementObject->setName('Edit Records');
        $UserEditEntitlementObject->setDescription('Ability to edit records');
        $UserEditEntitlementObject->addParent($UserCreateEntitlementObject);
        $UserEditEntitlementObject->addParent($FullSystemUserEntitlementObject);
        $UserEditEntitlementObject->setType(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT);
        self::$UserEntitlementService->addEntitlement($UserEditEntitlementObject);

        /** @var UserEntitlementObject $UserDeleteEntitlementObject */
        $UserDeleteEntitlementObject = CoreLogic::getObject('UserEntitlementObject');
        $UserDeleteEntitlementObject->setUrn(self::ENTITLEMENT_MAPTABLE_DELETE);
        $UserDeleteEntitlementObject->setName('Delete Records');
        $UserDeleteEntitlementObject->setDescription('Ability to delete records');
        $UserDeleteEntitlementObject->addParent($FullSystemUserEntitlementObject);
        $UserDeleteEntitlementObject->addParent($UserCreateEntitlementObject);
        $UserDeleteEntitlementObject->setType(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT);
        self::$UserEntitlementService->addEntitlement($UserDeleteEntitlementObject);

        /** @var UserEntitlementObject $UserContributeEntitlementObject */
        $UserContributeEntitlementObject = CoreLogic::getObject('UserEntitlementObject');
        $UserContributeEntitlementObject->setUrn(self::ENTITLEMENT_MAPTABLE_CONTRIBUTE);
        $UserContributeEntitlementObject->setName('Contribute Records');
        $UserContributeEntitlementObject->setDescription('Ability to contribute records');
        $UserContributeEntitlementObject->addParent($FullSystemUserEntitlementObject);
        $UserContributeEntitlementObject->setType(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT);
        self::$UserEntitlementService->addEntitlement($UserContributeEntitlementObject);

        /** @var UserEntitlementObject $UserEntitlementObject */
        $UserViewEntitlementObject = CoreLogic::getObject('UserEntitlementObject');
        $UserViewEntitlementObject->setUrn(self::ENTITLEMENT_MAPTABLE_VIEW);
        $UserViewEntitlementObject->setName('View Records');
        $UserViewEntitlementObject->setDescription('Ability to view records');
        $UserViewEntitlementObject->addParent($UserContributeEntitlementObject);
        $UserViewEntitlementObject->addParent($UserDeleteEntitlementObject);
        $UserViewEntitlementObject->addParent($UserEditEntitlementObject);
        $UserViewEntitlementObject->addParent($UserCreateEntitlementObject);
        $UserViewEntitlementObject->setType(self::ENTITLEMENT_TYPE_MAPTABLE_OBJECT);
        self::$UserEntitlementService->addEntitlement($UserViewEntitlementObject);

    }

    /**
     * Add maptable mappings
     */
    private static function addFieldMappings(){

        /** @var MapTableMapColumnObject $MapTableMapColumnObject */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('userpicker');
        $MapTableMapColumnObject->setAppendMatch('user_id');
        $MapTableMapColumnObject->setDataTypeMatch('/^int/');
        $MapTableMapColumnObject->setInputTemplate('formfieldpersonpicker');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /** @var MapTableMapColumnObject $MapTableMapColumnObject */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('emailaddress');
        $MapTableMapColumnObject->setAppendMatch(array('email', 'email_value'));
        $MapTableMapColumnObject->setDataTypeMatch('/^varchar/');
        $MapTableMapColumnObject->setInputTemplate('forminputtext');
        $MapTableMapColumnObject->setFieldTemplate('formfieldflexible');
        self::$MapTableService->addMapping($MapTableMapColumnObject);

        /** @var MapTableMapColumnObject $MapTableMapColumnObject */
        $MapTableMapColumnObject = CoreLogic::getObject('MapTableMapColumnObject');
        $MapTableMapColumnObject->setId('userentitlements');
        $MapTableMapColumnObject->setAppendMatch('entitlements');
        $MapTableMapColumnObject->setDataTypeMatch('/^text/');
        $MapTableMapColumnObject->setInputTemplate('formfieldentitlements');
        $MapTableMapColumnObject->setFieldTemplate('formfieldnaked');
        $MapTableMapColumnObject->setOptionMapper(function(MapTableColumnObject $MapTableColumnObject, MapTableContextObject $MapTableContextObject){

            $options = array();

            /** @var array $entitlements */
            $entitlements = self::$UserEntitlementService->getEntitlementByType(self::ENTITLEMENT_TYPE_SYSTEM);

            $size = sizeof($entitlements);
            $inc = 0;

            /** @var UserEntitlementObject $UserEntitlementObject */
            foreach($entitlements as $UserEntitlementObject){

                $FormFieldOption = new FormFieldOption();
                $FormFieldOption->setKey($UserEntitlementObject->getUrn());
                $FormFieldOption->setValue($UserEntitlementObject->getName());
                $FormFieldOption->setDescription($UserEntitlementObject->getDescription());

                $FormFieldOption->childUrns = self::$UserEntitlementService->getEntitlementChildrenUrns($UserEntitlementObject->getUrn());

                $FormFieldOption->first = ($inc == 0);
                $inc++;
                $FormFieldOption->last = ($size == $inc);

                array_push($options, $FormFieldOption);

            }

            return $options;

        });
        self::$MapTableService->addMapping($MapTableMapColumnObject);

    }

    /**
     * Set redirect upon authentication
     */
    public static function setRedirectUponAuthentication(){

        /**
         * Make sure we redirect to the requested page
         */
        if(isset($_SESSION[self::CONST_REQUESTED_URI_SESSION_KEY]) && !empty($_SESSION[self::CONST_REQUESTED_URI_SESSION_KEY])){
            CoreHeaders::setRedirect($_SESSION[self::CONST_REQUESTED_URI_SESSION_KEY]);
            unset($_SESSION[self::CONST_REQUESTED_URI_SESSION_KEY]);
        }else{
            CoreHeaders::setRedirect('/');
        }

    }

    /**
     * Handle Unauthorized Exception
     */
    public static function handleUnauthorized(){

        /**
         * Save requested location
         * exclude api
         */
        if(CoreController::$currentRoute->getGroup() == CoreControllerObject::GROUP_PAGE || CoreController::$currentRoute->getGroup() == CoreControllerObject::GROUP_STREAM){
            $_SESSION[self::CONST_REQUESTED_URI_SESSION_KEY] = $_SERVER[CoreController::SERVER_REQUEST_URI];
        }

        /**
         * Set the redirect header
         */
        CoreHeaders::setRedirect('/login');

    }

    /**
     * Set current user on render template
     *
     * @param $return
     * @param null $params
     * @return mixed
     */
	public static function setCurrentUser($return, $params = null){
		$UserObject = self::$UserService->setCurrentUser();
		CoreRender::setData('user', $UserObject);
        return $return;
	}

    /**
     * Set remembered user on render template
     *
     * @param $return
     * @param null $params
     * @return mixed
     */
    public static function setRememberedUser($return, $params = null){
        if(false !== ($userId = CoreVisitor::retrieve(UserProcedure::VISITOR_DATA_USERID))){
            /** @var UserRemeberedObject $UserRemeberedObject */
            $UserRemeberedObject = CoreLogic::getObject('UserRemeberedObject');
            $UserRemeberedObject->setId($userId);
            $UserRemeberedObject->setUsername(CoreVisitor::retrieve(UserProcedure::VISITOR_DATA_USERNAME));
            $UserRemeberedObject->setEmail(CoreVisitor::retrieve(UserProcedure::VISITOR_DATA_EMAIL));
            $UserRemeberedObject->setFirstName(CoreVisitor::retrieve(UserProcedure::VISITOR_DATA_FIRST_NAME));
            $UserRemeberedObject->setLastName(CoreVisitor::retrieve(UserProcedure::VISITOR_DATA_LAST_NAME));
            self::$RememberedUserObject = $UserRemeberedObject;
            CoreRender::setData(self::REMEMBERED_USER_VIEW_KEY, $UserRemeberedObject);
        }
        return $return;
    }

    /**
     * Login user with access token
     *
     * @param array $param
     */
    public static function accessToken($param = array()){
        $accessToken = isset($param[1]) ? $param[1] : null;
        if(false !== self::$UserService->loginWithAccessToken($accessToken)){
            CoreHeaders::setFallbackRedirect('/');
        }else{
            CoreHeaders::setRedirect('/login');
        }
    }

    /**
     * @param array $param
     */
    public static function getUser($param = array()){

        //get user object
        $UserObject = self::$UserService->getUser(isset($param[1]) ? $param[1] : 0);

        //set data
        CoreApi::setData('user', $UserObject);

    }

    /**
     * Create object group entitlement
     *
     * @param array $param
     */
    public static function createObjectGroupEntitlement($param = array()){

        /** @var bool $access */
        $access = self::$UserEntitlementService->userHasEntitlement(CoreUser::getUser(), self::$UserEntitlementService->getEntitlement(UserModule::ENTITLEMENT_FULL_SYSTEM_ADMIN));

        if($access) {

            if(empty($_POST)) CoreLog::error('Need POST for method ' . __FUNCTION__);

            $groupid = isset($_POST['group_id']) ? $_POST['group_id'] : false;
            $groupurn = isset($_POST['group_urn']) ? $_POST['group_urn'] : false;
            $objecturn = isset($_POST['object_urn']) ? $_POST['object_urn'] : false;
            $entitlementurn = isset($_POST['entitlement_urn']) ? $_POST['entitlement_urn'] : false;

            $result = self::$UserEntitlementService->addGroupObjectEntitlement($groupid, $groupurn, $objecturn, $entitlementurn);

            CoreApi::setData('response', $result);

        }else{

            CoreNotification::set('You are not authorized to create object group entitlements', CoreNotification::ERROR);

        }

    }

    /**
     * Delete object group entitlement
     *
     * @param array $param
     */
    public static function deleteObjectGroupEntitlements($param = array()){

        /** @var bool $access */
        $access = self::$UserEntitlementService->userHasEntitlement(CoreUser::getUser(), self::$UserEntitlementService->getEntitlement(UserModule::ENTITLEMENT_FULL_SYSTEM_ADMIN));

        if($access) {

            if(empty($_POST)) CoreLog::error('Need POST for method ' . __FUNCTION__);

            $groupid = isset($_POST['group_id']) ? $_POST['group_id'] : false;
            $groupurn = isset($_POST['group_urn']) ? $_POST['group_urn'] : false;
            $objecturn = isset($_POST['object_urn']) ? $_POST['object_urn'] : false;

            $result = self::$UserEntitlementService->removeGroupObjectEntitlements($groupid, $groupurn, $objecturn);

            CoreApi::setData('response', $result);

        }else{

            CoreNotification::set('You are not authorized to delete object group entitlements', CoreNotification::ERROR);

        }
    }

    /**
     * Get a single group
     *
     * @param array $param
     */
    public static function getGroup($param = array()){

        /** @var bool $access */
        $access = self::$UserEntitlementService->userHasEntitlement(CoreUser::getUser(), self::$UserEntitlementService->getEntitlement(UserModule::ENTITLEMENT_MANAGE_USERS));

        if($access) {

            /** @var array $groups */
            $group = self::$UserGroupService->getGroup(isset($param[1]) ? $param[1] : 0);

            //set data
            CoreApi::setData('group', $group);

        }else{

            CoreNotification::set('You are not authorized to view group', CoreNotification::ERROR);

        }
    }

    /**
     * Get groups
     *
     * @param array $param
     */
    public static function getGroups($param = array()){

        /** @var bool $access */
        $access = self::$UserEntitlementService->userHasEntitlement(CoreUser::getUser(), self::$UserEntitlementService->getEntitlement(UserModule::ENTITLEMENT_MANAGE_USERS));

        if($access) {

            /** @var array $groups */
            $groups = self::$UserGroupService->getGroups();

            //set data
            CoreApi::setData('groups', $groups);

        }else{

            CoreNotification::set('You are not authorized to view group', CoreNotification::ERROR);

        }

    }

    /**
     * Email exists
     *
     * @param array $param
     */
    public static function emailExists($param = array()){
        $email = isset($param[1]) ? urldecode($param[1]) : false;
        $user = self::$UserService->getUserByEmail($email);
        CoreApi::setData('existingUser', !empty($user));

    }

    /**
     * Username exists
     *
     * @param array $param
     */
    public static function usernameExists($param = array()){
        $username = isset($param[1]) ? urldecode($param[1]) : false;
        $user = self::$UserService->getUserByUsername($username);
        CoreApi::setData('existingUser', !empty($user));
    }

    /**
     * Get users
     *
     * @param array $param
     */
    public static function getUsers($param = array()){

        //set query
        $query = (isset($_GET['q'])) ? $_GET['q'] : null;
        $start = (isset($_GET['start'])) ? (int)$_GET['start'] : 0;
        $limit = (isset($_GET['limit'])) ? (int)$_GET['limit'] : 20;

        //user search object
        $UserSearchObject = new UserSearchObject();
        $UserSearchObject->setQuery($query);
        $UserSearchObject->setStart($start);
        $UserSearchObject->setLimit($limit);

        //get users
        $users = self::$UserService->getUsers($UserSearchObject);

        //set data
        CoreApi::setData('users', $users);

    }

	/**
	 * Perform logout
	 */
	public static function logout($params = array()){

     	/** Do the logout */
		self::$UserService->logout();

        /** try to go to requested page - will redirect to /login if unauthorized */
        if(isset($_SESSION[self::CONST_REQUESTED_URI_SESSION_KEY]) && !empty($_SESSION[self::CONST_REQUESTED_URI_SESSION_KEY])){
            CoreHeaders::setRedirect($_SESSION[self::CONST_REQUESTED_URI_SESSION_KEY]);
            unset($_SESSION[self::CONST_REQUESTED_URI_SESSION_KEY]);

        /** otherwise go to the landing page */
        }else{
            CoreHeaders::setRedirect('/');
        }

	}

    /**
     * Resend activation email
     */
    public static function resendActivationEmail(){
        CoreApi::setData('resentActivationEmail', self::$UserService->resendActivationEmail());
    }

    /**
     * Send email activation reminder
     */
    public static function sendActivationReminders(){
        self::$UserService->sendEmailActivationReminders();
    }

    /**
     * Run on update
     *
     * @param $previousVersion
     * @param $newVersion
     *
     * @return void
     */
    public static function __update__($previousVersion, $newVersion)
    {

    }

    /**
     * Run on enable
     *
     * @return void
     */
    public static function __enable__()
    {

    }

    /**
     * Run on disable
     *
     * @return mixed
     */
    public static function __disable__()
    {

    }

    /**
     * Run on install
     *
     */
    public static function __install__()
    {

        /** @var UserService $UserService */
        $UserService = CoreLogic::getService('UserService');

        /** @var UserTemplateObject $UserTemplateObject */
        $UserTemplateObject = CoreLogic::getObject('UserTemplateObject');

        /** admin user template */
        $UserTemplateObject->setUsername('admin');
        $UserTemplateObject->setEmail('b@ukora.com');
        $UserTemplateObject->setPassword('admin');
        $UserTemplateObject->setFirstName('Admin');
        $UserTemplateObject->setLastName('Admin');

        /** create admin user */
        $UserService->create($UserTemplateObject);

    }
	
}