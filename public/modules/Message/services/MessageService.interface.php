<?php
/**
 * Message Manager Interface
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */

interface MessageServiceInterface {

    /**
     * Get messages
     *
     * @return Array|bool
     */
    public function getMessages();

    /**
     * Send message
     *
     * @param MessageObject $MessageObject
     * @param Array(UserObject) $ReceivingUser
     * @return bool
     */
    public function send(MessageObject $MessageObject, Array $ReceivingUser);

    /**
     * Get Message
     *
     * @param null $message_id
     * @return MessageObject
     */
    public function getMessage($message_id = null);

}