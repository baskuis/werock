<?php

class IntelligenceNavigationDetailsObject {

    public $requestURI;
    public $pageView;

    /**
     * @param mixed $pageView
     */
    public function setPageView($pageView)
    {
        $this->pageView = $pageView;
    }

    /**
     * @return mixed
     */
    public function getPageView()
    {
        return $this->pageView;
    }

    /**
     * @param mixed $requestURI
     */
    public function setRequestURI($requestURI)
    {
        $this->requestURI = $requestURI;
    }

    /**
     * @return mixed
     */
    public function getRequestURI()
    {
        return $this->requestURI;
    }

}