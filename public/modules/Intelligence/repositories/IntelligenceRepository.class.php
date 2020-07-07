<?php

/**
 * Intelligence repository
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class IntelligenceRepository {
	
	/**
	 * SQL statements
	 */
	const SELECT_INTELLIGENCE_KEY_QUERY = " SELECT `werock_intelligence_data_id` FROM `werock_intelligence_data` WHERE `werock_intelligence_data_text` = :werock_intelligence_data_text ";
	const INSERT_INTELLIGENCE_KEY_QUERY = " INSERT INTO `werock_intelligence_data` ( `werock_intelligence_data_text` ) VALUES ( :werock_intelligence_data_text )";
	const SELECT_INTELLIGENCE_VALUE_QUERY = " SELECT `werock_intelligence_data_value_id` FROM `werock_intelligence_data_values` WHERE `werock_intelligence_data_value_text` = :werock_intelligence_data_value_text AND `werock_intelligence_data_id` = :werock_intelligence_data_id";
	const UPDATE_INTELLIGENCE_VALUE_QUERY = " UPDATE `werock_intelligence_data_values` SET `werock_intelligence_data_value_count` = `werock_intelligence_data_value_count` + 1 WHERE `werock_intelligence_data_value_id` = :werock_intelligence_data_value_id ";
	const INSERT_INTELLIGENCE_VALUE_QUERY = " INSERT INTO `werock_intelligence_data_values` ( `werock_intelligence_data_value_text`, `werock_intelligence_data_id` ) VALUES ( :werock_intelligence_data_value_text, :werock_intelligence_data_id ) ";
	const INSERT_INTELLIGENCE_QUERY = " INSERT INTO `werock_intelligence` ( `werock_user_id`, `werock_visitor_id`, `werock_intelligence_data_id`, `werock_intelligence_data_value_id`, `werock_intelligence_isbot`, `werock_intelligence_date_added` ) VALUES ( :werock_user_id, :werock_visitor_id, :werock_intelligence_data_id, :werock_intelligence_data_value_id, :werock_intelligence_isbot, NOW() )";

	/**
	 * Get data key
	 */
	const SQL_SELECT_INTELLIGENCE_ROW_QUERY = " SELECT * FROM werock_intelligence_data WHERE werock_intelligence_data_id = :dataId; ";

	/**
	 * Date added range
	 */
	const SELECT_RECORDS_ADDED_RANGE = " SELECT COUNT(*) AS count FROM {{table}} WHERE {{date_added}} BETWEEN :from AND :to; ";

    /**
     * Graphing SQL
     */
    const SELECT_INTELLGIENCE_CHART_RANGE_QUERY = "
	    SELECT SQL_CALC_FOUND_ROWS
            werock_intelligence_data_value_text AS text,
            COUNT(werock_intelligence_data_value_text) AS count
        FROM
            werock_intelligence_data_values
        LEFT JOIN
            werock_intelligence_data
        ON
            werock_intelligence_data.werock_intelligence_data_id = werock_intelligence_data_values.werock_intelligence_data_id
        LEFT JOIN
            werock_intelligence
        ON
            (
                werock_intelligence_data.werock_intelligence_data_id = werock_intelligence.werock_intelligence_data_id
            AND
                werock_intelligence_data_values.werock_intelligence_data_value_id = werock_intelligence.werock_intelligence_data_value_id
            )
        WHERE
            werock_intelligence_data.werock_intelligence_data_text = :key
        AND
            werock_intelligence.werock_intelligence_isbot = :crawler
        AND
            werock_intelligence.werock_intelligence_date_added BETWEEN :start AND :end
        GROUP BY
            werock_intelligence_data_value_text
        ORDER BY
            count DESC
        LIMIT
            :limit
	";

	/**
	 * Get intelligence data
	 *
	 * @param null $dataId
	 * @return array
	 */
	public function getIntelligenceData($dataId = null){
		return CoreSqlUtils::row(self::SQL_SELECT_INTELLIGENCE_ROW_QUERY, array(
			':dataId' => (int) $dataId
		));
	}

	/**
	 * Get records
	 *
	 * @param IntelligenceTableRangeRequestObject $IntelligenceTableRangeRequestObject
	 * @return array
	 */
	public function getRecordsAdded(IntelligenceTableRangeRequestObject $IntelligenceTableRangeRequestObject){

		$return = array();

		$current = $IntelligenceTableRangeRequestObject->getFrom();
		while($current <= $IntelligenceTableRangeRequestObject->getTo()){

			/** @var string $sql prepare the query for PDO */
			$sql = str_replace(array('{{table}}', '{{date_added}}'), array($IntelligenceTableRangeRequestObject->getTable(), $IntelligenceTableRangeRequestObject->getDataAddedField()), self::SELECT_RECORDS_ADDED_RANGE);

			/** @var array $data response */
			$data = CoreSqlUtils::row($sql, array(
				':from' => date('Y-m-d H:i:s', $current),
				':to' => date('Y-m-d H:i:s', $current + $IntelligenceTableRangeRequestObject->getInterval())
			));

			/** @var IntelligenceTableRangeResponseObject $IntelligenceTableRangeResponseObject */
			$IntelligenceTableRangeResponseObject = CoreLogic::getObject('IntelligenceTableRangeResponseObject');
			$IntelligenceTableRangeResponseObject->getIntelligenceTableRangeRequestObject($IntelligenceTableRangeRequestObject);
			$IntelligenceTableRangeResponseObject->setCount($data['count']);
			$IntelligenceTableRangeResponseObject->setStart($current);
			$IntelligenceTableRangeResponseObject->setNiceStart(date('m-d-Y H:i', $current));
			$IntelligenceTableRangeResponseObject->setEnd($current + $IntelligenceTableRangeRequestObject->getInterval());
			$IntelligenceTableRangeResponseObject->setNiceEnd(date('m-d-Y H:i', $current + $IntelligenceTableRangeRequestObject->getInterval()));

			/** Add to return */
			array_push($return, $IntelligenceTableRangeResponseObject);

			/** Increment pointer */
			$current += $IntelligenceTableRangeRequestObject->getInterval();

		}

		return $return;

	}

    /**
     * Get intelligence data
     *
     * @param IntelligenceDataRequestObject $intelligenceDataRequestObject
     * @return mixed
     */
    public function getData(IntelligenceDataRequestObject $intelligenceDataRequestObject){

        $return = array();

        $current = $intelligenceDataRequestObject->getFrom();
        while($current < $intelligenceDataRequestObject->getTo()){

            /** @var array $data */
            $data = CoreSqlUtils::rows(self::SELECT_INTELLGIENCE_CHART_RANGE_QUERY, array(
                ':key' => $intelligenceDataRequestObject->getKey(),
                ':start' => date('Y-m-d H:i:s', $current),
                ':end' => date('Y-m-d H:i:s', $current + $intelligenceDataRequestObject->getInterval()),
                ':limit' => $intelligenceDataRequestObject->getLimit(),
                ':crawler' => (int) $intelligenceDataRequestObject->isCrawler()
            ));

            /** @var IntelligenceDataResponseObject $IntelligenceDataResponseObject */
            $IntelligenceDataResponseObject = CoreLogic::getObject('IntelligenceDataResponseObject');
            $IntelligenceDataResponseObject->setIntelligenceDataRequestObject($intelligenceDataRequestObject);
            $IntelligenceDataResponseObject->setLabel($intelligenceDataRequestObject->getKey());
            $IntelligenceDataResponseObject->setValues($data);
            $IntelligenceDataResponseObject->setStart($current);
            $IntelligenceDataResponseObject->setNiceStart(date('m-d-Y H:i', $current));
            $IntelligenceDataResponseObject->setEnd($current + $intelligenceDataRequestObject->getInterval());
            $IntelligenceDataResponseObject->setNiceEnd(date('m-d-Y H:i', $current + $intelligenceDataRequestObject->getInterval()));

            /** Add to return */
            array_push($return, $IntelligenceDataResponseObject);

            /** Increment pointer */
            $current += $intelligenceDataRequestObject->getInterval();
        }

        return $return;

    }

	/**
	 * Inserts intelligence data value
     *
	 * @param IntelligenceEntryObject $IntelligenceEntryObject
	 * @return bool Return true when saved and false otherwise
	 */
	public function introduceIntelligenceData(IntelligenceEntryObject $IntelligenceEntryObject){

		//get intelligence row
		$row = CoreSqlUtils::row(self::SELECT_INTELLIGENCE_KEY_QUERY, array(
            ':werock_intelligence_data_text' => $IntelligenceEntryObject->getData()
        ));
		
		//return id .. or create one
		if(!empty($row)){ 
			return $row['werock_intelligence_data_id']; 
		}else{
			if(false !== ($id = CoreSqlUtils::insert(self::INSERT_INTELLIGENCE_KEY_QUERY, array(
                ':werock_intelligence_data_text' => $IntelligenceEntryObject->getData())
            ))){
				return (int)$id;
			}
		}
		
		//something went wrong
		CoreLog::error('Could not select or insert intelligence data.');
		return false;
		
	}
	
	/**
	 * Inserts intelligence meta value
     *
	 * @param string $value Meta value
	 * @param int $data_id Data id
	 * @return bool Return true when saved and false otherwise
	 */
	public function introduceIntelligenceDataValue($value = null, $data_id = null){
		
		//get meta row
		$row = CoreSqlUtils::row(self::SELECT_INTELLIGENCE_VALUE_QUERY, array(
            ':werock_intelligence_data_value_text' => $value,
            ':werock_intelligence_data_id' => (int) $data_id
        ));

		//handle meta row
		if(!empty($row)){
			if(false !== CoreSqlUtils::update(self::UPDATE_INTELLIGENCE_VALUE_QUERY, array(
                ':werock_intelligence_data_value_id' => (int) $row['werock_intelligence_data_value_id']
            ))){
				return $row['werock_intelligence_data_value_id'];
			}
		}else{
			return CoreSqlUtils::insert(self::INSERT_INTELLIGENCE_VALUE_QUERY, array(
                ':werock_intelligence_data_value_text' => CoreStringUtils::limitString($value, 252, '...'),
                ':werock_intelligence_data_id' => $data_id
            ));
		}
		
		//something went wrong
		CoreLog::error('Could not update or insert intelligence meta. value[' . $value . '] data_id[' . (int)$data_id . ']');
		
		//something went wrong
		return false;
	
	}

	/**
	 * Inserts intelligence stack
     *
	 * @param Array $stack_to_save
	 * @return bool Return true when saved and false otherwise
	 */
	public function insertIntelligenceStack($stack_to_save = array()){

        /**
         * Get Intelligence manager
         */
        $IntelligenceManager = CoreLogic::getService('IntelligenceService');

		if(!empty($stack_to_save)){
					
			/** @var IntelligenceEntryObject $IntelligenceEntryObject */
			foreach($stack_to_save as &$IntelligenceEntryObject){
				if(false !== ($data_id = self::introduceIntelligenceData($IntelligenceEntryObject))){

                    /** Scrub out crawlers */
                    if((int) $IntelligenceEntryObject->getVisitor() == 0) continue;

                    CoreSqlUtils::insert(self::INSERT_INTELLIGENCE_QUERY, array(
                        ':werock_user_id' => (int) $IntelligenceEntryObject->getUser() ,
                        ':werock_visitor_id' => (int) $IntelligenceEntryObject->getVisitor(),
                        ':werock_intelligence_data_id' => (int) $data_id,
                        ':werock_intelligence_data_value_id' => self::introduceIntelligenceDataValue($IntelligenceEntryObject->getValue(), (int)$data_id),
                        ':werock_intelligence_isbot' => ($IntelligenceEntryObject->getIsBot() ? 1 : 0)
                    ));

				}
			}

			//clear intelligence stack (preventing accidental double entries)
            $IntelligenceManager->IntelligenceStack = array();
			
			return true;
			
		}
		
		return false;
	
	}
		
}