<?php

class ExperimentationService {

    /** @var ExperimentationProcedure $ExperimentationProcedure */
    private $ExperimentationProcedure;

    /** @var UserEntitlementService $UserEntitlementService */
    private $UserEntitlementService;

    /** @var array $experiments */
    private $experiments;

    function __construct()
    {
        $this->ExperimentationProcedure = CoreLogic::getProcedure('ExperimentationProcedure');
        $this->UserEntitlementService = CoreLogic::getService('UserEntitlementService');
    }

    /**
     * Get experiments
     *
     * @return array
     */
    public function getExperiments(){
        return $this->ExperimentationProcedure->getExperiments();
    }

    /**
     * Build experiments
     */
    public function buildExperiments(){
        $this->experiments = $this->ExperimentationProcedure->getExperiments();
        /** @var ExperimentationObject $ExperimentationObject */
        foreach($this->experiments as &$ExperimentationObject){
            $this->ExperimentationProcedure->chooseVariant($ExperimentationObject);
        }
    }

    /**
     * @return ExperimentationSummaryObject
     */
    public function buildExperimentSummary(ExperimentationObject $e){

        /** @var ExperimentationSummaryObject $ExperimentationSummaryObject */
        $ExperimentationSummaryObject = CoreLogic::getObject('ExperimentationSummaryObject');

        /** @var bool $conclusive */
        $conclusive = true;

        /** @var ExperimentationVariantObject $b */
        $b = $e->getBase();

        /** @var ExperimentationVariantEntrySummaryObject $bs */
        $bs = $b->getExperimentationVariantEntrySummaryObject();;
        $ExperimentationSummaryObject->setBaseConversionRate($bs->getConversionRatePercentage());

        /** @var array $variants */
        $variants = $e->getVariants();
        /** @var ExperimentationVariantObject $variant */
        foreach($variants as &$variant){
            $s = $variant->getExperimentationVariantEntrySummaryObject();
            if(empty($s)){
                CoreLog::error('No ExperimentationVariantEntrySummaryObject summary');
            }
            if($bs->getLowerBound() < $s->getUpperBound()){
                $conclusive = false;
            }
        }

        $ExperimentationSummaryObject->setConclusive($conclusive);

        return $ExperimentationSummaryObject;

    }

    /**
     * Set variant summary
     *
     * @param ExperimentationVariantObject $v
     * @return bool|ExperimentationVariantEntrySummaryObject
     */
    public function setVariantSummary(ExperimentationVariantObject $v){
        try {
            $s = $this->ExperimentationProcedure->getVariantSummary($v);
            $v->setExperimentationVariantEntrySummaryObject($s);
            return $s;
        } catch(Exception $e){
            CoreNotification::set('Unable to get variant summary. Info: ' . $e->getMessage(), CoreNotification::ERROR);
        }
        return false;
    }

    /**
     * Handle an event
     *
     * @param null $key
     * @param null $value
     */
    public function handleEvent($key = null, $value = null){

        /** @var ExperimentationObject $E */
        foreach($this->experiments as &$E){
            $I = $E->getIntelligenceDataObject();
            if(!empty($I) && $key == $I->getText()) {
                if ($E->getMatchType() == 'match' && CoreStringUtils::compare($E->getMatchValue(), $value)) {
                    $this->ExperimentationProcedure->updateEntry($E->getVariant(), $value);
                    continue;
                }
                if ($E->getMatchType() == 'regex' && preg_match($E->getMatchValue(), $value)) {
                    $this->ExperimentationProcedure->updateEntry($E->getVariant(), $value);
                    continue;
                }
            }

        }

    }

    /**
     * Handle template
     *
     * @param null $template
     * @return string
     */
    public function handleTemplate($template = null){
        try {
            /** @var ExperimentationObject $ExperimentationObject */
            foreach ($this->experiments as &$ExperimentationObject) {
                if ($ExperimentationObject->getType() === $ExperimentationObject::TYPE_TEMPLATE && $ExperimentationObject->getTemplate() === $template) {
                    $ExperimentationVariantObject = $ExperimentationObject->getVariant();
                    if (!empty($ExperimentationVariantObject) && $this->ExperimentationProcedure->assureEntry($ExperimentationObject)) {
                        return $ExperimentationVariantObject->getTemplate();
                    }
                }
            }
        } catch(Exception $e){
            CoreNotification::set('Unable to handle template ' . $template);
        }
        return $template;
    }

    /**
     * Render javascript string
     *
     * @return string
     */
    public function renderJavascript(){
        $javascript = '';
        try {
            /** @var ExperimentationObject $ExperimentationObject */
            foreach ($this->experiments as &$ExperimentationObject) {
                if ($ExperimentationObject->getType() === $ExperimentationObject::TYPE_JS) {
                    $ExperimentationVariantObject = $ExperimentationObject->getVariant();
                    if (!empty($ExperimentationVariantObject) && $this->ExperimentationProcedure->assureEntry($ExperimentationObject)) {
                        $javascript .= "\n" . '//Experiment:' . $ExperimentationVariantObject->getName() . "\n" . $ExperimentationVariantObject->getJavascript() . "\n";
                    }
                }
            }
        } catch(Exception $e){
            CoreNotification::set('Unable to render javascript');
        }
        return $javascript;
    }

}