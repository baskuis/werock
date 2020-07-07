<?php

/**
 * Core security api exception
 * thrown when security token is not valid
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreSecurityApiException extends Exception implements ControllerException {

    /**
     * Add required trait to allow interception
     *
     */
    use CoreInterceptorTrait;

    /**
     * Handle this exception
     * allows for interception
     *
     * @return mixed
     */
    public function _handle()
    {
        CoreNotification::set('Access denied. <a href="">Refresh this page?</a>', CoreNotification::ERROR);
        CoreApi::$status = 403;
    }

}