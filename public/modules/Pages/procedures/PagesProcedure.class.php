<?php

class PagesProcedure {

    /** @var PagesRepository $PagesRepository */
    private $PagesRepository;

    function __construct(){
        $this->PagesRepository = CoreLogic::getRepository('PagesRepository');
    }

    function getPages() {
        $pages = array();
        $rows = $this->PagesRepository->getPages();
        foreach ($rows as $row) {

            /** @var PagesPageObject $PageObject */
            $PagesPageObject = CoreObjectUtils::applyRow('PagesPageObject', $row);
            array_push($pages, $PagesPageObject);

        }
        return $pages;
    }

    /**
     * Get page by id
     *
     * @param int $id
     * @return PagesPageObject
     */
    function getPage($id) {

        /** @var PagesPageObject $PagesPageObject */
        $PagesPageObject = CoreObjectUtils::applyRow('PagesPageObject',
            $this->PagesRepository->getPage($id)
        );
        return $PagesPageObject;

    }

}