<?php

/**
 * Load configuration
 */
require(__DIR__ . '/configuration.php');

/**
 * Show errors in DEV_MODE
 */
ini_set('display_errors', (DEV_MODE ? E_ALL : 0));

/**
 * Interfaces
 */
require(__DIR__ . '/controller/ControllerException.interface.php');
require(__DIR__ . '/module/CoreModuleInterface.interface.php');
require(__DIR__ . '/render/CoreRenderTemplate.interface.php');
require(__DIR__ . '/user/CoreUserObject.interface.php');

/**
 * Load Utils
 */
require(__DIR__ . '/tools/CoreArrayUtils.class.php');
require(__DIR__ . '/tools/CoreStringUtils.class.php');
require(__DIR__ . '/tools/CoreJsonUtils.class.php');
require(__DIR__ . '/tools/CoreFilesystemUtils.class.php');
require(__DIR__ . '/tools/CoreHtmlUtils.class.php');
require(__DIR__ . '/tools/CoreSqlUtils.class.php');
require(__DIR__ . '/tools/CoreSysUtils.class.php');
require(__DIR__ . '/tools/CoreSessionUtils.class.php');
require(__DIR__ . '/tools/CoreUriUtils.class.php');
require(__DIR__ . '/tools/CoreSecUtils.class.php');
require(__DIR__ . '/tools/CorePerformanceUtils.class.php');
require(__DIR__ . '/tools/CoreMathUtils.class.php');
require(__DIR__ . '/tools/CoreLanguageUtils.class.php');
require(__DIR__ . '/tools/CoreGeoUtils.class.php');
require(__DIR__ . '/tools/CoreEncryptionUtils.class.php');
require(__DIR__ . '/tools/CoreGitUtils.class.php');
require(__DIR__ . '/tools/CoreRemoteUtils.class.php');
require(__DIR__ . '/tools/CoreObjectUtils.class.php');
require(__DIR__ . '/tools/CoreImageUtils.class.php');
require(__DIR__ . '/tools/CoreHashUtils.class.php');
require(__DIR__ . '/tools/CoreColorUtils.class.php');

/**
 * Class loader
 */
require(__DIR__ . '/init/CoreClassLoader.class.php');

/**
 * Error handler
 */
require(__DIR__ . '/errors/CoreErrors.class.php');

/**
 * Core Errors
 * Allows more helpful error handling
 * can be disabled per configuration
 */
CoreErrors::init();

/**
 * Interceptor pattern support
 */
require(__DIR__ . '/interceptor/CoreInterceptorObject.class.php');
require(__DIR__ . '/interceptor/CoreInterceptor.class.php');
require(__DIR__ . '/interceptor/CoreInterceptorTrait.class.php');

/**
 * Reflection trait
 */
require(__DIR__ . '/reflection/ClassReflection.trait.php');

/**
 * Api
 */
require(__DIR__ . '/api/CoreApi.class.php');

/**
 * Load core menu
 */
require(__DIR__ . '/menu/CoreMenu.class.php');
require(__DIR__ . '/menu/CoreMenuObject.class.php');

/**
 * Load components
 */
require(__DIR__ . '/data/CoreData.class.php');
require(__DIR__ . '/security/CoreSecurityApiException.class.php');
require(__DIR__ . '/security/CoreSecurity.class.php');
require(__DIR__ . '/init/CoreInit.class.php');
require(__DIR__ . '/init/CoreInitAssetsReferenceObject.class.php');

/**
 * Schema
 */
require(__DIR__ . '/schema/CoreSchema.class.php');
require(__DIR__ . '/schema/CoreSchemaTableObject.class.php');
require(__DIR__ . '/schema/CoreSchemaTableColumnObject.class.php');
require(__DIR__ . '/schema/CoreSchemaTableKeyObject.class.php');
require(__DIR__ . '/schema/CoreSchemaTableIndexObject.class.php');
require(__DIR__ . '/schema/CoreSchemaTableStatusObject.class.php');
require(__DIR__ . '/schema/CoreSchemaTableNotFoundException.class.php');

