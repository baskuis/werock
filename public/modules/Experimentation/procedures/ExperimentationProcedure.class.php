<?php

class ExperimentationProcedure {

    /** @var ExperimentationRepository $ExperimentationRepository */
    private $ExperimentationRepository = null;

    /** @var IntelligenceService $IntelligenceService */
    private $IntelligenceService = null;

    /** @var UserGroupService $UserGroupService */
    private $UserGroupService = null;

    function __construct()
    {
        $this->ExperimentationRepository = CoreLogic::getRepository('ExperimentationRepository');
        $this->IntelligenceService = CoreLogic::getService('IntelligenceService');
        $this->UserGroupService = CoreLogic::getService('UserGroupService');
    }

    /**
     * Get experiments
     *
     * @return array
     */
    public function getExperiments(){
        $return = array();
        if(false !== ($rows = $this->ExperimentationRepository->getExperiments())){
            foreach($rows as &$row){

                /** @var ExperimentationObject $E */
                $E = CoreObjectUtils::applyRow('ExperimentationObject', $row);
                $E->setVariants($this->getExperimentVariants($E));
                $this->chooseVariant($E);
                $E->setIntelligenceDataObject($this->IntelligenceService->getIntelligenceData($E->getIntelligenceDataId()));
                $groupId = $E->getGroupId();
                if(!empty($groupId)) {
                    $E->setUserGroupObject($this->UserGroupService->getGroup($groupId));
                }
                array_push($return, $E);

            }
        }
        return $return;
    }

    /**
     * Get experiment variants
     *
     * @param ExperimentationObject $ExperimentationObject
     * @return array
     */
    public function getExperimentVariants($ExperimentationObject){
        $return = array();
        if(false !== ($rows = $this->ExperimentationRepository->getExperimentVariants($ExperimentationObject->getId()))){
            foreach($rows as &$row){

                /** @var ExperimentationVariantObject $ExperimentationVariantObject */
                $ExperimentationVariantObject = CoreObjectUtils::applyRow('ExperimentationVariantObject', $row);

                array_push($return, $ExperimentationVariantObject);

            }
        }
        return $return;
    }

    /**
     * Assure entry
     *
     * @param ExperimentationObject $experimentationObject
     * @return bool
     * @throws Exception
     */
    public function assureEntry(ExperimentationObject $experimentationObject){
        if(false === ($ExperimentationVariantEntryObject = $this->getEntry($experimentationObject->getVariant()))){
            $CoreVisitorObject = CoreVisitor::getVisitor();
            if($CoreVisitorObject->getHits() < $experimentationObject->getMinHists()){
                return false;
            }
            if(false === $this->ExperimentationRepository->insertEntry($CoreVisitorObject->getId(), $experimentationObject->getVariant()->getId())){
                throw new Exception();
            }
        }
        return true;
    }

    /**
     * Get an entry
     *
     * @param ExperimentationVariantObject $experimentationVariantObject
     * @return bool|object
     */
    public function getEntry(ExperimentationVariantObject $experimentationVariantObject){
        if(empty($experimentationVariantObject)){ CoreLog::error('no ExperimentationVariantObject'); }
        if(false !== ($row = $this->ExperimentationRepository->getEntry(CoreVisitor::getId(), $experimentationVariantObject->getId()))){
            return CoreObjectUtils::applyRow('ExperimentationVariantEntryObject', $row);
        }
        return false;
    }

    /**
     * Update entry
     *
     * @param ExperimentationVariantObject $e
     * @param null $value
     * @return True
     */
    public function updateEntry(ExperimentationVariantObject $e, $value = null){
        return $this->ExperimentationRepository->updateEntry(CoreVisitor::getId(), $e->getId(), $value);
    }

    /**
     * Get variant summary
     *
     * @param ExperimentationVariantObject $v
     * @return ExperimentationVariantEntrySummaryObject
     * @throws Exception
     */
    public function getVariantSummary(ExperimentationVariantObject $v){
        if(false !== ($row = $this->ExperimentationRepository->getEntrySummary($v->getId()))){
            return CoreObjectUtils::applyRow('ExperimentationVariantEntrySummaryObject', $row);
        }
        throw new Exception('ExperimentationVariantSummary could not be populated');
    }

    /**
     * Choose a random variant for each visitor
     *
     * @param ExperimentationObject $experimentationObject
     * @return void
     */
    public function chooseVariant(ExperimentationObject $experimentationObject) {
        $variants = $experimentationObject->getVariants();
        if(empty($variants)) return;
        $key = CoreHashUtils::murmurhash3_int(CoreVisitor::getId() . CoreStringUtils::DASH . $experimentationObject->getId()) % sizeof($variants) + 1;
        $experimentationObject->setVariant($variants[$key - 1]);
    }

}