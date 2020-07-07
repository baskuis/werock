<?php

class SSOProcedure {

    /** @var SSORepository $SSORepository */
    private $SSORepository;

    function __construct(){
        $this->SSORepository = CoreLogic::getRepository('SSORepository');
    }

}