/**
 * Plugins
 */
require(__DIR__ . '/plugin/CorePlugin.class.php');
require(__DIR__ . '/plugin/CorePluginObject.class.php');

/**
 * Modules
 */
require(__DIR__ . '/module/CoreModule.class.php');
require(__DIR__ . '/module/CoreModuleObject.class.php');

/**
 * Logic
 */
require(__DIR__ . '/logic/CoreLogic.class.php');
require(__DIR__ . '/logic/CoreLogicObject.class.php');

/**
 * Crutch loader
 */
require(__DIR__ . '/crutches/CoreCrutchObject.class.php');
require(__DIR__ . '/crutches/CoreCrutches.class.php');

/**
 * Error handler
 */
require(__DIR__ . '/log/CoreLogObject.class.php');
require(__DIR__ . '/log/CoreLog.class.php');

/**
 * Observer
 */
require(__DIR__ . '/observer/CoreObserverObject.class.php');
require(__DIR__ . '/observer/CoreObserver.class.php');

/**
 * Rendering helper
 */
require(__DIR__ . '/render/CoreRender.class.php');
require(__DIR__ . '/render/CoreRenderTemplate.class.php');

/**
 * Core response
 */
require(__DIR__ . '/response/CoreResponse.class.php');

/**
 * Pagination
 */
require(__DIR__ . '/pagination/CorePagination.class.php');
require(__DIR__ . '/pagination/CorePaginationObject.class.php');

/**
 * Schedule
 */
require(__DIR__ . '/schedule/CoreScheduleJobObject.class.php');
require(__DIR__ . '/schedule/CoreSchedule.class.php');

/**
 * Rte
 */
require(__DIR__ . '/filters/CoreFilters.class.php');
require(__DIR__ . '/macros/CoreMacros.class.php');

/**
 *  CSS resources
 */
require(__DIR__ . '/styles/CSSAsset.class.php');
require(__DIR__ . '/styles/less/CoreLess.class.php');
require(__DIR__ . '/styles/scss/CoreSCSS.class.php');
require(__DIR__ . '/styles/CoreStyles.class.php');

require(__DIR__ . '/resources/CoreResources.class.php');
require(__DIR__ . '/template/CoreTemplate.class.php');
require(__DIR__ . '/template/CoreTemplateObject.class.php');

/**
 * Forms
 */
require(__DIR__ . '/form/CoreForm.class.php');
require(__DIR__ . '/form/CoreFormObject.class.php');
require(__DIR__ . '/form/ui/FormUI.class.php');
require(__DIR__ . '/form/ui/FormField.php');
require(__DIR__ . '/form/ui/FormFieldOption.class.php');

/**
 * Session
 */
require(__DIR__ . '/session/CoreSession.class.php');
require(__DIR__ . '/session/CoreSessionHandler.class.php');

require(__DIR__ . '/notification/CoreNotification.class.php');
require(__DIR__ . '/visitor/CoreVisitor.class.php');
require(__DIR__ . '/visitor/CoreVisitorObject.class.php');
require(__DIR__ . '/user/CoreUser.class.php');
require(__DIR__ . '/cache/CoreCache.class.php');
require(__DIR__ . '/language/CoreLanguage.class.php');
require(__DIR__ . '/script/CoreScript.class.php');
require(__DIR__ . '/prop/CoreProp.class.php');
require(__DIR__ . '/fetemplate/CoreFeTemplate.class.php');
require(__DIR__ . '/store/CoreStore.class.php');

/**
 * Headers
 */
require(__DIR__ . '/headers/CoreHeaders.class.php');

/**
 * Core toMethod
 */
require(__DIR__ . '/controller/CoreControllerObject.class.php');
require(__DIR__ . '/controller/CoreController.class.php');