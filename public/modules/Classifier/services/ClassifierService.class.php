<?php

class ClassifierService implements ClassifierServiceInterface {

    /**
     * Submit event to classifier
     *
     * @param ClassifierEventRequestObject $ClassifierEventRequestObject
     * @return bool
     */
    public function submit(ClassifierEventRequestObject $ClassifierEventRequestObject){

        return true;

    }

    /**
     * Infer probability from classifier
     *
     * @param ClassifierEventRequestObject $ClassifierEventRequestObject
     * @return ClassifierEventResponseObject
     */
    public function infer(ClassifierEventRequestObject $ClassifierEventRequestObject){

        /** @var ClassifierEventResponseObject $ClassifierEventResponseObject */
        $ClassifierEventResponseObject = CoreLogic::getObject('ClassifierEventResponseObject');
        return $ClassifierEventResponseObject;

    }

}