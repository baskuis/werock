<?php

/**
 * Engagement Service
 *
 * PHP version 7
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class EngagementService {

    const ENGAGEMENT_EMAIL_CAPTURED = 'engagement:email:captured';

    /** @var EngagementCollectEmailObject */
    private static $EngagementCollectEmailObject;

    /** @var EngagementProcedure $EngagementProcedure */
    private $EngagementProcedure;

    function __construct(){
        $this->EngagementProcedure = CoreLogic::getProcedure('EngagementProcedure');
    }

    /**
     * @return EngagementCollectEmailObject
     */
    public static function getEngagementCollectEmailObject()
    {
        return self::$EngagementCollectEmailObject;
    }

    /**
     * @param EngagementCollectEmailObject $EngagementCollectEmailObject
     */
    public static function setEngagementCollectEmailObject($EngagementCollectEmailObject)
    {
        self::$EngagementCollectEmailObject = $EngagementCollectEmailObject;
    }

    public function hasEngagementCollectEmailObject(){
        return !empty(self::$EngagementCollectEmailObject);
    }

    /**
     * Capture email
     *
     * @param EngagementEmailCaptureObject $engagementEmailCaptureObject
     * @return bool
     */
    public function captureEmail(EngagementEmailCaptureObject $engagementEmailCaptureObject){
        try {
            $id = $this->EngagementProcedure->captureEmail($engagementEmailCaptureObject);
            CoreNotification::set('You will receive updates!', CoreNotification::SUCCESS);
            if(!empty($id)){
                CoreObserver::dispatch(self::ENGAGEMENT_EMAIL_CAPTURED, $engagementEmailCaptureObject);
                return true;
            }
        } catch(Exception $e){
            CoreNotification::set($e->getMessage(), CoreNotification::ERROR);
        }
        return false;
    }

    /**
     * Delete EngagementEmailEntryObject
     *
     * @param EngagementEmailEntryObject $engagementEmailEntryObject
     * @return bool
     */
    public function delete(EngagementEmailEntryObject $engagementEmailEntryObject){
        try {
            if(empty($engagementEmailEntryObject) || get_class($engagementEmailEntryObject) != EngagementEmailEntryObject::class){
                throw new Exception('Valid engagement entry needed');
            }
            return $this->EngagementProcedure->deleteById($engagementEmailEntryObject->getId());
        } catch(Exception $e){
            CoreNotification::set($e->getMessage(), CoreNotification::ERROR);
        }
    }

    /**
     * Get by tag prepend
     *
     * @param string $prepend
     * @param string $since
     * @return array (of EngagementEmailEntryObject)
     */
    public function getByTagPrepend($prepend = null, $since = null){
        try {
            return $this->EngagementProcedure->listByTagPrepend($prepend, $since);
        } catch(Exception $e){
            CoreNotification::set($e->getMessage(), CoreNotification::ERROR);
        }
    }

    /**
     * Update last sent
     *
     * @param null $id
     * @return mixed
     */
    public function updateLastSent($id = null){
        try {
            return $this->EngagementProcedure->updateLastSent($id);
        } catch(Exception $e){
            CoreNotification::set($e->getMessage(), CoreNotification::ERROR);
        }
    }

}