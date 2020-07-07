<?php

/**
 * Media Procedure
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MediaProcedure {

    const CACHE_NS = 'media:image';
    const CACHE_SEPARATOR = ':';

    /** @var MediaRepository $MediaRepository */
    private $MediaRepository;

    function __construct(){
        $this->MediaRepository = CoreLogic::getRepository('MediaRepository');
    }

    /**
     * Create MediaObject
     *
     * @param MediaCreateObject $mediaCreateObject
     * @return MediaObject
     * @throws MediaInvalidCreateRequestException
     */
    public function create(MediaCreateObject $mediaCreateObject){

        /**
         * Assertions
         */
        if(empty($mediaCreateObject->data)) throw new MediaInvalidCreateRequestException();
        if(empty($mediaCreateObject->filename)) throw new MediaInvalidCreateRequestException();

        try {
            $imageInfo = self::imageInfo($mediaCreateObject->data);
            $mediaCreateObject->setBackgroundColor($imageInfo->color);
            $mediaCreateObject->setHeight($imageInfo->height);
            $mediaCreateObject->setWidth($imageInfo->width);
        } catch(Exception $e){
            //ignore
        }

        /** @var MediaRepository $MediaDAO */
        $MediaDAO = CoreLogic::getRepository('MediaRepository');
        $MediaObject = $MediaDAO->persist($mediaCreateObject);

        return $MediaObject;

    }

    /**
     * Read MediaObject
     *
     * @param MediaRequestObject $mediaRequestObject
     * @return MediaObject
     * @throws MediaInvalidRequestException
     */
    public function read(MediaRequestObject $mediaRequestObject){

        /**
         * Cache lookup
         */
        $cacheKey = self::CACHE_NS . self::CACHE_SEPARATOR . $mediaRequestObject->getId();
        if(false !== ($MediaObject = CoreCache::getCache($cacheKey, false, self::CACHE_NS))){
            return $MediaObject;
        }

        /**
         * Assertions
         */
        if(empty($mediaRequestObject->id)) throw new MediaInvalidRequestException();

        /**
         * Read from repo
         */
        $MediaObject = $this->MediaRepository->read($mediaRequestObject);

        /**
         * Store cached
         */
        CoreCache::saveCache($cacheKey, $MediaObject, 0, false, self::CACHE_NS);

        return $MediaObject;

    }

    /**
     * Stream MediaObject
     *
     * @param MediaRequestObject $mediaRequestObject
     * @return MediaObject
     * @throws MediaInvalidRequestException
     */
    public function stream(MediaRequestObject $mediaRequestObject){

        /**
         * Assertions
         */
        if(empty($mediaRequestObject->id)) throw new MediaInvalidRequestException();

        /**
         * Cache lookup
         */
        $cacheKey = self::CACHE_NS . self::CACHE_SEPARATOR . md5(serialize($mediaRequestObject));
        if(false !== ($MediaStreamObject = CoreCache::getCache($cacheKey, false, self::CACHE_NS))){
            return $MediaStreamObject;
        }

        /**
         * Handle specific version
         */
        if($mediaRequestObject->width > 0 || $mediaRequestObject->height > 0 && ($mediaRequestObject->width < 1200 && $mediaRequestObject->height < 800)){

            /** @var array $versionRow */
            $versionRow = $this->MediaRepository->getVersion(
                $mediaRequestObject->getId(),
                $mediaRequestObject->getHeight(),
                $mediaRequestObject->getWidth());

            /**
             * Need to generate a appropriately sized version
             */
            if(empty($versionRow)){

                /** @var MediaStreamObject $MediaStreamObject */
                $MediaStreamObject = $this->MediaRepository->stream($mediaRequestObject);

                /** @var string $versionData */
                $versionData = CoreImageUtils::imageResize(
                    $MediaStreamObject->getData(),
                    $mediaRequestObject->getWidth(),
                    $mediaRequestObject->getHeight());

                $MediaStreamObject->setData(base64_decode($versionData));
                $MediaStreamObject->setType($MediaStreamObject->getType());

                /**
                 * Capture created version
                 */
                $this->MediaRepository->insertVersion($mediaRequestObject->getId(), $versionData, $mediaRequestObject->getHeight(), $mediaRequestObject->getWidth());

            /**
             * Return saved version
             */
            }else{

                /** @var MediaObject $MediaObject */
                $MediaObject = $this->MediaRepository->read($mediaRequestObject);

                /** @var MediaStreamObject $MediaStreamObject */
                $MediaStreamObject = CoreLogic::getObject('MediaStreamObject');
                $MediaStreamObject->setData(base64_decode($versionRow['werock_media_version_data']));
                $MediaStreamObject->setType($MediaObject->getType());
                $MediaStreamObject->setName($MediaObject->getName());

            }
        }

        if(!isset($MediaStreamObject) || empty($MediaStreamObject)) {
            /** @var MediaStreamObject $MediaStreamObject */
            $MediaStreamObject = $this->MediaRepository->stream($mediaRequestObject);
        }

        /**
         * Store cached
         */
        CoreCache::saveCache($cacheKey, $MediaStreamObject, 0, false, self::CACHE_NS);

        return $MediaStreamObject;

    }

    /**
     * Get dominant border color - and image dimensions
     *
     * @param $data
     * @return Object
     * @throws Exception
     */
    protected function imageInfo($data){
        $response = new stdClass();
        $colors = array();
        $size = @getimagesizefromstring($data);
        if($size === false){
            throw new Exception();
        }
        $img = @imagecreatefromstring($data);
        if(!$img){
            throw new Exception();
        }
        for($x = 0; $x < $size[0]; $x += 1) {
            for($y = 0; $y < $size[1]; $y += 1) {
                if($x == 0 || $x == $size[0] - 1 || $y == 0 || $y == $size[1] - 1) {
                    $thisColor = imagecolorat($img, $x, $y);
                    $rgb = imagecolorsforindex($img, $thisColor);
                    $red = round(round(($rgb['red'] / 0x10)) * 0x10);
                    $green = round(round(($rgb['green'] / 0x10)) * 0x10);
                    $blue = round(round(($rgb['blue'] / 0x10)) * 0x10);
                    $thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue);
                    if (array_key_exists($thisRGB, $colors)) {
                        $colors[$thisRGB]++;
                    } else {
                        $colors[$thisRGB] = 1;
                    }
                }
            }
        }
        arsort($colors);
        reset($colors);
        $response->color = key($colors);
        $response->width = $size[0];
        $response->height = $size[1];
        return $response;
    }

}