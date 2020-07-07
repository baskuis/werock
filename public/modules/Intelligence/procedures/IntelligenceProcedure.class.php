<?php

/**
 * Intelligence procedure
 * Allows for tracking of data for analytical purposes
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class IntelligenceProcedure {

    /** @var IntelligenceRepository $IntelligenceRepository */
    private $IntelligenceRepository;

    function __construct(){
        $this->IntelligenceRepository = CoreLogic::getRepository('IntelligenceRepository');
    }

    /**
     * Get range of records added
     *
     * @param IntelligenceTableRangeRequestObject $intelligenceTableRangeRequestObject
     * @return array
     * @throws IntelligenceIntervalTooSmallException
     * @throws IntelligenceInvalidRangeException
     */
    public function getRecordsAdded(IntelligenceTableRangeRequestObject $intelligenceTableRangeRequestObject){

        /** Assertions */
        if($intelligenceTableRangeRequestObject->getTo() - $intelligenceTableRangeRequestObject->getFrom() < 0){
            throw new IntelligenceInvalidRangeException();
        }
        if(($intelligenceTableRangeRequestObject->getTo() - $intelligenceTableRangeRequestObject->getFrom()) / $intelligenceTableRangeRequestObject->getInterval() > 300){
            throw new IntelligenceIntervalTooSmallException();
        }

        /** Call dao */
        return $this->IntelligenceRepository->getRecordsAdded($intelligenceTableRangeRequestObject);

    }

    /**
     * Get intelligence data
     *
     * @param null $dataId
     * @return bool|object
     */
    public function getIntelligenceData($dataId = null){
        if(false !== ($row = $this->IntelligenceRepository->getIntelligenceData($dataId))){
            return CoreObjectUtils::applyRow('IntelligenceDataObject', $row);
        }
        return false;
    }

    /**
     * Get intelligence data
     *
     * @param IntelligenceDataRequestObject $intelligenceDataRequestObject
     * @return mixed
     * @throws IntelligenceIntervalTooSmallException
     * @throws IntelligenceInvalidRangeException
     */
    public function getData(IntelligenceDataRequestObject $intelligenceDataRequestObject){

        /** Assertions */
        if($intelligenceDataRequestObject->getTo() - $intelligenceDataRequestObject->getFrom() < 0){
            throw new IntelligenceInvalidRangeException();
        }
        if(($intelligenceDataRequestObject->getTo() - $intelligenceDataRequestObject->getFrom()) / $intelligenceDataRequestObject->getInterval() > 300){
            throw new IntelligenceIntervalTooSmallException();
        }

        /** Call dao */
        return $this->IntelligenceRepository->getData($intelligenceDataRequestObject);

    }

	/**
	 * Inserts intelligence stack
	 * @param Array $stack_to_save
	 * @return bool Return true when saved and false otherwise
	 */
	public function insertIntelligenceStack($stack_to_save = array()){

		/** Insert into database */
		return $this->IntelligenceRepository->insertIntelligenceStack($stack_to_save);
		
	}

}