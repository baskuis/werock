<?php

/**
 * User Entitlement Service
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
interface UserEntitlementServiceInterface {

    /**
     * Add entitlement
     *
     * @param UserEntitlementObject $UserEntitlementObject
     * @return bool
     */
    public function addEntitlement(UserEntitlementObject $UserEntitlementObject);

    /**
     * Add group object entitlement
     *
     * @param null $groupid
     * @param null $groupurn
     * @param null $objecturn
     * @param null $entitlementurn
     */
    public function addGroupObjectEntitlement($groupid = null, $groupurn = null, $objecturn = null, $entitlementurn = null);

    /**
     * Remove group object entitlements
     *
     * @param null $groupid
     * @param null $groupurn
     * @param null $objecturn
     * @param null $entitlementurn
     */
    public function removeGroupObjectEntitlements($groupid = null, $groupurn = null, $objecturn = null);

    /**
     * See if user has an entitlement
     *
     * @param UserObject $userObject
     * @param UserEntitlementObject $userEntitlementObject
     */
    public function userHasEntitlement($userObject, UserEntitlementObject $userEntitlementObject);

    /**
     * Get entitlements by type
     *
     * @param string $type
     * @param UserEntitlementsContextObject $UserEntitlementsContextObject
     * @return array
     */
    public function getEntitlementByType($type = null, $UserEntitlementsContextObject = null);

    /**
     * Entitlement applies to context
     *
     * @param UserEntitlementObject $entitlementObject
     * @param UserEntitlementsContextObject $UserEntitlementsContextObject
     * @return bool
     */
    public function entitlementAppliesToContext(UserEntitlementObject $entitlementObject, UserEntitlementsContextObject $UserEntitlementsContextObject);

    /**
     * User has object entitlement
     *
     * @param UserObject $userObject
     * @param $objectUrn
     * @param UserEntitlementObject $userEntitlementObject
     */
    public function userHasObjectEntitlement($userObject, $objectUrn, UserEntitlementObject $userEntitlementObject);

    /**
     * Get entitlement by URN
     *
     * @param string $urn
     * @return mixed
     * @throws UserEntitlementNotFoundException
     */
    public function getEntitlement($urn = null);

    /**
     * Get entitlement children
     *
     * @param null $urn
     */
    public function getEntitlementChildren($urn = null);

    /**
     * Get entitlements
     *
     * @param UserEntitlementsContextObject $UserEntitlementsContextObject
     * @return array
     */
    public function getEntitlements($UserEntitlementsContextObject = null);

    /**
     * Simple shell for current user
     * Checks to see if user has entitlement
     *
     * @param null $entitlement
     * @return bool
     * @throws UserEntitlementNotFoundException
     */
    public function hasEntitlement($entitlement = null);

    /**
     * Simple shell for current user
     * Checks to see if user has object entitlement
     *
     * @param null $objecturn
     * @param null $entitlementurn
     * @return bool
     * @throws UserEntitlementNotFoundException
     */
    public function hasObjectEntitlement($objecturn = null, $entitlementurn = null);

}