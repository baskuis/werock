<?php

/**
 * Media Repository
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class MediaRepository {

    /**
     * Insert media SQL statement
     */
    const INSERT_MEDIA_SQL = "
        INSERT INTO werock_media (
            werock_media_name,
            werock_media_description,
            werock_media_type,
            werock_media_size,
            werock_media_background_color,
            werock_media_width,
            werock_media_height,
            werock_media_hash,
            werock_media_data,
            werock_media_date_added
        ) VALUES (
            :name,
            :description,
            :type,
            :size,
            :color,
            :width,
            :height,
            :hash,
            :data,
            NOW()
        )";

    /**
     * Get media
     */
    const GET_MEDIA_SQL = "
        SELECT
            *
        FROM
            werock_media
        WHERE
            werock_media_id = :id
    ";

    /**
     * Get by hash
     */
    const SQL_MEDIA_BY_HASH = "
        SELECT
            *
        FROM
            werock_media
        WHERE
            werock_media_hash = :hash
    ";

    /**
     * Get Media version
     */
    const SQL_GET_MEDIA_VERSION = "
        SELECT
          *
        FROM
          werock_media_versions
        WHERE
          werock_media_id = :mediaId
        AND
          werock_media_version_height = :height
        AND
          werock_media_version_width = :width
    ";

    /**
     * Insert new media version
     */
    const SQL_INSERT_MEDIA_VERSION = "
        INSERT INTO
          werock_media_versions
        (
          werock_media_id,
          werock_media_version_data,
          werock_media_version_height,
          werock_media_version_width,
          werock_media_version_date_added
        ) VALUES (
          :mediaId,
          :imageData,
          :height,
          :width,
          NOW()
        )
    ";

    /**
     * Insert a version
     *
     * @param int $mediaId
     * @param string $data
     * @param int $height
     * @param int $width
     * @return array
     */
    public function insertVersion($mediaId, $data, $height = 0, $width = 0){
        return CoreSqlUtils::insert(self::SQL_INSERT_MEDIA_VERSION, array(
            ':mediaId' => (int) $mediaId,
            ':imageData' => $data,
            ':height' => (int) $height,
            ':width' => (int) $width
        ));
    }

    /**
     * Get version
     *
     * @param int $mediaId
     * @param int $height
     * @param int $width
     * @return array
     */
    public function getVersion($mediaId, $height = 0, $width = 0){
        return CoreSqlUtils::row(self::SQL_GET_MEDIA_VERSION, array(
            ':mediaId' => (int) $mediaId,
            ':height' => (int) $height,
            ':width' => (int) $width
        ));
    }

    /**
     * Persist media
     *
     * @param MediaCreateObject $mediaCreateObject
     * @return MediaObject
     */
    public function persist(MediaCreateObject $mediaCreateObject){

        /**
         * Select existing instead
         */
        try {
            $hash = $mediaCreateObject->getHash();
            if(!empty($hash)) {
                return $this->byHash($hash);
            }
        } catch(MediaNotFoundException $e){
            //ignore - need to insert
        }

        /**
         * Insert record
         */
        $id = CoreSqlUtils::insert(self::INSERT_MEDIA_SQL, array(
            ':name' => CoreStringUtils::limitString($mediaCreateObject->getFilename(), 150),
            ':description' => $mediaCreateObject->getFilename(),
            ':type' => $mediaCreateObject->getType(),
            ':size' => (int) $mediaCreateObject->getSize(),
            ':color' => $mediaCreateObject->getBackgroundColor(),
            ':width' => (int) $mediaCreateObject->getWidth(),
            ':height' => (int) $mediaCreateObject->getHeight(),
            ':hash' => $mediaCreateObject->getHash(),
            ':data' => $mediaCreateObject->getData()
        ));

        /** @var MediaObject $MediaObject */
        $MediaObject = CoreLogic::getObject('MediaObject');
        $MediaObject->setId($id);
        $MediaObject->setName($mediaCreateObject->getFilename());
        $MediaObject->setDescription('');
        $MediaObject->setType($mediaCreateObject->getType());
        $MediaObject->setSize($mediaCreateObject->getSize());
        $MediaObject->setBackgroundColor($mediaCreateObject->getBackgroundColor());
        $MediaObject->setHeight($mediaCreateObject->getHeight());
        $MediaObject->setWidth($mediaCreateObject->getWidth());
        $MediaObject->setPath('/api/v1/media/' . $id);
        $MediaObject->setStream('/stream/v1/media/' . $id);
        $MediaObject->setDownload('/stream/v1/media/' . $id . '/download');
        $MediaObject->setDateAdded(time());

        /**
         * Image check
         */
        $MediaObject = $this->isImageCheck($MediaObject);

        /**
         * Return the object
         */
        return $MediaObject;

    }

    /**
     * Lookup by hash
     *
     * @param null $hash
     * @return mixed
     * @throws MediaNotFoundException
     */
    public function byHash($hash = null){

        /**
         * Lookup media by hash
         */
        $mediaRow = CoreSqlUtils::row(self::SQL_MEDIA_BY_HASH, array(
            ':hash' => $hash
        ));

        /**
         * Throw exception when not found
         */
        if(empty($mediaRow)) throw new MediaNotFoundException();

        /**
         * Return the object
         */
        return $this->populateMediaObject($mediaRow);
    }

    /**
     * Get media object
     *
     * @param MediaRequestObject $mediaRequestObject
     * @return MediaObject
     * @throws MediaNotFoundException
     */
    public function read(MediaRequestObject $mediaRequestObject){

        /**
         * Lookup media in database
         */
        $mediaRow = CoreSqlUtils::row(self::GET_MEDIA_SQL, array(
           ':id' => $mediaRequestObject->getId()
        ));

        /**
         * Throw exception when not found
         */
        if(empty($mediaRow)) throw new MediaNotFoundException();

        /**
         * Return the object
         */
        return $this->populateMediaObject($mediaRow);

    }

    /**
     * Populate mediaObject
     *
     * @param array $mediaRow
     * @return MediaObject
     */
    private function populateMediaObject($mediaRow = array()){

        /** @var MediaObject $MediaObject */
        $MediaObject = CoreLogic::getObject('MediaObject');
        $MediaObject->setId($mediaRow['werock_media_id']);
        $MediaObject->setName($mediaRow['werock_media_name']);
        $MediaObject->setDescription($mediaRow['werock_media_description']);
        $MediaObject->setType($mediaRow['werock_media_type']);
        $MediaObject->setSize($mediaRow['werock_media_size']);
        $MediaObject->setBackgroundColor($mediaRow['werock_media_background_color']);
        $MediaObject->setWidth($mediaRow['werock_media_width']);
        $MediaObject->setHeight($mediaRow['werock_media_height']);
        $MediaObject->setHash($mediaRow['werock_media_hash']);
        $MediaObject->setPath('/api/v1/media/' . (int) $mediaRow['werock_media_id']);
        $MediaObject->setStream('/stream/v1/media/' . (int) $mediaRow['werock_media_id']);
        $MediaObject->setDownload('/stream/v1/media/' . (int) $mediaRow['werock_media_id'] . '/download');
        $MediaObject->setLastModified($mediaRow['werock_media_last_modified']);
        $MediaObject->setDateAdded($mediaRow['werock_media_date_added']);

        /**
         * Image check
         */
        $MediaObject = $this->isImageCheck($MediaObject);

        /**
         * Return the object
         */
        return $MediaObject;

    }

    /**
     * Get stream object
     *
     * @param MediaRequestObject $mediaRequestObject
     * @return MediaStreamObject
     * @throws MediaNotFoundException
     */
    public function stream(MediaRequestObject $mediaRequestObject){

        /**
         * Lookup media in database
         */
        $mediaRow = CoreSqlUtils::row(self::GET_MEDIA_SQL, array(
            ':id' => $mediaRequestObject->getId()
        ));

        /**
         * Throw exception when not found
         */
        if(empty($mediaRow)) throw new MediaNotFoundException();

        /** @var MediaStreamObject $MediaStreamObject */
        $MediaStreamObject = CoreLogic::getObject('MediaStreamObject');
        $MediaStreamObject->setName($mediaRow['werock_media_name']);
        $MediaStreamObject->setType($mediaRow['werock_media_type']);
        $MediaStreamObject->setData($mediaRow['werock_media_data']);
        $MediaStreamObject->setSize($mediaRow['werock_media_size']);
        $MediaStreamObject->setAdded($mediaRow['werock_media_date_added']);
        $MediaStreamObject->setModified($mediaRow['werock_media_last_modified']);

        /**
         * Return the object
         */
        return $MediaStreamObject;

    }

    /**
     * Check to see if this is an image
     *
     * @param MediaObject $mediaObject
     * @return bool
     */
    private function isImageCheck(MediaObject $mediaObject){

        // pick image type
        switch ($mediaObject->getType()) {
            case "image/gif":
                $mediaObject->setIsImage(true);
                break;
            case "image/jpeg":
                $mediaObject->setIsImage(true);
                break;
            case "image/png":
                $mediaObject->setIsImage(true);
                break;
            case "image/bmp":
                $mediaObject->setIsImage(true);
                break;
            default:
                $mediaObject->setIsImage(false);
                break;
        }

        // return media object
        return $mediaObject;

    }

}