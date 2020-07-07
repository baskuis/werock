<?php

/**
 * User Message Object
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MessageObject {

    /**
     * Define message object specific configuration
     */
    const PLAIN_TEXT_EXCEPT_LENGTH = 64;

    /**
     * Message id
     *
     * @var
     */
    public $id;

    /**
     * Sending user
     *
     * @var \UserObject
     *
     */
    public $SendingUser;

    /**
     * Receiving user
     *
     * @var \Array(UserObject)
     *
     */
    public $ReceivingUsers;

    /**
     * Message string
     *
     * @var String
     *
     */
    public $body;

    /**
     * Plain text body string
     *
     * @var
     */
    public $plainTextBody;

    /**
     * Plain text body excerpt
     *
     * @var
     */
    public $plainTextExcerpt;

    /**
     * Timestamp
     *
     * @var
     */
    public $timestamp;

    /**
     * @param mixed $id
     */
    public function setId($id){
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param \UserObject $SendingUser
     */
    public function setSendingUser($SendingUser){
        $this->SendingUser = $SendingUser;
    }

    /**
     * @param Array $ReceivingUsers
     */
    public function setReceivingUsers($ReceivingUsers){
        $this->ReceivingUsers = $ReceivingUsers;
    }

    /**
     * @return Array
     */
    public function getReceivingUsers(){
        return $this->ReceivingUsers;
    }

    /**
     * @return \UserObject
     */
    public function getSendingUser(){
        return $this->SendingUser;
    }

    /**
     * @param UserObject $ReceivingUser
     */
    public function setReceivingUser(UserObject $ReceivingUser){
        if($this->ReceivingUsers == null){ $this->ReceivingUsers = array(); }
        array_push($this->ReceivingUsers, $ReceivingUser);
    }

    /**
     * @param mixed $body
     */
    public function setBody($body){
        $this->body = $body;
        $this->plainTextBody = strip_tags($this->body);
        $this->plainTextExcerpt = CoreStringUtils::limitString($this->plainTextBody, self::PLAIN_TEXT_EXCEPT_LENGTH);
    }

    /**
     * @return mixed
     */
    public function getBody(){
        return $this->body;
    }

    /**
     * Get plain text body
     *
     * @return string
     */
    public function getPlainTextBody(){
        return strip_tags($this->body);
    }

    /**
     * @param mixed $plainTextExcerpt
     */
    public function setPlainTextExcerpt($plainTextExcerpt){
        $this->plainTextExcerpt = $plainTextExcerpt;
    }

    /**
     * @return mixed
     */
    public function getPlainTextExcerpt(){
        return $this->plainTextExcerpt;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp){
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getTimestamp(){
        return $this->timestamp;
    }

}