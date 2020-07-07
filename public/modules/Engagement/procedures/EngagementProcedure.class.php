<?php

/**
 * Engagement Procedure
 *
 * PHP version 7
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class EngagementProcedure {

    /** @var EngagementRepository $EngagementRepository */
    private $EngagementRepository;

    function __construct(){
        $this->EngagementRepository = CoreLogic::getRepository('EngagementRepository');
    }

    /**
     * Capture email
     *
     * @param EngagementEmailCaptureObject $engagementEmailCaptureObject
     * @return array
     * @throws Exception
     */
    public function captureEmail(EngagementEmailCaptureObject $engagementEmailCaptureObject){
        if(filter_var($engagementEmailCaptureObject->getEmail(), FILTER_VALIDATE_EMAIL) === false){
            throw new Exception('Invalid email');
        }
        if(empty($engagementEmailCaptureObject->name)){
            throw new Exception('Name required');
        }
        if(empty($engagementEmailCaptureObject->tag)){
            throw new Exception('Tag required');
        }
        $inserted = $this->EngagementRepository->insertEngagementEmail(
            $engagementEmailCaptureObject->getTag(),
            $engagementEmailCaptureObject->getEmail(),
            $engagementEmailCaptureObject->getName()
        );
        if(!$inserted) throw new Exception('Unable to capture information');
        return $inserted;
    }

    /**
     * List by tag prepend
     *
     * @param string $prepend
     * @param string $since
     * @return array
     */
    public function listByTagPrepend($prepend = null, $since = null){
        $return = array();
        $rows = $this->EngagementRepository->listEngagementsEmailsByTagPrepend($prepend, $since);
        if(!empty($rows)){
            foreach($rows as $row){
                array_push($return, CoreObjectUtils::applyRow('EngagementEmailEntryObject', $row));
            }
        }
        return $return;
    }

    /**
     * Delete engagement by id
     *
     * @param null $id
     * @return bool
     */
    public function deleteById($id = null){
        return $this->EngagementRepository->deleteEngagementEmail($id);
    }

    /**
     * Update last sent
     *
     * @param $id
     * @return mixed
     */
    public function updateLastSent($id){
        return $this->EngagementRepository->updateLastSent($id);
    }

}