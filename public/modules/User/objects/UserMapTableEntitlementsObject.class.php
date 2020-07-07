<?php

class UserMapTableEntitlementsObject {

    const ENTITLEMENT_CREATE = "create";
    const ENTITLEMENT_EDIT = "edit";
    const ENTITLEMENT_UPDATE = "update";
    const ENTITLEMENT_DELETE = "delete";

    /** @var string $object */
    public $object = null;

    public $availableEntitlements = array(
        self::ENTITLEMENT_CREATE,
        self::ENTITLEMENT_EDIT,
        self::ENTITLEMENT_UPDATE,
        self::ENTITLEMENT_DELETE
    );

    public $entitlements = array();

    public function UserMapTableEntitlementsObject(){

    }

    /**
     * Add entitlement
     *
     * @param null $entitlement
     * @return bool
     * @throws UserUnsupportedEntitlementException
     */
    public function addEntitlement($entitlement = null){

        if(!in_array($entitlement, $this->availableEntitlements)){
            throw new UserUnsupportedEntitlementException();
        }

        if(!is_array($entitlement, $this->entitlements)){
            array_push($this->entitlements, $entitlement);
        }

        return true;

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
     * @return array
     */
    public function getAvailableEntitlements()
    {
        return $this->availableEntitlements;
    }

}