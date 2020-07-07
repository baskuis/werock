<?php

interface IntelligenceServiceInterface {
	
	/**
	 * Get intelligence values
	 * @param string $key Intelligence key
	 * @param string $user_id Intelligence key
	 * @param string $visitor_id Intelligence key
	 * @param string $from_date Intelligence key
	 * @param string $to_date Intelligence key
	 * @param string $limit Intelligence key
	 * @param string $order_by Intelligence key
	 * @return array Intelligence values
	 */
	public static function getIntelligenceValues($key = null, $user_id = null, $visitor_id = null, $from_date = '-1 month', $to_date = 'now', $limit = 20, $order_by = null);

}