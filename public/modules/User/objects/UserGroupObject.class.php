<?php

/**
 * User Group
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserGroupObject {

    public $id;
    public $name;
    public $description;
    public $added;

    /** @var array $members */
    public $members = array();

    /** @var array $owners */
    public $owners = array();

    /** @var array $entitlements */
    public $entitlements = array();

    /**
     * Is user a member of this group
     *
     * @param UserObject $userObject
     * @return bool
     */
    public function isMember(UserObject $userObject){
        if(empty($this->members)){
            CoreLog::debug('Group does not have members when isMember is called');
            return false;
        }

        if(empty($userObject)) return false;

        /** @var UserGroupMemberObject $member */
        foreach($this->members as $member){
            if($member->getUserObject() == null) continue;
            if($member->getUserObject()->getId() == $userObject->getId()){
                return true;
            }
        }
        return false;
    }

    /**
     * Is user an owner of this group
     *
     * @param UserObject $userObject
     * @return bool
     */
    public function isOwner(UserObject $userObject){
        if(empty($this->owners)){
            CoreLog::debug('Group does not have owners when isOwner is called');
            return false;
        }
        /** @var UserObject $member */
        foreach($this->owners as $owner){
            if($owner->getId() == $userObject->getId()){
                return true;
            }
        }
        return false;
    }

    /**
     * Add entitlement
     *
     * @param UserEntitlementObject $userEntitlementObject
     */
    public function addEntitlement(UserEntitlementObject $userEntitlementObject){
        array_push($this->entitlements, $userEntitlementObject);
    }

    /**
     * Check if group has an entitlement
     *
     * @param string $urn
     * @return bool
     */
    public function hasEntitlement($urn = null){
        /** @var UserEntitlementObject $entitlement */
        foreach($this->entitlements as $entitlement){
            if($entitlement->getUrn() == $urn) return true;
            if($entitlement->hasUrn($urn)) return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * @param mixed $added
     */
    public function setAdded($added)
    {
        $this->added = $added;
    }

    /**
     * @return array
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param array $members
     */
    public function setMembers($members)
    {
        $this->members = $members;
    }

    /**
     * @return array
     */
    public function getOwners()
    {
        return $this->owners;
    }

    /**
     * @param array $owners
     */
    public function setOwners($owners)
    {
        $this->owners = $owners;
    }

    /**
     * @return array
     */
    public function getEntitlements()
    {
        return $this->entitlements;
    }

    /**
     * @param array $entitlements
     */
    public function setEntitlements($entitlements)
    {
        $this->entitlements = $entitlements;
    }

}