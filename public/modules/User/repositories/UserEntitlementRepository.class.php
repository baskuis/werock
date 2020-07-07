<?php

/**
 * User entitlement repository
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserEntitlementRepository {

    /**
     * Sql statements
     */
    const GET_GROUP_ENTITLEMENTS_FOR_OBJECT_SQL = " SELECT * FROM werock_entitlements WHERE werock_entitlement_object_urn = :objecturn ";
    const ADD_GROUP_OBJECT_ENTITLEMENT_SQL = " INSERT INTO werock_entitlements (werock_group_id, werock_group_urn, werock_entitlement_type, werock_entitlement_object_urn, werock_entitlement_date_added) VALUES (:groupid, :groupurn, :entitlement, :objecturn, NOW()); ";
    const REMOVE_GROUP_OBJECT_ENTITLEMENTS_SQL = " DELETE FROM werock_entitlements WHERE werock_entitlement_object_urn = :objecturn AND ((werock_group_id > 0 AND werock_group_id = :groupid) OR werock_group_urn = :groupurn); ";
    const REMOVE_ALL_OBJECT_ENTITLEMENTS_SQL = " DELETE FROM werock_entitlements WHERE werock_entitlement_object_urn = :objecturn; ";
    const REMOVE_GROUP_ENTITLEMENTS_SQL = " DELETE FROM werock_entitlements WHERE (werock_group_id > 0 AND werock_group_id = :groupid) OR werock_group_urn = :groupurn; ";

    /**
     * Add an object entitlement
     *
     * @param null $groupid
     * @param null $groupurn
     * @param null $objecturn
     * @param null $entitlementurn
     * @return Array
     */
    public function addGroupObjectEntitlement($groupid = null, $groupurn = null, $objecturn = null, $entitlementurn = null){
        return CoreSqlUtils::insert(self::ADD_GROUP_OBJECT_ENTITLEMENT_SQL, array(
            ':groupid' => (int) $groupid,
            ':groupurn' => $groupurn,
            ':objecturn' => $objecturn,
            ':entitlement' => $entitlementurn
        ));
    }

    /**
     * Get object group entitlements
     *
     * @param null $objecturn
     * @return Array
     */
    public function getObjectEntitlements($objecturn = null){
        return CoreSqlUtils::rows(self::GET_GROUP_ENTITLEMENTS_FOR_OBJECT_SQL, array(
            ':objecturn' => $objecturn
        ));
    }

    /**
     * Delete group object entitlements
     *
     * @param null $groupid
     * @param null $groupurn
     * @param null $objecturn
     * @return bool
     */
    public function deleteGroupObjectEntitlements($groupid = null, $groupurn = null, $objecturn = null){
        return CoreSqlUtils::delete(self::REMOVE_GROUP_OBJECT_ENTITLEMENTS_SQL, array(
            ':groupid' => (int) $groupid,
            ':groupurn' => $groupurn,
            ':objecturn' => $objecturn
        ));
    }

    /**
     * Delete object entitlements
     *
     * @param null $objecturn
     * @return bool
     */
    public function deleteObjectEntitlements($objecturn = null){
        return CoreSqlUtils::delete(self::REMOVE_ALL_OBJECT_ENTITLEMENTS_SQL, array(
            ':objecturn' => $objecturn
        ));
    }

    /**
     * Delete group entitlements
     *
     * @param null $groupid
     * @param null $groupurn
     * @return bool
     */
    public function deleteGroupEntitlements($groupid = null, $groupurn = null){
        return CoreSqlUtils::delete(self::REMOVE_GROUP_ENTITLEMENTS_SQL, array(
            ':groupid' => (int) $groupid,
            ':groupurn' => $groupurn
        ));
    }

}