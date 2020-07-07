<?php

class IntelligenceTableRangeResponseObject {

    /** @var IntelligenceTableRangeRequestObject $IntelligenceTableRangeRequestObject */
    public $IntelligenceTableRangeRequestObject;

    public $label;
    public $start;
    public $niceStart;
    public $end;
    public $niceEnd;

    public $count;

    /**
     * @return IntelligenceTableRangeRequestObject
     */
    public function getIntelligenceTableRangeRequestObject()
    {
        return $this->IntelligenceTableRangeRequestObject;
    }

    /**
     * @param IntelligenceTableRangeRequestObject $IntelligenceTableRangeRequestObject
     */
    public function setIntelligenceTableRangeRequestObject($IntelligenceTableRangeRequestObject)
    {
        $this->IntelligenceTableRangeRequestObject = $IntelligenceTableRangeRequestObject;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
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
    public function getStart()
    {
        return $this->start;
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
    public function getNiceStart()
    {
        return $this->niceStart;
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
    public function getEnd()
    {
        return $this->end;
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
    public function getNiceEnd()
    {
        return $this->niceEnd;
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
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

}