<?php

/**
 * Message Service Procedure
 *
 * PHP version 5.4
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MessageProcedure {

    /**
     * Events
     */
    const MESSAGE_EVENT_MESSAGE_SEND_SUCCESS = 'message:send:success';

    /** @var UserService $UserService */
    private $UserService;

    /** @var MessageRepository $MessageRepository */
    private $MessageRepository;

    function __construct(){
        $this->UserService = CoreLogic::getService('UserService');
        $this->MessageRepository = CoreLogic::getRepository('MessageRepository');
    }

    /**
     * Get Messages For User
     *
     * @return Array|bool
     * @throws UserUnauthorizedException
     */
    public function getMessages(){

        /**
         * Check current active user
         * @var UserObject $CurrentUser
         */
        $CurrentUser = $this->UserService->getCurrentUser();

        /**
         * We need an active user at this point
         */
        if(!$CurrentUser){
            throw new UserUnauthorizedException();
        }

        /**
         * Message filter
         * @var MessageFilterObject $MessageFilterObject
         */
        $MessageFilterObject = CoreLogic::getObject('MessageFilterObject');
        $MessageFilterObject->setStart(0);
        $MessageFilterObject->setLimit(CoreModule::getProp('MessageModule', 'show:messages', 20));

        /**
         * Generate cache key
         * and namespace
         */
        $cacheKey = 'getmessages:' . $CurrentUser->getId() . ':' . $MessageFilterObject->getHash();
        $cacheNS = array('getmessages', 'getmessages:' . $CurrentUser->getId());

        /**
         * Return the cached value if
         * there is one
         */
        if(false !== ($response = CoreCache::getCache($cacheKey, true, $cacheNS))){
            return $response;
        }

        $messages = $this->MessageRepository->getMessages($CurrentUser, $MessageFilterObject);

        //response
        $response = array();

        /**
         * Build response
         */
        if(!empty($messages)){
            foreach($messages as $message){

                try {

                    /**
                     * @var MessageObject $MessageObject
                     */
                    $MessageObject = CoreLogic::getObject('MessageObject');
                    $MessageObject->setId($message['message']['werock_message_id']);
                    $MessageObject->setBody($message['message']['werock_message_body']);
                    $MessageObject->setSendingUser($this->UserService->getUser($message['message']['userId']));

                    //Addressees
                    if(!empty($message['addressees'])){
                        foreach($message['addressees'] as $addressee){

                            //get receiving user
                            $ReceivingUser = $this->UserService->getUser($addressee['addresseeId']);

                            //check type
                            if(@get_class($ReceivingUser) != UserObject::class){
                                throw new UserNotFoundException();
                            }

                            //set receiving user
                            $MessageObject->setReceivingUser($ReceivingUser);

                        }
                    }

                    //stack it
                    array_push($response, $MessageObject);

                } catch(UnableToFindUserByIdException $e){

                    /**
                     * Was unable to find receiving user by id
                     */
                    CoreLog::debug('Unable to find user by id!');
                    CoreLog::debug('Was not able to find user with id=[' . $addressee['werock_message_id'] . ']');
                    CoreLog::debug('Was not able to find address for message id id=[' . $message['message']['werock_message_id'] . ']');
                    CoreLog::debug(json_encode($message));

                }

            }
        }

        //cache response
        CoreCache::saveCache($cacheKey, $response, 0, true, $cacheNS);

        //return
        return $response;

    }

    /**
     * Send Message
     *
     * @param MessageObject $MessageObject
     * @param array(UserObject) $ReceivingUsers
     * @return bool
     * @throws UserUnauthorizedException
     */
    public function send(MessageObject $MessageObject, $ReceivingUsers){

        /**
         * Check current active user
         * @var UserObject $CurrentUser
         */
        $CurrentUser = $this->UserService->getCurrentUser();

        /**
         * We need an active user at this point
         */
        if(!$CurrentUser){
            throw new UserUnauthorizedException();
        }

        $this->MessageRepository->send($MessageObject, $ReceivingUsers, $CurrentUser);

        /**
         * Invalidate caches for this user
         */
        $cacheNS = 'getmessages:' . $CurrentUser->getId();
        CoreCache::invalidateNamespace($cacheNS);

        /**
         * Invalidate caches for addressees
         */
        /** @var UserObject $ReceivingUser */
        foreach($ReceivingUsers as $ReceivingUser){
            $cacheNS = 'getmessages:' . $ReceivingUser->getId();
            CoreCache::invalidateNamespace($cacheNS);
        }

        /**
         * Fire off the event
         */
        CoreObserver::dispatch(self::MESSAGE_EVENT_MESSAGE_SEND_SUCCESS, $this);

        return true;

    }

    /**
     * Get Message
     *
     * @param null $id
     * @return MessageObject
     * @throws UserUnauthorizedException
     */
    public function getMessage($id = null){

        /**
         * Check current active user
         * @var UserObject $CurrentUser
         */
        $CurrentUser = $this->UserService->getCurrentUser();

        /**
         * We need an active user at this point
         */
        if(!$CurrentUser){
            throw new UserUnauthorizedException();
        }

        /**
         * Cache key and namespace
         */
        $cacheKey = 'getmessage:' . $id;
        $cacheNS = 'user:' . $CurrentUser->getId();

        /**
         * Return cached if available
         */
        if(false !== ($MessageObject = CoreCache::getCache($cacheKey, true, $cacheNS))){
            return $MessageObject;
        }

        $message = $this->MessageRepository->getMessage($id);

        /**
         * The current user needs access to at least one of the messages
         */
        $userHasAccessToMessage = false;
        if($message['message']['userId'] == $CurrentUser->getId()){
            $userHasAccessToMessage = true;
        }else{
            if(!empty($message['addressees'])){
                foreach($message['addressees'] as $addressee){
                    if($addressee['addresseeId'] == $CurrentUser->getId()){
                        $userHasAccessToMessage = true;
                        break;
                    }
                }
            }
        }
        if(!$userHasAccessToMessage){
            throw new UserUnauthorizedException();
        }

        /**
         * Populate the message object
         */
        $MessageObject = new MessageObject();
        $MessageObject->setId($message['message']['werock_message_id']);
        $MessageObject->setBody($message['message']['werock_message_body']);
        $MessageObject->setSendingUser($this->UserService->getUser($message['message']['userId']));
        $MessageObject->setTimestamp($message['message']['werock_message_date_added']);

        //Addressees
        if(!empty($message['addressees'])){
            foreach($message['addressees'] as $addressee){
                $MessageObject->setReceivingUser($this->UserService->getUser($addressee['addresseeId']));
            }
        }

        /**
         * Store cached
         */
        CoreCache::saveCache($cacheKey, $MessageObject, 0, true, $cacheNS);

        //return message object
        return $MessageObject;

    }

}