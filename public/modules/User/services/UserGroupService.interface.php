<?php

/**
 * User group service interface
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
interface UserGroupServiceInterface {

    /**
     * Add a system group
     *
     * @param UserSystemGroupObject $userSystemGroupObject
     * @return mixed
     */
    public function addSystemGroup(UserSystemGroupObject $userSystemGroupObject);

    /**
     * Is user a member of a group
     *
     * @param UserGroupObject $userGroupObject
     * @param UserObject $userObject
     * @return mixed
     */
    public function isGroupMember(UserGroupObject $userGroupObject, UserObject $userObject);

    /**
     * Get all groups
     *
     * @return array
     */
    public function getGroups();

    /**
     * Get a single group
     *
     * @param null $id
     * @return UserGroupObject
     */
    public function getGroup($id = null);

    /**
     * Get member
     *
     * @param UserObject $userObject
     * @param UserGroupObject $userGroupObject
     * @return bool|UserGroupMemberObject
     */
    public function getMember(UserObject $userObject, UserGroupObject $userGroupObject);

    /**
     * Get all members for a group
     *
     * @param UserGroupObject $userGroupObject
     * @return array|bool
     */
    public function getMembers(UserGroupObject $userGroupObject);

    /**
     * Is user a member of group
     *
     * @param UserObject $userObject
     * @param UserGroupObject $userGroupObject
     * @return bool
     */
    public function isMember(UserObject $userObject, UserGroupObject $userGroupObject);

}