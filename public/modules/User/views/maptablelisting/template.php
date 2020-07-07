<?php

/** @var UserEntitlementService $UserEntitlementManager */
$UserEntitlementManager = CoreLogic::getService('UserEntitlementService');

/** @var boolean $canDelete */
$canDelete = (bool) $UserEntitlementManager->userHasObjectEntitlement(
    CoreUser::getUser(),
    'maptable.object.' . $data['MapTableContextObject']->getMapTableTableObject()->getName(),
    $UserEntitlementManager->getEntitlement('maptable:delete'));

/** @var boolean $canView */
$canView = (bool) $UserEntitlementManager->userHasObjectEntitlement(
    CoreUser::getUser(),
    'maptable.object.' . $data['MapTableContextObject']->getMapTableTableObject()->getName(),
    $UserEntitlementManager->getEntitlement('maptable:view'));

/** only if entitlements can be picked */
if(false === (
        isset(CoreController::$currentAction->suppressEntitlementPicker) &&
        CoreController::$currentAction->suppressEntitlementPicker === true)
){

    /** modify display to hide delete button */
    if(!$canDelete){
        $view = '<div class="nodelete">' . $view . '</div>';
    }

    /** show no view access */
    if(!$canView){
        $view = '<div class="noview">no view access</div>';
    }

}else{

    /** @var UserEntitlementService $UserEntitlementManager */
    $UserEntitlementManager = CoreLogic::getService('UserEntitlementService');
    $canManageUsers = $UserEntitlementManager->userHasEntitlement(CoreUser::getUser(), $UserEntitlementManager->getEntitlement(UserModule::ENTITLEMENT_MANAGE_USERS));

    if(!$canManageUsers){
        $view = '<div class="noview">no view access</div>';
    }

}