<?php

class UserEntitlementsContextObject {

    public $groupId;
    public $groupUrn;
    public $objectUrn;

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param mixed $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return mixed
     */
    public function getGroupUrn()
    {
        return $this->groupUrn;
    }

    /**
     * @param mixed $groupUrn
     */
    public function setGroupUrn($groupUrn)
    {
        $this->groupUrn = $groupUrn;
    }

    /**
     * @return mixed
     */
    public function getObjectUrn()
    {
        return $this->objectUrn;
    }

    /**
     * @param mixed $objectUrn
     */
    public function setObjectUrn($objectUrn)
    {
        $this->objectUrn = $objectUrn;
    }

}