<?php

/**
 * Intelligence Widget Object
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class IntelligenceWidgetObject {

    public $label;

    public $keys;

    public $key;
    public $start;
    public $end;
    public $interval;
    public $limit;
    public $canEdit = false;

    public $isMapTable = false;
    public $isLineChart = false;
    public $isPieChart = false;
    public $isCountryChart = false;
    public $isRegionChart = false;
    public $isCityChart = false;

    public $uniqueID;

    public $template;

    public $height = '220px';
    public $width = '100%';

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param boolean $canEdit
     */
    public function setCanEdit($canEdit)
    {
        $this->canEdit = $canEdit;
    }

    /**
     * @return boolean
     */
    public function getCanEdit()
    {
        return $this->canEdit;
    }

    /**
     * @return boolean
     */
    public function isIsMapTable()
    {
        return $this->isMapTable;
    }

    /**
     * @param boolean $isMapTable
     */
    public function setIsMapTable($isMapTable)
    {
        $this->isMapTable = $isMapTable;
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $interval
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;
    }

    /**
     * @return mixed
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return mixed
     */
    public function getKeys()
    {
        return $this->keys;
    }

    /**
     * @param mixed $keys
     */
    public function setKeys($keys)
    {
        $this->keys = $keys;
    }

    /**
     * @param mixed $uniqueID
     */
    public function setUniqueID($uniqueID)
    {
        $this->uniqueID = $uniqueID;
    }

    /**
     * @return mixed
     */
    public function getUniqueID()
    {
        return $this->uniqueID;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return boolean
     */
    public function isIsLineChart()
    {
        return $this->isLineChart;
    }

    /**
     * @param boolean $isLineChart
     */
    public function setIsLineChart($isLineChart)
    {
        $this->isLineChart = $isLineChart;
    }

    /**
     * @return boolean
     */
    public function isIsPieChart()
    {
        return $this->isPieChart;
    }

    /**
     * @param boolean $isPieChart
     */
    public function setIsPieChart($isPieChart)
    {
        $this->isPieChart = $isPieChart;
    }

    /**
     * @return boolean
     */
    public function isIsCountryChart()
    {
        return $this->isCountryChart;
    }

    /**
     * @param boolean $isCountryChart
     */
    public function setIsCountryChart($isCountryChart)
    {
        $this->isCountryChart = $isCountryChart;
    }

    /**
     * @return boolean
     */
    public function isIsRegionChart()
    {
        return $this->isRegionChart;
    }

    /**
     * @param boolean $isRegionChart
     */
    public function setIsRegionChart($isRegionChart)
    {
        $this->isRegionChart = $isRegionChart;
    }

    /**
     * @return boolean
     */
    public function isIsCityChart()
    {
        return $this->isCityChart;
    }

    /**
     * @param boolean $isCityChart
     */
    public function setIsCityChart($isCityChart)
    {
        $this->isCityChart = $isCityChart;
    }

    /**
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param string $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Render widget
     *
     * @return mixed
     */
    public function render(){
        return CoreTemplate::render($this->template, $this);
    }

}