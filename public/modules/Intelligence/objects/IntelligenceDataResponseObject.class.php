<?php

class IntelligenceDataResponseObject {

    /** @var IntelligenceDataRequestObject $IntelligenceDataRequestObject */
    public $IntelligenceDataRequestObject;

    public $label;
    public $start;
    public $niceStart;
    public $end;
    public $niceEnd;

    /**
     * Keys: text, count
     * @var array
     */
    public $values;

    /**
     * @param \IntelligenceDataRequestObject $IntelligenceDataRequestObject
     */
    public function setIntelligenceDataRequestObject($IntelligenceDataRequestObject)
    {
        $this->IntelligenceDataRequestObject = $IntelligenceDataRequestObject;
    }

    /**
     * @return \IntelligenceDataRequestObject
     */
    public function getIntelligenceDataRequestObject()
    {
        return $this->IntelligenceDataRequestObject;
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param mixed $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * @return mixed
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param mixed $niceEnd
     */
    public function setNiceEnd($niceEnd)
    {
        $this->niceEnd = $niceEnd;
    }

    /**
     * @return mixed
     */
    public function getNiceEnd()
    {
        return $this->niceEnd;
    }

    /**
     * @param mixed $niceStart
     */
    public function setNiceStart($niceStart)
    {
        $this->niceStart = $niceStart;
    }

    /**
     * @return mixed
     */
    public function getNiceStart()
    {
        return $this->niceStart;
    }

}