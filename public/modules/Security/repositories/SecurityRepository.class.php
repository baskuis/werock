<?php

/**
 * Security repository
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class SecurityRepository {

    const UPSERT_REMOTE_REPUTATION = "
        INSERT INTO werock_remote_reputation (
          `werock_remote_reputation_ip`,
          `werock_remote_reputation_fatal_count`,
          `werock_remote_reputation_error_count`,
          `werock_remote_reputation_warn_count`,
          `werock_remote_reputation_info_count`,
          `werock_remote_reputation_blocked`,
          `werock_failed_login_date_added`
        ) VALUES (
          :ip,
          0,
          0,
          0,
          0,
          0,
          NOW()
        ) ON DUPLICATE KEY UPDATE
          werock_remote_reputation_fatal_count = werock_remote_reputation_fatal_count + :fatalInc,
          werock_remote_reputation_error_count = werock_remote_reputation_error_count + :errorInc,
          werock_remote_reputation_warn_count = werock_remote_reputation_warn_count + :warnInc,
          werock_remote_reputation_info_count = werock_remote_reputation_info_count + :infoInc;
    ";

    const INSERT_FAILED_LOGIN_SQL = "
      INSERT INTO
        `werock_failed_logins`
      (
        `werock_failed_login_username`,
        `werock_failed_login_email`,
        `werock_visitor_id`,
        `werock_failed_login_date_added`
      ) VALUES (
        :username,
        :email,
        :visitorid,
        NOW()
      ); ";

    /**
     * Insert failed login attempt
     *
     * @param UserAuthenticationObject $UserAuthenticationObject
     * @return int
     */
    public function addFailedLoginAttempt(UserAuthenticationObject $UserAuthenticationObject){
        return CoreSqlUtils::insert(self::INSERT_FAILED_LOGIN_SQL, array(
            ':username' => $UserAuthenticationObject->getUsername(),
            ':email' => $UserAuthenticationObject->getEmail(),
            ':visitorid' => Corevisitor::getId()
        ));
    }

    /**
     * Upsert remote reputation
     *
     * @param null $ip
     * @param bool $fatalInc
     * @param bool $errorInc
     * @param bool $warnInc
     * @param bool $infoInc
     * @return int
     */
    public function upsertRemoteReputation($ip = null, $fatalInc = false, $errorInc = false, $warnInc = false, $infoInc = false){
        return CoreSqlUtils::insert(self::UPSERT_REMOTE_REPUTATION, array(
            ':ip' => $ip,
            ':fatalInc' => ($fatalInc ? 1 : 0),
            ':errorInc' => ($errorInc ? 1 : 0),
            ':warnInc' => ($warnInc ? 1 : 0),
            ':infoInc' => ($infoInc ? 1 : 0)
        ));
    }

}