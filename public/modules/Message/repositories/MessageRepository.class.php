<?php

/**
 * Message Manager Repository
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MessageRepository {

    /**
     * Queries
     */
    const INSERT_NEW_MESSAGE_QUERY = "
		INSERT INTO
			werock_messages
		(
		    werock_user_id,
		    werock_message_body,
		    werock_message_date_added
		) VALUES (
            :userId,
            :messageBody,
            NOW()
		)
	";
    const GET_MESSAGES_FOR_USER_QUERY = "
        SELECT
            *,
            @serial := IF((@id != werock_messages.werock_message_id), @serial + 1, @serial),
            @id := werock_messages.werock_message_id,
            werock_messages.werock_user_id AS userId,
            werock_message_addressees.werock_user_id AS addresseeId
        FROM
            werock_messages
        LEFT JOIN
            werock_message_addressees
        ON
            werock_messages.werock_message_id = werock_message_addressees.werock_message_id
        WHERE
            (
                werock_messages.werock_user_id = :userId
            OR
                werock_message_addressees.werock_user_id = :userId
            )
            AND
                @serial < :limit
        ORDER BY
            werock_message_date_added DESC
    ";
    const GET_MESSAGE_BY_ID = "
        SELECT
            *,
            werock_messages.werock_user_id as userId,
            werock_message_addressees.werock_user_id as addresseeId
        FROM
            werock_messages
        LEFT JOIN
            werock_message_addressees
        ON
            werock_messages.werock_message_id = werock_message_addressees.werock_message_id
        WHERE
            werock_messages.werock_message_id = :messageId
    ";
    const ADD_MESSAGE_ADDRESSEES_QUERY = "
        INSERT INTO
            werock_message_addressees
        (
            werock_message_addressees.werock_message_id,
            werock_message_addressees.werock_user_id,
            werock_message_addressees.werock_message_addressee_date_added
        ) VALUES (
            :messageid,
            :userid,
            NOW()
        )
    ";

    /**
     * Get all messages for user
     *
     * @param UserObject $UserObject
     * @return Array|bool
     */
    public function getMessages(UserObject $UserObject, MessageFilterObject $MessageFilterObject){

        /**
         * Try to get messages
         */
        try {

            /**
             * Get the messages
             *
             * Allow global limit on messages
             * by setting serial and id
             */
            CoreSqlUtils::query('SET @serial=0;');
            CoreSqlUtils::query('SET @id=0;');
            $rows = CoreSqlUtils::rows(self::GET_MESSAGES_FOR_USER_QUERY, array(
                ':userId' => $UserObject->getId(),
                ':limit' => $MessageFilterObject->getLimit()
            ));

            /**
             * No messages found
             */
            if(!$rows){
                return false;
            }

            /**
             * Organize array
             */
            $messages = array();
            foreach($rows as $row){
                $messages[$row['werock_message_id']]['message'] = $row;
                $messages[$row['werock_message_id']]['addressees'][$row['addresseeId']] = $row;
            }

            //return messages
            return $messages;

        } catch(Exception $e){

            /**
             * Keep track of error
             */
            CoreLog::error('Unable to get messages');

            /**
             * Re-throw exception
             */
            throw new Exception('Unable to get messages');

        }

    }

    /**
     * Write message to database
     *
     * @param MessageObject $MessageObject
     * @param array(UserObject) $ReceivingUser
     * @return bool|void
     */
    public function send(MessageObject $MessageObject, $ReceivingUsers, UserObject $CurrentUser){

        /**
         * Attempt to write the message
         */
        $inserted = CoreSqlUtils::insert(self::INSERT_NEW_MESSAGE_QUERY, array(
            ':userId' => $CurrentUser->getId(),
            ':messageBody' => $MessageObject->getBody()
        ));

        //receiving users
        if(!empty($ReceivingUsers) && $inserted > 0){
            foreach($ReceivingUsers as $ReceivingUser){

                /**
                 * @var ReceivingUser $ReceivingUser
                 */
                $inserted_addressee = CoreSqlUtils::insert(self::ADD_MESSAGE_ADDRESSEES_QUERY, array(
                    ':messageid' => $inserted,
                    ':userid' => $ReceivingUser->getId()
                ));

                //check for issues
                if(!$inserted_addressee){
                    throw new Exception('Unable to store addressee');
                }

            }
        }

        //return success
        if(false !== $inserted){
            return true;
        }

        /**
         * Keep track of error
         */
        CoreLog::error('Unable to store message');

        /**
         * Throw exception - unable to write message
         */
        throw new Exception('Unable to store message');

    }

    /**
     * Get Message
     *
     * @param null $id
     * @return Array
     * @throws MessageNotFoundException
     */
    public function getMessage($id = null){

        /**
         * Return row
         */
        $rows = CoreSqlUtils::rows(self::GET_MESSAGE_BY_ID, array(
            'messageId' => $id
        ));

        /**
         * Check rows
         */
        if(!$rows){

            /**
             * Throw exception - unable to get message
             */
            throw new MessageNotFoundException();

        }

        /**
         * Organize array
         */
        $message = array();
        foreach($rows as $row){
            $message['message'] = $row;
            $message['addressees'][$row['addresseeId']] = $row;
        }

        //return row
        return $message;

    }

}