<?php

/**
 * Core Store
 */
class CoreStore { 
	
	/**
	 * Queries
	 */
	const GET_STORE_ENTRY_SQL = " SELECT * FROM `store` WHERE `storeType` = :storeType AND `storeKey` = :storeKey; ";
	const INSERT_STORE_ENTRY_SQL = " INSERT INTO `store` ( `storeType`, `storeKey`, `storeValue`, `storeDateAdded` ) VALUES ( :storeType, :storeKey, :storeValue, NOW() ); ";
	
}