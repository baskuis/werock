<?php

/**
 * Message Service
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MessageService implements MessageServiceInterface {

    /**
     * Get messages
     *
     * @return Array|bool
     */
    public function getMessages(){

        try {

            /**
             * @var \MessageProcedure $MessageProxy
             */
            $MessageProxy = CoreLogic::getProcedure('MessageProcedure');

            /**
             * Get messages
             */
            return $MessageProxy->getMessages();

        } catch (UserUnauthorizedException $e){

            /**
             * Set unauthorized notification
             */
            CoreNotification::set('Unauthorized!', CoreNotification::ERROR);

        } catch (Exception $e){

            /**
             * Set error notification
             */
            CoreNotification::set('Unable to get messages!', CoreNotification::ERROR);

        }

        return false;

    }

    /**
     * Send message
     *
     * @param MessageObject $MessageObject
     * @param array() $receivingusers
     * @return bool
     */
    public function send(MessageObject $MessageObject, Array $ReceivingUsers){

        try {

            /**
             * @var \MessageProcedure $MessageProxy
             */
            $MessageProxy = CoreLogic::getProcedure('MessageProcedure');
            $MessageProxy->send($MessageObject, $ReceivingUsers);

            return true;

        } catch (UserUnauthorizedException $e){

            /**
             * Set unauthorized notification
             */
            CoreNotification::set('Unauthorized!', CoreNotification::ERROR);

        } catch (Exception $e){

            /**
             * Set error notification
             */
            CoreNotification::set('An error occurred!', CoreNotification::ERROR);

        }

        return false;

    }

    /**
     * Get Message
     *
     * @param null $message_id
     * @return MessageObject
     */
    public function getMessage($id = null){

        try {

            /**
             * @var \MessageProcedure $MessageProxy
             */
            $MessageProxy = CoreLogic::getProcedure('MessageProcedure');
            $MessageObject = $MessageProxy->getMessage($id);

            //return the message
            return $MessageObject;

        } catch (UserUnauthorizedException $e){

            /**
             * Set unauthorized notification
             */
            CoreNotification::set('Unauthorized!', CoreNotification::ERROR);

        } catch (MessageNotFoundException $e){

            /**
             * Get message - not found
             */
            CoreNotification::set('Message not found!', CoreNotification::ERROR);

        } catch (Exception $e){

            /**
             * Set error notification
             */
            CoreNotification::set('An error occurred!', CoreNotification::ERROR);

        }

        return false;

    }

}