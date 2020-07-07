<?php

/**
 * Core api template
 *
 * PHP version 5
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreApiTemplate {

    /**
     * Page notifications
     */
    public $notifications = array();

    /**
     * Template builder
     */
    function __construct(){

        //set notifications
        self::buildNotifications();

    }

    /**
     * Builds Notifications on rendering object
     */
    private function buildNotifications(){

        //set notifications
        $notificationTypes = CoreNotification::getTypes();
        foreach($notificationTypes as $type){
            $this->notifications[$type]	= CoreNotification::getNotifications($type);
        }

    }

    /**
     * Add Mustache helpers
     * recursive array stepper
     */
    private function addMustacheHelpers($array = array()){

        //define return
        $return = array();

        //step through array
        foreach($array as $key => $value){

            //recursive
            $return[$key] = is_array($value) || is_object($value) ? self::addMustacheHelpers($value) : $value;

            //mustache exists helper
            if(!is_numeric($key)) $return[CoreFeTemplate::FE_HAVE_PREFIX . $key] = !empty($value);

        }

        //return array
        return $return;

    }

    /**
     * Prepare template for Mustache
     */
    private function prepareTemplate(){
        return self::addMustacheHelpers((array) $this);
    }

    /**
     * Render template and output to browser
     */
    public function execute(){

        //template data
        $response = self::prepareTemplate();

        //add in queued data
        if(!empty(CoreApi::$data)){
            foreach(CoreApi::$data as $key => $value){

                //recursive
                $response[$key] = is_array($value) ? self::addMustacheHelpers($value) : $value;

                //mustache exists helper
                if(!is_numeric($key)) $response[CoreApi::API_HAVE_PREFIX . $key] = !empty($value);

            }
        }

        //return api template
        return json_encode($response);

    }

}