<?php

/**
 * User Manager
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
interface UserServiceInterface {

    /**
     * Set User Data
     *
     * @param null $key
     * @param null $value
     * @param UserObject $UserObject
     * @return mixed
     */
    public function setData($key = null, $value = null, $UserObject = null);

    /**
     * Get User Data
     *
     * @param null $key
     * @param UserObject $UserObject
     * @return mixed
     */
    public function getData($key = null, $UserObject = null);

    /**
     * Authenticate User
     *
     * @param null $UserAuthenticationObject
     * @return mixed
     */
    public function _authenticate($UserAuthenticationObject = null);

    /**
     * Create User
     *
     * @param null $UserTemplateObject
     * @return mixed
     */
    public function _create($UserTemplateObject = null);

}