<?php

class UserGroupMemberObject {

    /** @var UserObject $UserObject */
    public $UserObject;

    /** @var UserGroupObject $UserGroupObject */
    public $UserGroupObject;

    /** @var long $dateAdded */
    public $dateAdded;

    /**
     * @return UserObject
     */
    public function getUserObject()
    {
        return $this->UserObject;
    }

    /**
     * @param UserObject $UserObject
     */
    public function setUserObject($UserObject)
    {
        $this->UserObject = $UserObject;
    }

    /**
     * @return UserGroupObject
     */
    public function getUserGroupObject()
    {
        return $this->UserGroupObject;
    }

    /**
     * @param UserGroupObject $UserGroupObject
     */
    public function setUserGroupObject($UserGroupObject)
    {
        $this->UserGroupObject = $UserGroupObject;
    }

    /**
     * @return long
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param long $dateAdded
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    }

}