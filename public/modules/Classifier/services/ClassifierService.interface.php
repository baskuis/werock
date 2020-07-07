<?php

interface ClassifierServiceInterface {

    public function submit(ClassifierEventRequestObject $ClassifierEventRequestObject);

    public function infer(ClassifierEventRequestObject $ClassifierEventRequestObject);

}