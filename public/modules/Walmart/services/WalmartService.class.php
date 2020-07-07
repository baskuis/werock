<?php

/**
 * Class WalmartService
 *
 */
class WalmartService {

    /** @var WalmartProcedure $WalmartProcedure */
    private $WalmartProcedure;

    public function __construct(){
        $this->WalmartProcedure = CoreLogic::getProcedure('WalmartProcedure');
    }

    /**
     * Search
     *
     * @param $query
     * @param $page
     * @param null $sort
     * @param null $category
     * @param null $tag
     * @return array|bool
     */
    public function search($query, $page, $sort = null, $category = null, $tag = null){
        try {
            return $this->WalmartProcedure->search($query, $page, $sort, $category, $tag);
        } catch(Exception $e){
            CoreNotification::set($e->getMessage(), CoreNotification::ERROR);
        }
        return false;
    }

    public function upc($upc = null){
        try {
            return $this->WalmartProcedure->lookup($upc);
        } catch(Exception $e){
            CoreNotification::set($e->getMessage(), CoreNotification::ERROR);
        }
        return false;
    }

}