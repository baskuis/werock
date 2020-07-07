<?php

/**
 * User group procedure
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserGroupProcedure {

    /**
     * Cache
     */
    const CACHE_GROUPS_KEY = 'user:groups';
    const CACHE_GROUP_ROOT = 'user:group:';
    const CACHE_GROUP_MEMBERS_APPEND = ':members';

    /** @var UserGroupRepository $UserGroupRepository */
    private $UserGroupRepository;

    /** @var UserService $UserService */
    private $UserService;

    function __construct(){
        $this->UserGroupRepository = CoreLogic::getRepository('UserGroupRepository');
        $this->UserService = CoreLogic::getService('UserService');
    }

    /**
     * Is user a member of the passed group
     *
     * @param UserGroupObject $userGroupObject
     * @param UserObject $userObject
     * @return bool
     */
    public function isGroupMember(UserGroupObject $userGroupObject, UserObject $userObject){

        try {
            $this->getGroupMember($userGroupObject, $userObject);
            return true;
        } catch(UserGroupMemberNotFoundException $e){
            //suppress
        }

        return false;

    }

    /**
     * Get group member
     *
     * @param UserGroupObject $userGroupObject
     * @param UserObject $userObject
     * @return UserGroupMemberObject
     * @throws UserGroupMemberNotFoundException
     */
    public function getGroupMember(UserGroupObject $userGroupObject, UserObject $userObject){

        /** @var array $record */
        if(false !== ($record = $this->UserGroupRepository->getGroupMember($userGroupObject->getId(), $userObject->getId()))) {

            /** @var UserGroupMemberObject $UserGroupMemberObject */
            $UserGroupMemberObject = CoreLogic::getObject('UserGroupMemberObject');
            $UserGroupMemberObject->setUserObject($userObject);
            $UserGroupMemberObject->setUserGroupObject($userGroupObject);
            $UserGroupMemberObject->setDateAdded(strtotime($record['werock_group_member_date_added']));

            return $UserGroupMemberObject;

        }

        throw new UserGroupMemberNotFoundException();

    }

    /**
     * Get group members
     *
     * @param UserGroupObject $userGroupObject
     * @return array
     */
    public function getGroupMembers(UserGroupObject $userGroupObject){

        $cacheKey = self::CACHE_GROUP_ROOT . $userGroupObject->getId() . self::CACHE_GROUP_MEMBERS_APPEND;
        $cacheNS = array(self::CACHE_GROUPS_KEY, self::CACHE_GROUP_ROOT . $userGroupObject->getId());

        if(false !== ($members = CoreCache::getCache($cacheKey, true, $cacheNS))) {

            return $members;

        } else {

            /** @var array $members */
            $members = array();

            /** @var array $record */
            if(false !== ($records = $this->UserGroupRepository->getGroupMembers($userGroupObject->getId()))) {

                foreach ($records as $record) {

                    /**
                     * Suppress issues on a per member basis - instead let's log them to the debug log
                     */
                    try {

                        /** @var UserGroupMemberObject $UserGroupMemberObject */
                        $UserGroupMemberObject = CoreLogic::getObject('UserGroupMemberObject');
                        $UserGroupMemberObject->setUserObject($this->UserService->getUser($record['werock_user_id']));
                        $UserGroupMemberObject->setUserGroupObject($userGroupObject);
                        $UserGroupMemberObject->setDateAdded(strtotime($record['werock_group_member_date_added']));

                        array_push($members, $UserGroupMemberObject);

                    } catch(Exception $e){
                        CoreLog::debug('Unable to retrieve member! Info: ' . $e->getMessage());
                    }
                }

            }

            CoreCache::saveCache($cacheKey, $members, 600, true, $cacheNS);

            return $members;

        }

    }

    /**
     * Get groups
     *
     * @return array
     */
    public function getGroups(){

        $cacheKey = self::CACHE_GROUPS_KEY;
        $cacheNS = array(self::CACHE_GROUPS_KEY);

        if(false !== ($groups = CoreCache::getCache($cacheKey, true, $cacheNS))) {

            return $groups;

        } else {

            $groups = array();

            /** @var array $rows */
            $rows = $this->UserGroupRepository->getGroups();
            if (!empty($rows)) {

                /** @var UserEntitlementService $UserEntitlementService */
                $UserEntitlementService = CoreLogic::getService('UserEntitlementService');

                foreach ($rows as $row) {

                    /** @var UserGroupObject $UserGroupObject */
                    $UserGroupObject = CoreLogic::getObject('UserGroupObject');

                    $UserGroupObject->setId(isset($row['werock_group_id']) ? $row['werock_group_id'] : null);
                    $UserGroupObject->setName(isset($row['werock_group_name']) ? $row['werock_group_name'] : null);
                    $UserGroupObject->setDescription(isset($row['werock_group_description']) ? $row['werock_group_description'] : null);
                    $UserGroupObject->setAdded(isset($row['werock_group_date_added']) ? strtotime($row['werock_group_date_added']) : null);

                    $entitlementsraw = isset($row['werock_group_entitlements']) ? $row['werock_group_entitlements'] : null;
                    $entitlements = explode(',', $entitlementsraw);
                    if (!empty($entitlements)) {
                        foreach ($entitlements as $entitlement) {

                            try {

                                /** @var UserEntitlementObject $UserEntitlementObject */
                                $UserEntitlementObject = $UserEntitlementService->getEntitlement($entitlement);

                                if (!empty($UserEntitlementObject)) {
                                    $UserGroupObject->addEntitlement($UserEntitlementObject);
                                }

                            } catch (UserEntitlementNotFoundException $e) {

                                //suppress

                            }

                        }
                    }

                    /**
                     * Set members
                     */
                    $UserGroupObject->setMembers(self::getGroupMembers($UserGroupObject));

                    array_push($groups, $UserGroupObject);

                }
            }

            if (empty($groups)) {
                throw new UserNoGroupsException();
            }

            CoreCache::saveCache($cacheKey, $groups, 600, true, $cacheNS);

            return $groups;

        }

    }

    /**
     * Get a single group
     *
     * @param null $id
     * @return UserGroupObject
     * @throws UserNoGroupException
     */
    public function getGroup($id = null){

        $cacheKey = self::CACHE_GROUP_ROOT . $id;
        $cacheNS = array(self::CACHE_GROUPS_KEY, self::CACHE_GROUP_ROOT . $id);

        if(false !== ($UserGroupObject = CoreCache::getCache($cacheKey, true, $cacheNS))){

            return $UserGroupObject;

        } else {

            $row = $this->UserGroupRepository->getGroup($id);

            if (empty($row)) {
                throw new UserNoGroupException();
            }

            /** @var UserGroupObject $UserGroupObject */
            $UserGroupObject = CoreLogic::getObject('UserGroupObject');

            $UserGroupObject->setId(isset($row['werock_group_id']) ? $row['werock_group_id'] : null);
            $UserGroupObject->setName(isset($row['werock_group_name']) ? $row['werock_group_name'] : null);
            $UserGroupObject->setDescription(isset($row['werock_group_description']) ? $row['werock_group_description'] : null);
            $UserGroupObject->setAdded(isset($row['werock_group_date_added']) ? strtotime($row['werock_group_date_added']) : null);

            /**
             * Set members
             */
            $UserGroupObject->setMembers(self::getGroupMembers($UserGroupObject));

            /**
             * Store cache
             */
            CoreCache::saveCache($cacheKey, $UserGroupObject, 600, true, $cacheNS);

            return $UserGroupObject;

        }
    }

}