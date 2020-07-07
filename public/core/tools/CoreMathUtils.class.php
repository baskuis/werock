<?php

/**
 * Core Math Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreMathUtils {

    /**
     * Calculate chance of correlation against
     * the first standard deviation and mean passed
     * the highest value is 1 for perfect correlation with the first set of data
     * or -1 for perfect correlation with the second set of data
     *
     * @param double $value
     * @param double $this_deviation
     * @param double $this_mean
     * @param double $common_deviation
     * @param double $common_mean
     * @return double
     */
    public static function chanceOfCorrelation($value, $this_deviation, $this_mean, $common_deviation, $common_mean){
        return (double) (2 / pow(2 * pi() * pow($this_deviation, 2), 0.5) * pow(M_E, (-pow($value-$this_mean, 2) / (2 * pow($this_deviation, 2)))) / ((1 / pow(2 * pi() * pow($common_deviation, 2), 0.5) * pow(M_E, (-pow($value-$common_mean, 2) / (2 * pow($common_deviation, 2))))) + (1 / pow(2 * pi() * pow($this_deviation, 2), 0.5) * pow(M_E, (-pow($value-$this_mean, 2) / (2 * pow($this_deviation, 2)))))))-1;
    }

    /**
     * Apply value to data set
     *
     * @param double $value
     * @param double $mean
     * @param double $count
     * @param double $deviation
     * @return double
     */
    public static function applyValueGetNewDeviation($value, $mean, $count, $deviation){
        return (double) sqrt((($count * ( pow($value, 2) + (($count - 1) * (pow($deviation, 2) + pow($mean, 2))))) - pow($value + (($count - 1) * $mean), 2)) / pow($count, 2));
    }

    /**
     * Apply value get new deviation
     *
     * @param double $value
     * @param double $mean
     * @param double $count
     * @return double
     */
    public static function applyValueGetNewMean($value, $mean, $count){
        return (double) (($mean * $count) + $value) / ($count + 1);
    }

}