<?php

/**
 * Core ControllerException
 * controller exception with handle method
 * may allow interceptors when interceptorTrait is also used
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
interface ControllerException {

    /**
     * Handle this exception
     * allows for interception
     *
     * @return mixed
     */
    public function _handle();

}