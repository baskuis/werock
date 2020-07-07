<?php

/**
 * Engagement Repository
 *
 * PHP version 7
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class EngagementRepository {

    const INSERT_ENGAGEMENT_SQL = "INSERT INTO werock_engagement_emails (werock_visitor_id, werock_engagement_email_tag, werock_engagement_email_value, werock_engagement_email_name, werock_engagement_email_last_sent, werock_engagement_email_date_added) VALUES (:visitorId, :tag, :email, :name, NOW(), NOW());";
    const UPDATE_ENGAGEMENT_SQL = "UPDATE werock_engagement_emails SET werock_engagement_email_tag = :tag, werock_engagement_email_value = :email, werock_engagement_email_name = :name WHERE werock_engagement_id = :id;";
    const SELECT_ENGAGEMENT_SQL = "SELECT * FROM werock_engagement_emails WHERE werock_engagement_email_id = :id;";
    const DELETE_ENGAGEMENT_SQL = "DELETE FROM werock_engagement_emails WHERE werock_engagement_email_id = :id;";
    const LIST_ENGAGEMENT_SQL = "SELECT * FROM werock_engagement_emails WHERE werock_engagement_email_tag = :tag;";
    const LIST_ENGAGEMENT_BY_TAG_MATCH = "SELECT * FROM werock_engagement_emails WHERE werock_engagement_email_tag LIKE :prepend AND werock_engagement_email_last_sent < :since ORDER BY RAND() LIMIT :limit;";
    const UPDATE_ENGAGEMENT_LAST_SENT_SQL = "UPDATE werock_engagement_emails SET werock_engagement_email_last_sent = NOW() WHERE werock_engagement_email_id = :id;";

    function __construct(){

    }

    public function updateLastSent($id = null){
        return CoreSqlUtils::update(self::UPDATE_ENGAGEMENT_LAST_SENT_SQL, array(
            ':id' => (int) $id
        ));
    }

    public function listEngagementsEmailsByTagPrepend($prepend = null, $since = null, $limit = 20){
        return CoreSqlUtils::rows(self::LIST_ENGAGEMENT_BY_TAG_MATCH, array(
            ':prepend' => $prepend . '%',
            ':since' => date('Y-m-d H:i:s', strtotime($since)),
            ':limit' => (int) $limit
        ));
    }

    public function insertEngagementEmail($tag = null, $email = null, $name = null){
        return CoreSqlUtils::insert(self::INSERT_ENGAGEMENT_SQL, array(
            ':visitorId' => (int) CoreVisitor::getId(),
            ':tag' => $tag,
            ':email' => $email,
            ':name' => $name
        ));
    }

    public function updateEngagementEmail($id = null, $tag = null, $email = null, $name = null){
        return CoreSqlUtils::update(self::UPDATE_ENGAGEMENT_SQL, array(
            ':id' => (int) $id,
            ':tag' => $tag,
            ':email' => $email,
            ':name' => $name
        ));
    }

    public function selectEngagementEmail($id = null){
        return CoreSqlUtils::row(self::SELECT_ENGAGEMENT_SQL, array(
            ':id' => (int) $id
        ));
    }

    public function deleteEngagementEmail($id = null){
        return CoreSqlUtils::delete(self::DELETE_ENGAGEMENT_SQL, array(
            ':id' => (int) $id
        ));
    }

    public function listEngagementEmails($tag = null){
        return CoreSqlUtils::rows(self::LIST_ENGAGEMENT_SQL, array(
            ':tag' => $tag
        ));
    }

}