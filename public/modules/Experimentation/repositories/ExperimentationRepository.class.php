<?php

/**
 * DB interactions
 *
 * Class ExperimentationRepository
 */
class ExperimentationRepository {

    const SQL_SELECT_EXPERIMENTS = "
        SELECT
          *
        FROM
          werock_experiments
    ";

    const SQL_SELECT_EXPERIMENT_VARIANTS = "
        SELECT
          *
        FROM
          werock_experiment_variants
        WHERE
          werock_experiment_id = :experimentId
    ";

    const SQL_SELECT_EXPERIMENT_ENTRY = "
        SELECT
          *
        FROM
          werock_experiment_variant_entries
        WHERE
          werock_visitor_id = :visitorId
        AND
          werock_experiment_variant_id = :variantId
    ";

    const SQL_INSERT_EXPERIMENT_ENTRY = "
        INSERT INTO
          werock_experiment_variant_entries
        (
          werock_visitor_id,
          werock_experiment_variant_id,
          werock_experiment_variant_entry_date_added,
          werock_experiment_variant_entry_value,
          werock_experiment_variant_entry_count
        ) VALUES (
          :visitorId,
          :variantId,
          NOW(),
          '',
          0
        )
    ";

    const SQL_UPDATE_EXPERIMENT_ENTRY = "
        UPDATE
          werock_experiment_variant_entries
        SET
          werock_experiment_variant_entry_value = :value,
          werock_experiment_variant_entry_count = werock_experiment_variant_entry_count + 1
        WHERE
          werock_visitor_id = :visitorId
        AND
          werock_experiment_variant_id = :variantId
    ";

    /**
     * Get variant entries
     */
    const SQL_GET_VARIANT_ENTRIES = "
        SELECT
          COUNT(*) + SUM(werock_experiment_variant_entry_count) AS exposures,
          SUM(werock_experiment_variant_entry_count) AS conversions
        FROM
          werock_experiment_variant_entries
        WHERE
          werock_experiment_variant_id = :variantId
    ";

    public function getEntrySummary($variantId = null){
        return CoreSqlUtils::row(self::SQL_GET_VARIANT_ENTRIES, array(
           ':variantId' => (int) $variantId
        ));
    }

    /**
     * Get an entry
     *
     * @param int $visitorId
     * @param int $variantId
     * @return array
     */
    public function getEntry($visitorId = null, $variantId = null){
        if(!($visitorId > 0)) return false;
        return CoreSqlUtils::row(self::SQL_SELECT_EXPERIMENT_ENTRY, array(
            ':visitorId' => (int) $visitorId,
            ':variantId' => (int) $variantId
        ));
    }

    /**
     * Insert entry
     *
     * @param int $visitorId
     * @param int $variantId
     * @return int
     */
    public function insertEntry($visitorId = null, $variantId = null){
        if(!($visitorId > 0)) return false;
        return CoreSqlUtils::insert(self::SQL_INSERT_EXPERIMENT_ENTRY, array(
            ':visitorId' => (int) $visitorId,
            ':variantId' => (int) $variantId
        ));
    }

    /**
     * Update an entry
     *
     * @param null $visitorId
     * @param null $variantId
     * @param null $value
     * @return True
     */
    public function updateEntry($visitorId = null, $variantId = null, $value = null){
        if(!($visitorId > 0)) return false;
        return CoreSqlUtils::update(self::SQL_UPDATE_EXPERIMENT_ENTRY, array(
            ':visitorId' => (int) $visitorId,
            ':variantId' => (int) $variantId,
            ':value' => $value
        ));
    }

    /**
     * Get experiments
     *
     * @return array
     */
    public function getExperiments(){
        return CoreSqlUtils::rows(self::SQL_SELECT_EXPERIMENTS, array());
    }

    /**
     * Get experiment variants
     *
     * @param int $experimentId
     * @return array
     */
    public function getExperimentVariants($experimentId = null){
        return CoreSqlUtils::rows(self::SQL_SELECT_EXPERIMENT_VARIANTS, array(
            ':experimentId' => (int) $experimentId
        ));
    }

}