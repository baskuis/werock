<?php

/**
 * Email Repository
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class EmailRepository {

    const SQL_SELECT_UNSUBSCRIBED_EMAIL = "
        SELECT
          *
        FROM
          werock_email_unsubscribes
        WHERE
          werock_email_unsubscribe_email = :email
    ";
    const SQL_INSERT_UNSUBSCRIBED_EMAIL = "
        INSERT INTO
          werock_email_unsubscribes
        (
          werock_visitor_id,
          werock_email_unsubscribe_email,
          werock_email_unsubscribe_date_added
        ) VALUES (
          :visitorId,
          :email,
          NOW()
        )
    ";
    const SQL_DELETE_UNSUBSCRIBED_EMAIL = "
        DELETE FROM
          werock_email_unsubscribes
        WHERE
          werock_email_unsubscribe_email = :email
    ";

    /**
     * Get un-subscribed email
     *
     * @param null $email
     * @return array
     */
    public function getUnsubscribedEmail($email = null){
        return CoreSqlUtils::row(self::SQL_SELECT_UNSUBSCRIBED_EMAIL, array(
            ':email' => $email
        ));
    }

    /**
     * Insert un-subscribed email
     *
     * @param null $email
     * @return Array
     */
    public function insertUnsubscribedEmail($email = null){
        return CoreSqlUtils::insert(self::SQL_INSERT_UNSUBSCRIBED_EMAIL, array(
            ':email' => $email,
            ':visitorId' => CoreVisitor::getId()
        ));
    }

    /**
     * Delete un-subscribed email
     *
     * @param null $email
     * @return bool
     */
    public function deleteUnsubscribedEmail($email = null){
        return CoreSqlUtils::delete(self::SQL_DELETE_UNSUBSCRIBED_EMAIL, array(
            ':email' => $email
        ));
    }

}