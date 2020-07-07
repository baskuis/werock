<?php

class ExperimentationSummaryObject {

    /** @var int $exposures */
    public $exposures;

    /** @var int $conversions */
    public $conversions;

    /** @var float $baseConversionRate */
    public $baseConversionRate;

    /** @var bool $conclusive */
    public $conclusive;

    /**
     * @return int
     */
    public function getExposures()
    {
        return $this->exposures;
    }

    /**
     * @param int $exposures
     */
    public function setExposures($exposures)
    {
        $this->exposures = $exposures;
    }

    /**
     * @return int
     */
    public function getConversions()
    {
        return $this->conversions;
    }

    /**
     * @param int $conversions
     */
    public function setConversions($conversions)
    {
        $this->conversions = $conversions;
    }

    /**
     * @return float
     */
    public function getBaseConversionRate()
    {
        return $this->baseConversionRate;
    }

    /**
     * @param float $baseConversionRate
     */
    public function setBaseConversionRate($baseConversionRate)
    {
        $this->baseConversionRate = $baseConversionRate;
    }

    /**
     * @return boolean
     */
    public function isConclusive()
    {
        return $this->conclusive;
    }

    /**
     * @param boolean $conclusive
     */
    public function setConclusive($conclusive)
    {
        $this->conclusive = $conclusive;
    }

}