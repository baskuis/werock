<?php

/**
 * User entitlement service
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserEntitlementService implements UserEntitlementServiceInterface {

    /**
     * Entitlements
     *
     * @var array
     */
    private $entitlements = array();

    /** @var UserEntitlementProcedure $UserEntitlementProcedure */
    private $UserEntitlementProcedure;

    /** @var UserGroupService $UserGroupService */
    private $UserGroupService;

    function __construct(){
        $this->UserEntitlementProcedure = CoreLogic::getProcedure('UserEntitlementProcedure');
        $this->UserGroupService = CoreLogic::getService('UserGroupService');
    }

    /**
     * Add entitlement
     *
     * @param UserEntitlementObject $UserEntitlementObject
     * @return bool
     */
    public function addEntitlement(UserEntitlementObject $UserEntitlementObject){
        if(isset($this->entitlements[$UserEntitlementObject->getUrn()])){
            CoreLog::debug('Overloading entitlement by name: ' . $UserEntitlementObject->getName());
        }
        $this->entitlements[$UserEntitlementObject->getUrn()] = $UserEntitlementObject;
        return true;
    }

    /**
     * Add group object entitlement
     *
     * @param null $groupid
     * @param null $groupurn
     * @param null $objecturn
     * @param null $entitlementurn
     */
    public function addGroupObjectEntitlement($groupid = null, $groupurn = null, $objecturn = null, $entitlementurn = null){
        return $this->UserEntitlementProcedure->addGroupObjectEntitlement($groupid, $groupurn, $objecturn, $entitlementurn);
    }

    /**
     * Remove group object entitlements
     *
     * @param null $groupid
     * @param null $groupurn
     * @param null $objecturn
     * @param null $entitlementurn
     */
    public function removeGroupObjectEntitlements($groupid = null, $groupurn = null, $objecturn = null){
        return $this->UserEntitlementProcedure->removeGroupObjectEntitlements($groupid, $groupurn, $objecturn);
    }

    /**
     * See if user has an entitlement
     *
     * @param UserObject $userObject
     * @param UserEntitlementObject $userEntitlementObject
     */
    public function userHasEntitlement($userObject, UserEntitlementObject $userEntitlementObject){

        if(!$userObject) return false;

        /** @var array $groups */
        $groups = $this->UserGroupService->getGroups();

        if(!empty($groups)) {

            /** @var UserGroupObject $group */
            foreach ($groups as $group) {
                if ($group->isMember($userObject) || $group->isOwner($userObject)) {

                    /** @var array $groupEntitlements */
                    $groupEntitlements = $group->getEntitlements();

                    /** @var UserEntitlementObject $entitlement */
                    foreach ($groupEntitlements as $entitlement) {

                        /** check for group entitlement match */
                        if ($entitlement->getUrn() == $userEntitlementObject->getUrn()) {
                            return true;
                        }

                        try {

                            /** @var array $children */
                            $children = $this->getEntitlementChildren($entitlement->getUrn());

                            if (!empty($children)) {
                                /** @var UserEntitlementObject $childEntitlement */
                                foreach ($children as $childEntitlement) {
                                    if ($childEntitlement->getUrn() == $userEntitlementObject->getUrn()) {
                                        return true;
                                    }
                                }
                            }

                        } catch (UserEntitlementNotFoundException $e) {

                            //suppress

                        }

                    }


                }
            }
        }

        return false;

    }

    /**
     * Get entitlements by type
     *
     * @param string $type
     * @param UserEntitlementsContextObject $UserEntitlementsContextObject
     * @return array
     */
    public function getEntitlementByType($type = null, $UserEntitlementsContextObject = null){
        $list = array();
        /** @var UserEntitlementObject $entitlement */
        foreach($this->entitlements as $entitlement){
            if($entitlement->getType() == $type){
                if($UserEntitlementsContextObject) $entitlement->isApplied = self::entitlementAppliesToContext($entitlement, $UserEntitlementsContextObject);
                $entitlement->childUrns = self::getEntitlementChildrenUrns($entitlement->getUrn());
                array_push($list, $entitlement);
            }
        }
        return $list;
    }

    /**
     * Entitlement applies to context
     *
     * @param UserEntitlementObject $entitlementObject
     * @param UserEntitlementsContextObject $UserEntitlementsContextObject
     * @return bool
     */
    public function entitlementAppliesToContext(UserEntitlementObject $entitlementObject, UserEntitlementsContextObject $UserEntitlementsContextObject){

        /** @var array $records */
        $records = $this->UserEntitlementProcedure->getObjectEntitlements($UserEntitlementsContextObject->getObjectUrn());
        if(!empty($records)){
            foreach($records as $record){
                if(!isset($record['werock_entitlement_type'])){ //sanity check
                    CoreLog::error('Unable to find werock_entitlement_type in record');
                }
                if($record['werock_entitlement_object_urn'] != $UserEntitlementsContextObject->getObjectUrn()){
                    continue;
                }
                if($record['werock_group_id'] != $UserEntitlementsContextObject->getGroupId()){
                    continue;
                }
                if($record['werock_group_urn'] != $UserEntitlementsContextObject->getGroupUrn()){
                    continue;
                }
                if($record['werock_entitlement_type'] == $entitlementObject->getUrn()){
                    return true;
                }
            }
        }

        return false;

    }

    /**
     * Simple shell for current user
     * Checks to see if user has entitlement
     *
     * @param null $entitlement
     * @return bool
     * @throws UserEntitlementNotFoundException
     */
    public function hasEntitlement($entitlement = null){
        return $this->userHasEntitlement(CoreUser::getUser(), $this->getEntitlement($entitlement));
    }

    /**
     * Simple shell for current user
     * Checks to see if user has object entitlement
     *
     * @param null $objecturn
     * @param null $entitlementurn
     * @return bool
     * @throws UserEntitlementNotFoundException
     */
    public function hasObjectEntitlement($objecturn = null, $entitlementurn = null){
        return $this->userHasObjectEntitlement(CoreUser::getUser(), $objecturn, $this->getEntitlement($entitlementurn));
    }

    /**
     * User has object entitlement
     *
     * @param UserObject $userObject
     * @param $objectUrn
     * @param UserEntitlementObject $userEntitlementObject
     */
    public function userHasObjectEntitlement($userObject, $objectUrn, UserEntitlementObject $userEntitlementObject){

        if(!$userObject) return false;

        /** check for overriding entitlement */
        if($this->userHasEntitlement($userObject, $userEntitlementObject)){
            return true;
        }

        /** @var array $groups */
        $groups = $this->UserGroupService->getGroups();

        /** @var array $records */
        $records = $this->UserEntitlementProcedure->getObjectEntitlements($objectUrn);

        /** @var UserGroupObject $group */
        foreach($groups as $group) {
            if ($group->isMember($userObject) || $group->isOwner($userObject)) {
                foreach($records as $record){
                    if($group->isSystem){
                        /** @var UserSystemGroupObject $group */
                        if($record['werock_group_urn'] == $group->getUrn() && $record['werock_entitlement_type'] == $userEntitlementObject->getUrn()){
                            return true;
                        }
                    }else{
                        /** @var UserGroupObject $group */
                        if($record['werock_group_id'] == $group->getId() && $record['werock_entitlement_type'] == $userEntitlementObject->getUrn()){
                            return true;
                        }
                    }

                }
            }
        }

        return false;

    }

    /**
     * Get entitlement by URN
     *
     * @param string $urn
     * @return mixed
     * @throws UserEntitlementNotFoundException
     */
    public function getEntitlement($urn = null){
        if(!isset($this->entitlements[$urn])){
            throw new UserEntitlementNotFoundException();
        }
        return $this->entitlements[$urn];
    }

    /**
     * Get entitlement children
     *
     * @param null $urn
     */
    public function getEntitlementChildren($urn = null){
        $children = array();
        /** @var UserEntitlementObject $entitlement */
        foreach($this->entitlements as $entitlement){
            if($entitlement->hasParentUrn($urn)){
                array_push($children, $entitlement);
            }
        }
        return $children;
    }

    /**
     * Get entitlement children
     * just the urns
     *
     * @param null $urn
     * @return array
     */
    public function getEntitlementChildrenUrns($urn = null){
        $urns = array();
        $children = $this->getEntitlementChildren($urn);
        if(!empty($children)){
            /** @var UserEntitlementObject $entitlement */
            foreach($children as $entitlement){
                if(!in_array($entitlement->getUrn(), $urns)){
                    array_push($urns, $entitlement->getUrn());
                }
            }
        }
        return $urns;
    }

    /**
     * Get entitlements
     *
     * @param UserEntitlementsContextObject $UserEntitlementsContextObject
     * @return array
     */
    public function getEntitlements($UserEntitlementsContextObject = null)
    {
        foreach($this->entitlements as &$entitlement){
            if($UserEntitlementsContextObject) $entitlement->isApplied = self::entitlementAppliesToContext($entitlement, $UserEntitlementsContextObject);
        }
        return $this->entitlements;
    }

}