<?php

class ClassifierEventResponseObject {

    /** @var ClassifierEventObject $ClassifierEventObject */
    public $ClassifierEventObject;

    /**
     * @return ClassifierEventObject
     */
    public function getClassifierEventObject()
    {
        return $this->ClassifierEventObject;
    }

    /**
     * @param ClassifierEventObject $ClassifierEventObject
     */
    public function setClassifierEventObject($ClassifierEventObject)
    {
        $this->ClassifierEventObject = $ClassifierEventObject;
    }

}