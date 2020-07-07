<?php

/**
 * Search suggestions repository
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class SearchSuggestionsRepository {

    /** SQL string - insert search */
    const SQL_INSERT_SEARCH = "
      INSERT INTO
          `werock_searches`
        (
          `werock_user_id`,
          `werock_visitor_id`,
          `werock_search_urn`,
          `werock_search_text`,
          `werock_search_found`,
          `werock_search_date_added`
        ) VALUES (
          :user,
          :visitor,
          :urn,
          :text,
          :found,
          NOW()
        ); ";

    /** SQL string - suggestions */
    const SQL_INSERT_SUGGESTION = "
      INSERT INTO
        `werock_search_suggestions`
      (
        `werock_search_suggestion_urn`,
        `werock_search_suggestion_text`,
        `werock_search_suggestion_count`,
        `werock_search_suggestion_found`,
        `werock_search_suggestion_date_added`
      ) VALUES (
        :urn,
        :text,
        1,
        :found,
        NOW()
      ); ";
    const SQL_UPDATE_SUGGESTION = "
      UPDATE
        `werock_search_suggestions`
      SET
        `werock_search_suggestion_count` = `werock_search_suggestion_count` + 1,
        `werock_search_suggestion_found` = :found
      WHERE
        `werock_search_suggestion_urn` = :urn
      AND
        `werock_search_suggestion_text` = :text; ";

    /** SQL string - LIKE lookup */
    const SQL_GET_SUGGESTIONS = "
      SELECT
        `werock_search_suggestion_text` AS text,
        `werock_search_suggestion_count` AS count,
        `werock_search_suggestion_found` AS found
      FROM
        `werock_search_suggestions`
      WHERE
        `werock_search_suggestion_text` LIKE :text
      AND
        `werock_search_suggestion_urn` = :urn
      AND
        `werock_search_suggestion_found` = 1
      ORDER BY
        `werock_search_suggestion_count` DESC; ";

    /** SQL string - MATCH lookup */
    const SQL_MATCH_SUGGESTIONS = "
      SELECT
        `werock_search_suggestion_text` AS text,
        `werock_search_suggestion_count` AS count,
        `werock_search_suggestion_found` AS found
      FROM
        `werock_search_suggestions`
      WHERE
        MATCH(`werock_search_suggestion_text`) AGAINST (:search IN BOOLEAN MODE)
      AND
        `werock_search_suggestion_urn` = :urn
      AND
        `werock_search_suggestion_found` = 1
      AND
        LENGTH (`werock_search_suggestion_text`) < 40
      ORDER BY
        (100 * MATCH(`werock_search_suggestion_text`) AGAINST (:search IN BOOLEAN MODE)) + LOG(2 + `werock_search_suggestion_count`) DESC
      LIMIT
        :limit;
    ";

    /** SQL string - MATCH lookup */
    const SQL_MATCH_SUGGESTIONS_GLOBAL = "
      SELECT
        `werock_search_suggestion_text` AS text,
        `werock_search_suggestion_count` AS count,
        `werock_search_suggestion_found` AS found
      FROM
        `werock_search_suggestions`
      WHERE
        MATCH(`werock_search_suggestion_text`) AGAINST (:search IN BOOLEAN MODE)
      AND
        `werock_search_suggestion_found` = 1
      AND
        LENGTH (`werock_search_suggestion_text`) < 40
      ORDER BY
        (100 * MATCH(`werock_search_suggestion_text`) AGAINST (:search IN BOOLEAN MODE)) + LOG(2 + `werock_search_suggestion_count`) DESC
      LIMIT
        :limit;
    ";

    /** SQL string - Get popular suggestions */
    const SQL_GET_POPULAR_SUGGESTIONS_BY_URN = "
        SELECT
            `werock_search_suggestion_text` AS text,
            `werock_search_suggestion_count` AS count,
            `werock_search_suggestion_found` AS found
        FROM
            `werock_search_suggestions`
        WHERE
            `werock_search_suggestion_found` = 1
        AND
            `werock_search_suggestion_urn` = :urn
        AND
            LENGTH(werock_search_suggestion_text) < 24
        ORDER BY
            `werock_search_suggestion_count` DESC
        LIMIT
            :limit;
    ";

    /** SQL string - Get popular suggestions */
    const SQL_GET_POPULAR_SUGGESTIONS = "
        SELECT
            `werock_search_suggestion_text` AS text,
            `werock_search_suggestion_count` AS count,
            `werock_search_suggestion_found` AS found
        FROM
            `werock_search_suggestions`
        WHERE
            `werock_search_suggestion_found` = 1
        AND
            LENGTH(werock_search_suggestion_text) < 24
        ORDER BY
            `werock_search_suggestion_count` DESC
        LIMIT
            :limit;
    ";

    /** SQL string - Get recent searches */
    const SQL_GET_RECENT_SEARCHES_BY_URN = "
        SELECT DISTINCT
            `werock_search_text` AS text,
            count(werock_search_text) AS count
        FROM
            `werock_searches`
        WHERE
            `werock_search_urn` = :urn
        AND
            `werock_search_found` = 1
        AND
            `werock_search_date_added` BETWEEN :from AND NOW()
        AND
            LENGTH(werock_search_text) < 24
        GROUP BY
            `werock_search_text`
        ORDER BY
            `count` DESC
        LIMIT
            :limit;
    ";

    /** SQL string - Get recent searches */
    const SQL_GET_RECENT_SEARCHES = "
        SELECT DISTINCT
            `werock_search_text` AS text,
            count(werock_search_text) AS count
        FROM
            `werock_searches`
        WHERE
            `werock_search_found` = 1
        AND
            `werock_search_date_added` BETWEEN :from AND NOW()
        AND
            LENGTH(werock_search_text) < 24
        GROUP BY
            `werock_search_text`
        ORDER BY
            `count` DESC
        LIMIT
            :limit;
    ";

    /** SQL string - MATCH personalized lookup */
    const SQL_MATCH_PERSONALIZED_SUGGESTIONS = "
      SELECT DISTINCT
        werock_search_text AS text,
        COUNT(werock_search_text) AS count,
        werock_search_found AS found,
        MAX(werock_search_date_added) AS latest
      FROM
        werock_searches
      WHERE
        (
          (werock_user_id > 0 AND werock_user_id = :userid)
        OR
          (werock_visitor_id > 0 AND werock_visitor_id = :visitorid)
        )
      AND
        werock_search_urn = :urn
      AND
        werock_search_found = 1
      AND
        werock_search_date_added > :date
      AND
        (LENGTH(:search) < 2 OR MATCH (werock_search_text) AGAINST (:search IN BOOLEAN MODE) > 0)
      GROUP BY
        werock_search_text
      ORDER BY
        latest DESC
      LIMIT
        :limit;
    ";

    /**
     * Get popular suggestions by urn
     *
     * @param null $urn
     * @param int $limit
     * @return array
     */
    public function getPopularSuggestionsByUrn($urn = null, $limit = 12){
        if(empty($urn)) CoreLog::error('Need urn to get suggestions');
        return CoreSqlUtils::rows(self::SQL_GET_POPULAR_SUGGESTIONS_BY_URN, array(
            ':urn' => $urn,
            ':limit' => $limit
        ));
    }

    /**
     * Get popular suggestions
     *
     * @param int $limit
     * @return array
     */
    public function getPopularSuggestions($limit = 12){
        return CoreSqlUtils::rows(self::SQL_GET_POPULAR_SUGGESTIONS, array(
            ':limit' => $limit
        ));
    }

    /**
     * Get recent searches by urn
     *
     * @param null $urn
     * @param int $limit
     * @return array
     */
    public function getRecentSearchesByUrn($urn = null, $limit = 12){
        if(empty($urn)) CoreLog::error('Need urn to get suggestions');
        return CoreSqlUtils::rows(self::SQL_GET_RECENT_SEARCHES_BY_URN, array(
            ':urn' => $urn,
            ':from' => date('Y-m-d H:i:s', strtotime('-30 days')),
            ':limit' => $limit
        ));
    }

    /**
     * Get recent searches
     *
     * @param int $limit
     * @return array
     */
    public function getRecentSearches($limit = 12){
        return CoreSqlUtils::rows(self::SQL_GET_RECENT_SEARCHES, array(
            ':from' => date('Y-m-d H:i:s', strtotime('-30 days')),
            ':limit' => $limit
        ));
    }

    /**
     * Get suggestions
     *
     * @param string $text
     * @param string $urn
     * @param int $limit
     * @return Array
     */
    public function getSuggestions($text = null, $urn = null, $limit = 12){
        if(empty($urn)) CoreLog::error('Need urn to get suggestions');
        return CoreSqlUtils::rows(self::SQL_MATCH_SUGGESTIONS, array(
            ':text' => $text,
            ':search' => $text,
            ':urn' => $urn,
            ':limit' => $limit
        ));
    }

    /**
     * Get suggestions
     *
     * @param string $text
     * @param string $urn
     * @param int $limit
     * @return Array
     */
    public function getFuzzySuggestions($text = null, $urn = null, $limit = 12){
        if(empty($urn)) CoreLog::error('Need urn to get suggestions');
        return CoreSqlUtils::rows(self::SQL_MATCH_SUGGESTIONS, array(
            ':text' => $text,
            ':search' => str_replace(' ', '* ', $text) . '*',
            ':urn' => $urn,
            ':limit' => $limit
        ));
    }

    /**
     * Get global suggestions
     *
     * @param string $text
     * @param int $limit
     * @return Array
     */
    public function getFuzzySuggestionsGlobal($text = null, $limit = 12){
        return CoreSqlUtils::rows(self::SQL_MATCH_SUGGESTIONS_GLOBAL, array(
            ':text' => $text,
            ':search' => str_replace(' ', '* ', $text) . '*',
            ':limit' => $limit
        ));
    }

    /**
     * Get personalized suggestions
     *
     * @param null $text
     * @param null $urn
     * @param int $userid
     * @param int $visitorid
     * @param int $limit
     * @return Array
     */
    public function getPersonalizedSuggestions($text = null, $urn = null, $userid = 0, $visitorid = 0, $limit = 3){
        if(empty($urn)) CoreLog::error('Need urn to get personalized suggestions');
        return CoreSqlUtils::rows(self::SQL_MATCH_PERSONALIZED_SUGGESTIONS, array(
            ':userid' => (int) $userid,
            ':visitorid' => (int) $visitorid,
            ':date' => date('Y-m-d H:i:s', strtotime('-1 day')),
            ':text' => $text,
            ':search' => str_replace(' ', '* ', $text) . '*',
            ':urn' => $urn,
            ':limit' => (int) $limit
        ));
    }

    /**
     * Insert search + handle suggestion
     *
     * @param SearchSuggestionsContextObject $searchSuggestionsContextObject
     */
    public function insertSearch(SearchSuggestionsContextObject $searchSuggestionsContextObject){

        /** @var UserService $UserManager */
        $UserManager = CoreLogic::getService('UserService');

        /** @var int $UserID */
        $UserID = 0;
        $UserObject = $UserManager->getCurrentUser();
        if(!empty($UserObject)){
            $UserID = $UserObject->getId();
        }

        /** Insert the search */
        CoreSqlUtils::insert(self::SQL_INSERT_SEARCH, array(
            ':user' => (int) $UserID,
            ':visitor' => (int) CoreVisitor::getId(),
            ':urn' => $searchSuggestionsContextObject->getUrn(),
            ':text' => strtolower($searchSuggestionsContextObject->getSearchSuggestionsSearchObject()->getSearch()),
            ':found' => ($searchSuggestionsContextObject->getSearchSuggestionsSearchObject()->getFound() ? 1 : 0)
        ));

        /** Handle suggestion */
        self::handleSuggestion($searchSuggestionsContextObject);

    }

    /**
     * Handle a suggestion
     *
     * @param SearchSuggestionsContextObject $searchSuggestionsContextObject
     * @return Array
     */
    private function handleSuggestion(SearchSuggestionsContextObject $searchSuggestionsContextObject){

        /** @var array $suggestion */
        $suggestion = array(
            ':urn' => $searchSuggestionsContextObject->getUrn(),
            ':text' => strtolower($searchSuggestionsContextObject->getSearchSuggestionsSearchObject()->getSearch()),
            ':found' => ($searchSuggestionsContextObject->getSearchSuggestionsSearchObject()->getFound() ? 1 : 0)
        );

        /** SQL operations */
        if(!CoreSqlUtils::update(self::SQL_UPDATE_SUGGESTION, $suggestion)){
            return CoreSqlUtils::insert(self::SQL_INSERT_SUGGESTION, $suggestion);
        }

    }

}