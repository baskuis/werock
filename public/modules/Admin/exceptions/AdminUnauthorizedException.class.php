<?php

/**
 * Unauthorized Exception
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class AdminUnauthorizedException extends Exception implements ControllerException {

    /**
     * Use traits
     */
    use CoreInterceptorTrait;

    /**
     * Handle Exception
     */
    public function _handle(){
        CoreNotification::set('Unauthorized', CoreNotification::ERROR);
    }

}