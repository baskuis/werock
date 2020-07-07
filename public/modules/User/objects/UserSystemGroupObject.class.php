<?php

/**
 * User System Group
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserSystemGroupObject {

    /** @var string $urn */
    public $urn;

    /** @var string $name */
    public $name;

    /** @var string $description */
    public $description;

    /** @var callable $checkMember */
    public $checkMember;

    /** @var callable $checkOwner */
    public $checkOwner;

    /** @var array $entitlements */
    public $entitlements = array();

    /** @var bool $super Is this a super group */
    public $super = false;

    /**
     * Is User a member of this group
     *
     * @param UserObject $userObject
     * @return bool
     */
    public function isMember(UserObject $userObject){
        if(is_callable($this->checkMember)){
            $check = $this->checkMember;
            return $check($userObject);
        }
        CoreLog::debug('No checkMember method defined');
        return false;
    }

    /**
     * Is User an owner of this group
     *
     * @param UserObject $userObject
     * @return bool
     */
    public function isOwner(UserObject $userObject){
        if(is_callable($this->checkOwner)){
            $check = $this->checkOwner;
            return $check($userObject);
        }
        CoreLog::debug('No checkOwner method defined');
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
        }
        return false;
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
     * @param callable $checkMember
     */
    public function setCheckMember($checkMember)
    {
        $this->checkMember = $checkMember;
    }

    /**
     * @param callable $checkOwner
     */
    public function setCheckOwner($checkOwner)
    {
        $this->checkOwner = $checkOwner;
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

    /**
     * @return string
     */
    public function getUrn()
    {
        return $this->urn;
    }

    /**
     * @param string $urn
     */
    public function setUrn($urn)
    {
        $this->urn = $urn;
    }

    /**
     * @return boolean
     */
    public function isSuper()
    {
        return $this->super;
    }

    /**
     * @param boolean $super
     */
    public function setSuper($super)
    {
        $this->super = $super;
    }

}