<?php

/**
 * Media Service
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MediaService implements MediaServiceInterface {

    private $MediaProcedure;

    function __construct()
    {
        $this->MediaProcedure = CoreLogic::getProcedure('MediaProcedure');
    }

    /**
     * Create media
     *
     * @param MediaCreateObject $mediaCreateObject
     * @return MediaObject
     */
    public function create(MediaCreateObject $mediaCreateObject){

        try {
            return $this->MediaProcedure->create($mediaCreateObject);
        } catch(MediaInvalidCreateRequestException $e){
            CoreNotification::set("Invalid media create request", CoreNotification::ERROR);
        } catch(Exception $e){
            CoreNotification::set("Unable to upload media", CoreNotification::ERROR);
        }

        return false;

    }

    /**
     * Capture remote url
     *
     * @param null $url
     * @return bool|MediaObject
     */
    public function captureUrl($url = null){

        try {

            /**
             * Assertions
             */
            if(empty($url)) throw new MediaInvalidCreateRequestException('No url passed');
            if(substr($url, 0, 2) == '//') $url = 'https:' . $url;
            if(!preg_match('/^https?:\/\//', $url)) throw new MediaInvalidCreateRequestException('Not a valid url passed');

            /** @var MediaCreateObject $MediaCreateObject */
            $MediaCreateObject = CoreLogic::getObject('MediaCreateObject');
            $imageData = null;
            try {
                $imageData = CoreRemoteUtils::getRemoteContents($url);
                if (strlen($imageData) < 100 || stripos($imageData, '</body>') > 0) {
                    throw new Exception();
                }
            } catch(Exception $e) {
                $imageData = CoreRemoteUtils::getRemoteContents('https://res.cloudinary.com/demo/image/fetch/' . $url);
            }
            $MediaCreateObject->setData(base64_encode($imageData));
            $MediaCreateObject->setFilename(pathinfo($url, PATHINFO_FILENAME));
            $MediaCreateObject->setType(pathinfo($url, PATHINFO_EXTENSION));

            return self::create($MediaCreateObject);

        } catch(MediaInvalidCreateRequestException $e){
            CoreNotification::set("Invalid media create request", CoreNotification::ERROR);
        } catch(Exception $e){
            CoreNotification::set("Unable to capture media from url", CoreNotification::ERROR);
        }

        return false;

    }

    /**
     * Get by id
     *
     * @param null $id
     * @return bool|MediaObject
     */
    public function getById($id = null){

        /** @var MediaRequestObject $MediaRequestObject */
        $MediaRequestObject = CoreLogic::getObject('MediaRequestObject');
        $MediaRequestObject->setId($id);

        return $this->get($MediaRequestObject);

    }

    /**
     * Get media object
     *
     * @param MediaRequestObject $MediaRequestObject
     * @return bool|MediaObject
     */
    public function get(MediaRequestObject $MediaRequestObject){

        try {
            return $this->MediaProcedure->read($MediaRequestObject);
        } catch(MediaNotFoundException $e){
            CoreNotification::set("Media not found" . serialize($MediaRequestObject), CoreNotification::ERROR);
        } catch(Exception $e){
            CoreNotification::set("Unable to get media. Info: " . $e->getMessage(), CoreNotification::ERROR);
        }

        return false;

    }

    /**
     * Stream data
     *
     * @param MediaRequestObject $MediaRequestObject
     * @return bool|MediaStreamObject
     */
    public function stream(MediaRequestObject $MediaRequestObject){

        try {
            return $this->MediaProcedure->stream($MediaRequestObject);
        } catch(MediaNotFoundException $e){
            CoreNotification::set("Media not found", CoreNotification::ERROR);
        } catch(Exception $e){
            CoreNotification::set("Unable to stream media", CoreNotification::ERROR);
        }

        return false;

    }

    public function update(MediaObject $MediaObject){

    }

    public function delete(MediaObject $MediaObject){

    }

}