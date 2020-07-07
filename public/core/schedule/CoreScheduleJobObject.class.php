<?php

/**
 * Scheduler object
 * Class CoreScheduleJobObject
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreScheduleJobObject {

    const SCHEDULE_EVERY_MINUTE = "* * * * *";
    const SCHEDULE_EVERY_FIVE_MINUTES = "*/5 * * * *";
    const SCHEDULE_EVERY_FIVE_MINUTES_DURING_DAYTIME = "*/5 8,9,10,11,12,13,14,15,16,17,18 * * *";

    const CRON_EXPRESSION_SEPARATOR = ',';
    const CRON_EXPRESSION_WILDCARD = '*';
    const CRON_COMPONENTS_SEPARATOR = ' ';
    const CRON_COMPONENTS_COUNT = 5;

    /** @var string $minutes */
    public $minutes = '';
    /** @var string $hours */
    public $hours = '';
    /** @var string $days */
    public $days = '';
    /** @var string $months */
    public $months = '';
    /** @var string $daysOfWeek */
    public $daysOfWeek = '';

    /** @var string $Module */
    public $Module = null;
    /** @var string $Method */
    public $Method = null;

    /**
     * Set yearly
     */
    public function yearly(){
        $this->minutes = 0;
        $this->hours = 0;
        $this->days = 1;
        $this->months = 1;
        $this->daysOfWeek = '*';
    }

    /**
     * Set monthly
     */
    public function monthly(){
        $this->minutes = 0;
        $this->hours = 0;
        $this->days = 1;
        $this->months = '*';
        $this->daysOfWeek = '*';
    }

    /**
     * Set weekly
     */
    public function weekly(){
        $this->minutes = 0;
        $this->hours = 0;
        $this->days = '*';
        $this->months = '*';
        $this->daysOfWeek = 0;
    }

    /**
     * Set Daily
     */
    public function daily(){
        $this->minutes = 0;
        $this->hours = 0;
        $this->days = '*';
        $this->months = '*';
        $this->daysOfWeek = '*';
    }

    /**
     * Set Hourly
     */
    public function hourly(){
        $this->minutes = 0;
        $this->hours = '*';
        $this->days = '*';
        $this->months = '*';
        $this->daysOfWeek = '*';
    }

    /**
     * Set minutely
     */
    public function minutely(){
        $this->minutes = '*';
        $this->hours = '*';
        $this->days = '*';
        $this->months = '*';
        $this->daysOfWeek = '*';
    }

    /**
     * Set cron expression
     *
     * @param string $expression
     */
    public function cron($expression = null){
        $pieces = explode(self::CRON_COMPONENTS_SEPARATOR, $expression);
        if(sizeof($pieces) != self::CRON_COMPONENTS_COUNT){
            CoreLog::error('Invalid cron expression passed: ' . $expression);
        }
        $this->minutes = $pieces[0];
        $this->hours = $pieces[1];
        $this->days = $pieces[2];
        $this->months = $pieces[3];
        $this->daysOfWeek = $pieces[4];
    }

    /**
     * @return string
     */
    public function getMinutes()
    {
        return $this->minutes;
    }

    /**
     * @param string $minutes
     */
    public function setMinutes($minutes)
    {
        $this->minutes = $minutes;
    }

    /**
     * @return string
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @param string $hours
     */
    public function setHours($hours)
    {
        $this->hours = $hours;
    }

    /**
     * @return string
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param string $days
     */
    public function setDays($days)
    {
        $this->days = $days;
    }

    /**
     * @return string
     */
    public function getMonths()
    {
        return $this->months;
    }

    /**
     * @param string $months
     */
    public function setMonths($months)
    {
        $this->months = $months;
    }

    /**
     * @return string
     */
    public function getDaysOfWeek()
    {
        return $this->daysOfWeek;
    }

    /**
     * @param string $daysOfWeek
     */
    public function setDaysOfWeek($daysOfWeek)
    {
        $this->daysOfWeek = $daysOfWeek;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->Module;
    }

    /**
     * @param string $Module
     */
    public function setModule($Module)
    {
        $this->Module = $Module;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->Method;
    }

    /**
     * @param string $Method
     */
    public function setMethod($Method)
    {
        $this->Method = $Method;
    }

}