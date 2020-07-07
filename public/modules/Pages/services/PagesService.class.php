<?php

class PagesService {

    /** @var PagesProcedure $PagesProcedure */
    private $PagesProcedure;

    function __construct(){
        $this->PagesProcedure = CoreLogic::getProcedure('PagesProcedure');
    }

    /**
     * @return array
     */
    function getPages() {
        try {
            return $this->PagesProcedure->getPages();
        } catch (Exception $e) {
            CoreNotification::set($e->getMessage(), CoreNotification::ERROR);
        }
        return null;
    }

    function getPage($id) {
        try {
            return $this->PagesProcedure->getPage($id);
        } catch (Exception $e) {
            CoreNotification::set($e->getMessage(), CoreNotification::ERROR);
        }
        return null;
    }

}