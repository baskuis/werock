<?php

/**
 * ElasticSearch Query Object
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class ElasticSearchQueryObject {

    /** @var string $index */
    public $index;

    /** @var string $type */
    public $type;

    /** @var string $query */
    public $query;

    /** @var int $from */
    public $from = 0;

    /** @var int $size */
    public $size = 20;

    /** @var array $fields */
    public $fields = array();

    /** @var array $boosts */
    public $boosts = array();

    /** @var string $factorField */
    public $factorField;

    /** @var string $factorModifier */
    public $factorModifier;

    /** @var float $factorFieldBoost */
    public $factorFieldBoost = 0.1;

    /** @var string $factorBoostMode */
    public $factorBoostMode;

    /**
     * Add field name
     *
     * @param string $fieldName
     * @return $this
     */
    public function addField($fieldName = null){
        array_push($this->fields, $fieldName);
        return $this;
    }

    /**
     * Is a field
     *
     * @param null $fieldName
     * @return bool
     */
    private function isField($fieldName = null){
        foreach($this->fields as $field){
            if($fieldName == $field) return true;
        }
        return false;
    }

    /**
     * Set boost
     *
     * @param null $fieldName
     * @param int $boost
     * @return $this
     */
    public function setBoost($fieldName = null, $boost = 1){
        if(self::isField($fieldName)){
            $this->boosts[$fieldName] = (int) $boost;
        }
        return $this;
    }

    /**
     * Set query
     *
     * @param $query
     * @return $this
     */
    public function setQuery($query){
        $this->query = $query;
        return $this;
    }

    /**
     * Set type
     *
     * @param $type
     * @return $this
     */
    public function setType($type){
        $this->type = $type;
        return $this;
    }

    /**
     * Get index
     *
     * @return string
     */
    public function getIndex(){
        return $this->index;
    }

    /**
     * Set index
     *
     * @param $index
     * @return $this
     */
    public function setIndex($index){
        $this->index = $index;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType(){
        return $this->type;
    }

    /**
     * Set from
     *
     * @param int $from
     * @return $this
     */
    public function setFrom($from){
        $this->from = $from;
        return $this;
    }

    /**
     * Set size
     *
     * @param int $size
     * @return $this
     */
    public function setSize($size){
        $this->size = $size;
        return $this;
    }

    /**
     * Set factor field
     *
     * @param $factorField
     * @return $this
     */
    public function setFactorField($factorField){
        $this->factorField = $factorField;
        return $this;
    }

    /**
     * Set factor modifier
     *
     * @param string $factorModifier
     * @return $this
     */
    public function setFactorModifier($factorModifier){
        $this->factorModifier = $factorModifier;
        return $this;
    }

    /**
     * Set factor boost mode
     *
     * @param $factorBoostMode
     * @return $this
     */
    public function setFactorBoostMode($factorBoostMode){
        $this->factorBoostMode = $factorBoostMode;
        return $this;
    }

    /**
     * Set factor boost field
     *
     * @param $factorFieldBoost
     * @return $this
     */
    public function setFactorFieldBoost($factorFieldBoost){
        $this->factorFieldBoost = $factorFieldBoost;
        return $this;
    }

    /**
     * Get elastic search payload
     *
     * @return array
     */
    public function getPayload(){

        /**
         * Build fields
         */
        $fields = array();
        foreach($this->fields as $field){
            if(isset($this->boosts[$field])){
                $f_entry = array('match' => array($field => array(
                    'query' => $this->query,
                    'boost' => $this->boosts[$field]
                )));
            }else{
                $f_entry = array('match' => array($field => $this->query));
            }
            array_push($fields, $f_entry);
        }

        /**
         * Build lookup query
         */
        $lookupQuery = array('query' => array('bool' => array('should' => $fields)));

        /**
         * Handle custom field factor
         */
        if(isset($this->factorField) && !empty($this->factorField) && isset($this->factorModifier) && !empty($this->factorModifier)) {
            return array(
                'query' => array(
                    'function_score' => array(
                        'query' => array('bool' => array('should' => $fields)),
                        'field_value_factor' => array(
                            'field' => $this->factorField,
                            'modifier' => $this->factorModifier,
                            'factor' => $this->factorFieldBoost
                        ),
                        'boost_mode' => $this->factorBoostMode
                    )
                ),
                'from' => (int) $this->from,
                'size' => (int) $this->size
            );
        }

        return $lookupQuery;
    }

}