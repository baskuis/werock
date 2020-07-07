<?php

/**
 * Core Performance Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CorePerformanceUtils {

    /**
     * @var float $start_time
     */
    static private $start_time;

    /**
     * @var float $previous_time
     */
    static private $previous_time;

    /**
     * @var string $performance_messages
     */
    static private $performance_messages;

    /**
     * Time difference
     *
     * @param null $message
     */
    public static function timeDifference($message = null){

        /** Only if turned on */
        if(!SHOW_PERFORMANCE_DATA) return;

        /** Instantiate current time */
        if(!self::$previous_time) self::$start_time = self::$previous_time = microtime(true);

        /** Show timedifference */
        self::$performance_messages .= $message . ': ' . number_format((microtime(true) - self::$previous_time), 14) . ' seconds' . "\n";

        /** Set current time */
        self::$previous_time = microtime(true);

    }

    /**
     * Show performance messages
     */
    public static function showPerformanceMessages(){
        if(!SHOW_PERFORMANCE_DATA) return;
        echo '
            <pre>
                ' . self::$performance_messages . '
                Total time: ' . number_format(self::$previous_time - self::$start_time, 14) . ' seconds
            </pre>';
    }

    /**
     * Get performance messages
     *
     * @return mixed
     */
    public static function getPerformanceMessages(){
        return self::$performance_messages;
    }

}