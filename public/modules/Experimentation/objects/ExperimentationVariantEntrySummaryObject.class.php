<?php

class ExperimentationVariantEntrySummaryObject {

    /** @var int $exposures */
    public $exposures;

    /** @var int $conversions */
    public $conversions;

    /** @var $conversionRate */
    public $conversionRate;

    /** @var float $standardDeviation */
    public $standardDeviation;

    /** @var float $standardError */
    public $standardError;

    /** @var float $standardErrorPercentage */
    public $standardErrorPercentage;

    /** @var float $zValue */
    private $zValue = 1.282; //(1.64) 90% confidence -- (1.282 80% confidence)
    public $confidencePercentage = 80;

    /** @var float $conversionRatePercentage */
    public $conversionRatePercentage;

    /** @var float $lowerBound */
    public $lowerBound;

    /** @var float $lowerBoundPercentage */
    public $lowerBoundPercentage;

    /** @var float $upperBound */
    public $upperBound;

    /** @var float $upperBoundPercentage */
    public $upperBoundPercentage;

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
     * @return int
     */
    public function getConfidencePercentage()
    {
        return $this->confidencePercentage;
    }

    /**
     * @param int $confidencePercentage
     */
    public function setConfidencePercentage($confidencePercentage)
    {
        $this->confidencePercentage = $confidencePercentage;
    }

    /**
     * @return mixed
     */
    public function getConversionRate()
    {
        return $this->conversionRate;
    }

    /**
     * @param mixed $conversionRate
     */
    public function setConversionRate($conversionRate)
    {
        $this->conversionRate = $conversionRate;
    }

    /**
     * @return float
     */
    public function getStandardDeviation()
    {
        return $this->standardDeviation;
    }

    /**
     * @param float $standardDeviation
     */
    public function setStandardDeviation($standardDeviation)
    {
        $this->standardDeviation = $standardDeviation;
    }

    /**
     * @return float
     */
    public function getStandardError()
    {
        return $this->standardError;
    }

    /**
     * @param float $standardError
     */
    public function setStandardError($standardError)
    {
        $this->standardError = $standardError;
    }

    /**
     * @return float
     */
    public function getStandardErrorPercentage()
    {
        return $this->standardErrorPercentage;
    }

    /**
     * @param float $standardErrorPercentage
     */
    public function setStandardErrorPercentage($standardErrorPercentage)
    {
        $this->standardErrorPercentage = $standardErrorPercentage;
    }

    /**
     * @return float
     */
    public function getConversionRatePercentage()
    {
        return $this->conversionRatePercentage;
    }

    /**
     * @param float $conversionRatePercentage
     */
    public function setConversionRatePercentage($conversionRatePercentage)
    {
        $this->conversionRatePercentage = $conversionRatePercentage;
    }

    /**
     * @return float
     */
    public function getLowerBound()
    {
        return $this->lowerBound;
    }

    /**
     * @param float $lowerBound
     */
    public function setLowerBound($lowerBound)
    {
        $this->lowerBound = $lowerBound;
    }

    /**
     * @return float
     */
    public function getLowerBoundPercentage()
    {
        return $this->lowerBoundPercentage;
    }

    /**
     * @param float $lowerBoundPercentage
     */
    public function setLowerBoundPercentage($lowerBoundPercentage)
    {
        $this->lowerBoundPercentage = $lowerBoundPercentage;
    }

    /**
     * @return float
     */
    public function getUpperBound()
    {
        return $this->upperBound;
    }

    /**
     * @param float $upperBound
     */
    public function setUpperBound($upperBound)
    {
        $this->upperBound = $upperBound;
    }

    /**
     * @return float
     */
    public function getUpperBoundPercentage()
    {
        return $this->upperBoundPercentage;
    }

    /**
     * @param float $upperBoundPercentage
     */
    public function setUpperBoundPercentage($upperBoundPercentage)
    {
        $this->upperBoundPercentage = $upperBoundPercentage;
    }

    /**
     * Calculate
     */
    public function calculate(){

        $this->conversionRate = ($this->exposures > 0) ? $this->conversions / $this->exposures : 0;
        $this->standardDeviation = sqrt($this->exposures * $this->conversionRate * (1 - $this->conversionRate));
        if (sqrt($this->exposures) > 0) {
            $this->standardError = $this->standardDeviation / sqrt($this->exposures);
        } else {
            $this->lowerBoundPercentage = number_format(0, 2);
            $this->upperBoundPercentage = number_format(100, 2);
            $this->conversionRatePercentage = number_format(0, 2);
            return;
        }
        $this->standardErrorPercentage = number_format($this->standardError * 100, 2);

        $this->conversionRatePercentage = number_format($this->conversionRate * 100, 2);

        $this->lowerBound = abs((
            $this->conversionRate +
            (pow($this->zValue, 2) / (2 * $this->exposures)) -
            ($this->zValue * sqrt(
                (
                    $this->conversionRate * (1 - $this->conversionRate) +
                    (pow($this->zValue, 2) / (4 * $this->exposures))
                ) / $this->exposures)
            )
        ) / (1 + (pow($this->zValue, 2) / $this->exposures)));
        $this->upperBound = abs((
            $this->conversionRate +
            (pow($this->zValue, 2) / (2 * $this->exposures)) +
            ($this->zValue * sqrt(
                    (
                        $this->conversionRate * (1 - $this->conversionRate) +
                        (pow($this->zValue, 2) / (4 * $this->exposures))
                    ) / $this->exposures)
            )
        ) / (1 + (pow($this->zValue, 2) / $this->exposures)));

        $this->lowerBoundPercentage = number_format($this->lowerBound * 100, 2);
        $this->upperBoundPercentage = number_format($this->upperBound * 100, 2);

    }

}