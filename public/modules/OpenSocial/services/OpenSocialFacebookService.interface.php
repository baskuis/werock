<?php

/**
 * OpenSocialFacebookFQLService
 * This interface establishes Facebook FQL interactions
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
interface OpenSocialFacebookServiceInterface {

    /**
     * Is enabled
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Get login url
     *
     * @param string $url
     * @return string
     */
    public function getConnectUrl($url);

    /**
     * Connect callback handler
     * Takes care of creating/authenticating as a user
     * and populating the user profile
     * @param bool $createAccount Should account be create if none exist
     *
     */
    public function connectCallback($createAccount);

}