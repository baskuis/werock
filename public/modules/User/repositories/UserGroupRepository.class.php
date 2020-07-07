<?php

/**
 * User group repository
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserGroupRepository {

    const GET_GROUPS_SQL = " SELECT * FROM werock_groups; ";
    const GET_GROUP_SQL = " SELECT * FROM werock_groups WHERE werock_group_id = :id; ";

    const GET_GROUP_MEMBER_SQL = " SELECT * FROM werock_group_members WHERE werock_group_id = :group AND werock_user_id = :user; ";
    const GET_GROUP_MEMBERS_SQL = " SELECT * FROM werock_group_members WHERE werock_group_id = :group; ";

    const GET_USER_GROUPS_SQL = " SELECT * FROM werock_group_members LEFT JOIN werock_groups USING ( werock_group_id ) WHERE werock_user_id = :user; ";

    const ADD_GROUP_MEMBER_SQL = " INSERT INTO werock_group_members (werock_group_id, werock_user_id, werock_group_member_date_added) VALUES ( :group, :user, NOW() ); ";
    const DELETE_GROUP_MEMBER_SQL = " DELETE FROM werock_group_members WHERE werock_group_id = :group AND werock_user_id = :user; ";

    public static function getGroup($id = null){
        return CoreSqlUtils::row(self::GET_GROUP_SQL, array(
            ':id' => (int) $id
        ));
    }

    public static function getGroups(){
        return CoreSqlUtils::rows(self::GET_GROUPS_SQL, array());
    }

    public static function getGroupMember($groupid = null, $userid = null){
        return CoreSqlUtils::row(self::GET_GROUP_MEMBER_SQL, array(
            ':group' => (int) $groupid,
            ':user' => (int) $userid
        ));
    }

    public static function getGroupMembers($groupid = null){
        return CoreSqlUtils::rows(self::GET_GROUP_MEMBERS_SQL, array(
            'group' => (int) $groupid
        ));
    }

    public static function getUserGroups($userid = null){
        return CoreSqlUtils::rows(self::GET_USER_GROUPS_SQL, array(
            ':user' => (int) $userid
        ));
    }

    public static function addGroupMember($groupid = null, $userid = null){
        return CoreSqlUtils::insert(self::ADD_GROUP_MEMBER_SQL, array(
           ':group' => (int) $groupid,
           ':user' => (int) $userid
        ));
    }

    public static function deleteGroupMember($groupid = null, $userid = null){
        return CoreSqlUtils::delete(self::DELETE_GROUP_MEMBER_SQL, array(
            ':group' => (int) $groupid,
            ':user' => (int) $userid
        ));
    }

}