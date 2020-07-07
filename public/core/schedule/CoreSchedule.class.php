<?php

/**
 * Core Schedule
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreSchedule {

    private static $minute;
    private static $hour;
    private static $day;
    private static $month;
    private static $weekday;

    private static $jobs = array();

    /**
     * Schedule a cron job
     *
     * @param CoreScheduleJobObject $coreScheduleJobObject
     */
    public static function add(CoreScheduleJobObject $coreScheduleJobObject){
        self::validate($coreScheduleJobObject);
        array_push(self::$jobs, $coreScheduleJobObject);
    }

    /**
     * Validates cron job definition
     *
     * @param CoreScheduleJobObject $coreScheduleJobObject
     */
    private static function validate(CoreScheduleJobObject $coreScheduleJobObject){
        if(!method_exists($coreScheduleJobObject->getModule(), $coreScheduleJobObject->getMethod())){
            CoreLog::error($coreScheduleJobObject->getModule() . '::' . $coreScheduleJobObject->getMethod() . ' does not exist');
        }
        if(
            (empty($coreScheduleJobObject->minutes) && $coreScheduleJobObject->minutes !== '0') ||
            (empty($coreScheduleJobObject->hours) && $coreScheduleJobObject->hours !== '0') ||
            (empty($coreScheduleJobObject->days) && $coreScheduleJobObject->days !== '0') ||
            (empty($coreScheduleJobObject->months) && $coreScheduleJobObject->months !== '0') ||
            (empty($coreScheduleJobObject->daysOfWeek) && $coreScheduleJobObject->daysOfWeek !== '0')
        ){
            CoreLog::error('Not all interval parameters provided');
        }
    }

    /**
     * Set current values
     */
    private static function now(){
        self::$minute = number_format(date('i'));
        self::$hour = number_format(date('G'));
        self::$day = number_format(date('j'));
        self::$month = number_format(date('n'));
        self::$weekday = number_format(date('w'));
    }

    /**
     * Execute all selected jobs
     */
    public static function execute(){
        self::now();
        if(!empty(self::$jobs)){
            /** @var CoreScheduleJobObject $coreScheduleJobObject */
            foreach(self::$jobs as $coreScheduleJobObject){
                if(self::selectedJob($coreScheduleJobObject)){
                    call_user_func(array($coreScheduleJobObject->getModule(), $coreScheduleJobObject->getMethod()));
                }
            }
        }
    }

    /**
     * Is this a selected job?
     *
     * @param CoreScheduleJobObject $coreScheduleJobObject
     * @return bool
     */
    private static function selectedJob(CoreScheduleJobObject $coreScheduleJobObject){
        if(false === self::evaluateInterval(self::$weekday, $coreScheduleJobObject->getDaysOfWeek())){
            return false;
        }
        if(false === self::evaluateInterval(self::$month, $coreScheduleJobObject->getMonths())){
            return false;
        }
        if(false === self::evaluateInterval(self::$day, $coreScheduleJobObject->getDays())){
            return false;
        }
        if(false === self::evaluateInterval(self::$hour, $coreScheduleJobObject->getHours())){
            return false;
        }
        if(false === self::evaluateInterval(self::$minute, $coreScheduleJobObject->getMinutes())){
            return false;
        }
        return true;
    }

    /**
     * Evaluate an interval
     *
     * @param null $value
     * @param null $interval
     * @return bool
     */
    private static function evaluateInterval($value = null, $interval = null){

        switch (true){

            /**
             * Wildcard
             */
            case ($interval == CoreScheduleJobObject::CRON_EXPRESSION_WILDCARD):
                return true;

            /**
             * Value
             */
            case ($value == number_format($interval)):
                return true;

            /**
             * Comma separated
             */
            case (strpos($interval, CoreScheduleJobObject::CRON_EXPRESSION_SEPARATOR) > -1):
                $values = explode(CoreScheduleJobObject::CRON_EXPRESSION_SEPARATOR, $interval);
                if(in_array($value, $values)){
                    return true;
                }
                break;

            /**
             * Fraction
             */
            case (strpos($interval, CoreScheduleJobObject::CRON_EXPRESSION_WILDCARD) > -1 && strpos($interval, CoreFilesystemUtils::SLASH) > -1):
                $divider = number_format(str_replace(CoreScheduleJobObject::CRON_EXPRESSION_WILDCARD . CoreFilesystemUtils::SLASH, CoreStringUtils::EMPTY_STRING, $interval));
                if($value % $divider == 0){
                    return true;
                }
                break;

        }

        return false;
    }

}