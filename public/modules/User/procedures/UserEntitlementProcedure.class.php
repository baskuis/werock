<?php

/**
 * User entitlement procedure
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserEntitlementProcedure {

    /** @var UserEntitlementRepository $UserEntitlementRepository */
    private $UserEntitlementRepository;

    function __construct(){
        $this->UserEntitlementRepository = CoreLogic::getRepository('UserEntitlementRepository');
    }

    /**
     * Add group object entitlement
     *
     * @param null $groupid
     * @param null $groupurn
     * @param null $objecturn
     * @param null $entitlementurn
     * @return Array
     */
    public function addGroupObjectEntitlement($groupid = null, $groupurn = null, $objecturn = null, $entitlementurn = null){
        return $this->UserEntitlementRepository->addGroupObjectEntitlement($groupid, $groupurn, $objecturn, $entitlementurn);
    }

    /**
     * Remove group object entitlement
     *
     * @param null $groupid
     * @param null $groupurn
     * @param null $objecturn
     * @param null $entitlementurn
     * @return Array
     */
    public function removeGroupObjectEntitlements($groupid = null, $groupurn = null, $objecturn = null){
        return $this->UserEntitlementRepository->deleteGroupObjectEntitlements($groupid, $groupurn, $objecturn);

    }

    /**
     * Get object group entitlements
     *
     * @param null $objecturn
     * @return Array
     */
    public function getObjectEntitlements($objecturn = null){
        return $this->UserEntitlementRepository->getObjectEntitlements($objecturn);
    }

}