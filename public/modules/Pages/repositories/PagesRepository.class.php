<?php

class PagesRepository {

    /**
     * SQL statements
     */
    const SQL_GET_PAGES = "
      SELECT 
        *
      FROM 
        werock_pages
    ";
    const SQL_GET_PAGE_BY_ID = "
      SELECT 
        *
      FROM 
        werock_pages
      WHERE 
        werock_page_id = :id
    ";

    /**
     * Get pages
     *
     * @return array
     */
    public function getPages(){
        return CoreSqlUtils::rows(self::SQL_GET_PAGES, array());
    }

    /**
     * Get page
     *
     * @param null $id
     * @return array
     */
    public function getPage($id = null){
        return CoreSqlUtils::row(self::SQL_GET_PAGE_BY_ID, array(
            ':id' => (int) $id
        ));
    }

}