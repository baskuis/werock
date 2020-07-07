<?php

/**
 * User group service
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserGroupService implements UserGroupServiceInterface {

    /**
     * @var array $systemGroups
     */
    private $systemGroups = array();

    /**
     * @var array $groups
     */
    private $groups = array();

    /** @var UserGroupProcedure $UserGroupProcedure */
    private $UserGroupProcedure;

    function __construct(){
        $this->UserGroupProcedure = CoreLogic::getProcedure('UserGroupProcedure');
    }

    /**
     * Add a system group
     *
     * @param UserSystemGroupObject $userSystemGroupObject
     */
    public function addSystemGroup(UserSystemGroupObject $userSystemGroupObject){
        if(isset($this->systemGroups[$userSystemGroupObject->getUrn()])){
            CoreLog::debug('Overloading system group by urn: ' . $userSystemGroupObject->getUrn());
        }
        $this->systemGroups[$userSystemGroupObject->getUrn()] = $userSystemGroupObject;
    }

    /**
     * Check if user is a group member
     *
     * @param UserGroupObject $userGroupObject
     * @param UserObject $userObject
     * @return bool
     */
    public function isGroupMember(UserGroupObject $userGroupObject, UserObject $userObject){

        try {
            return $this->UserGroupProcedure->isGroupMember($userGroupObject, $userObject);
        } catch(Exception $e){
            CoreLog::error('Unable to determine if user is a group member due to unknown error');
        }

        return false;

    }

    /**
     * Get all groups
     *
     * @return array
     */
    public function getGroups(){

        try {

            /**
             * Shortcut
             * Do lookup once only
             */
            if(!empty($this->groups)){
                return $this->groups;
            }

            /** @var array $groups */
            $this->groups = array();
            if(!empty($this->systemGroups)){
                /** @var UserSystemGroupObject $systemGroup */
                foreach($this->systemGroups as $systemGroup){
                    $systemGroup->isSystem = true;
                    $systemGroup->isMember = $systemGroup->isMember(CoreUser::getUser());
                    $systemGroup->isOwner = $systemGroup->isOwner(CoreUser::getUser());
                    array_push($this->groups, $systemGroup);
                }
            }else{
                CoreLog::error('Need to define system level groups before calling UserGroupService->getGroups()');
            }
            $customGroups = $this->UserGroupProcedure->getGroups();
            if(!empty($customGroups)){
                /** @var UserGroupObject $customGroup */
                foreach($customGroups as $customGroup){
                    $customGroup->isSystem = false;
                    $customGroup->isMember = $customGroup->isMember(CoreUser::getUser());
                    $customGroup->isOwner = $customGroup->isOwner(CoreUser::getUser());
                    array_push($this->groups, $customGroup);
                }
            }

            return $this->groups;

        } catch(UserNoGroupsException $e){

            CoreNotification::set('No custom groups found!', CoreNotification::WARNING);

        } catch(Exception $e){

            CoreNotification::set('Some error occurred. No groups found!. ' . $e->getMessage(), CoreNotification::ERROR);

        }

    }

    /**
     * Get a single group
     *
     * @param null $id
     * @return UserGroupObject
     */
    public function getGroup($id = null){

        try {

            return $this->UserGroupProcedure->getGroup($id);

        } catch(UserNoGroupException $e){

            CoreNotification::set('No group found!', CoreNotification::ERROR);

        } catch(Exception $e){

            CoreNotification::set('Some error occurred. No group found!', CoreNotification::ERROR);

        }

    }

    /**
     * Get member
     *
     * @param UserObject $userObject
     * @param UserGroupObject $userGroupObject
     * @return bool|UserGroupMemberObject
     */
    public function getMember(UserObject $userObject, UserGroupObject $userGroupObject){

        try {

            return $this->UserGroupProcedure->getGroupMember($userObject, $userGroupObject);

        } catch(UserGroupMemberNotFoundException $e){

            CoreNotification::set('Member not found', CoreNotification::ERROR);

        } catch(Exception $e){

            CoreNotification::set('An error occurred. Error: ' . $e->getMessage(), CoreNotification::ERROR);

        }

        return false;

    }

    /**
     * Get all members for a group
     *
     * @param UserGroupObject $userGroupObject
     * @return array|bool
     */
    public function getMembers(UserGroupObject $userGroupObject){

        try {

            return $this->UserGroupProcedure->getGroupMembers($userGroupObject);

        } catch(Exception $e){

            CoreNotification::set('An error occurred. Error: ' . $e->getMessage(), CoreNotification::ERROR);

        }

        return false;

    }

    /**
     * Is user a member of group
     *
     * @param UserObject $userObject
     * @param UserGroupObject $userGroupObject
     * @return bool
     */
    public function isMember(UserObject $userObject, UserGroupObject $userGroupObject){

        try {

            $this->UserGroupProcedure->getGroupMember($userObject, $userGroupObject);

            return true;

        } catch(UserGroupMemberNotFoundException $e){

            //ignore

        }

        return false;

    }

}