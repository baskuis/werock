<?php

/** @var UserEntitlementService $UserEntitlementManager */
$UserEntitlementManager = CoreLogic::getService('UserEntitlementService');

/** @var bool $entitlementAccess */
$entitlementAccess = $UserEntitlementManager->userHasEntitlement(CoreUser::getUser(), $UserEntitlementManager->getEntitlement(UserModule::ENTITLEMENT_FULL_SYSTEM_ADMIN));

/**
 * Add show permissions button + entitlement picker to parent template
 */
if($entitlementAccess) {
    $view = preg_replace('/\<\/div\>$/i', '
    ' . CoreTemplate::getView('objectentitlementspicker') . '
    {{^suppressEntitlementPicker}}
    <a class="show-permissions" data-toggle="modal" href="#object_entitlements_modal">
        <i class="fa fa-key"></i>
    </a>
    {{/suppressEntitlementPicker}}
</div>', $view);
}

/** @var boolean $canDelete */
$canCreate = (bool) $UserEntitlementManager->userHasObjectEntitlement(
    CoreUser::getUser(),
    'maptable.object.' . $data['MapTableContextObject']->getMapTableTableObject()->getName(),
    $UserEntitlementManager->getEntitlement('maptable:create'));

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
    if(!$canCreate){
        $view = '<div class="nocreate">' . $view . '</div>';
    }

    /** neuter if not view access */
    if(!$canView){
        $view = '<div class="noview">' . $view . '</div>';
    }

}