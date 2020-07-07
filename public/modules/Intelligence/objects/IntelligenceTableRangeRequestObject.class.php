<?php

class IntelligenceTableRangeRequestObject {

    public $table;
    public $dataAddedField;
    public $from;
    public $to;
    public $interval;

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return mixed
     */
    public function getDataAddedField()
    {
        return $this->dataAddedField;
    }

    /**
     * @param mixed $dataAddedField
     */
    public function setDataAddedField($dataAddedField)
    {
        $this->dataAddedField = $dataAddedField;
    }

    function __call($method = null, $params){

        if($method == 'mySpecialMethod') {

            switch (sizeof($params)) {
                case 1:
                    self::method1(func_get_args($params));
                    break;
                case 2:
                    self::method2(func_get_args($params));
                    break;
            }

        }

    }

    public function testMethod(){
        self::mySpecialMethod(true);
        self::mySpecialMethod(1, true);
    }



    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return mixed
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param mixed $interval
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;
    }

}