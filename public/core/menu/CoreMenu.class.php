<?php

/**
 * Core menu
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreMenu {

    /**
     * Cache
     */
    const CACHE_MENUS_NS = 'menus';
    const CACHE_MENUS_KEY = 'menus';
    const CACHE_MENUS_DURATION = 86400;

    /**
     * Allow interceptors
     */
    use CoreInterceptorTrait;

    /**
     * Constants
     */
    const MENU_SORT_METHOD_NAME = 'cmpMenuItem';

    /**
     * @var Array CoreMenuObject $menus
     */
    private static $menus = array();

    /**
     * Add to menu system
     *
     * @param null $id
     * @param CoreMenuObject $CoreMenuObject
     */
    public static function add($id = null, CoreMenuObject $CoreMenuObject){
        if(!isset(self::$menus[$id])) self::$menus[$id] = array();
        array_push(self::$menus[$id], $CoreMenuObject);
    }

    /**
     * Hide a menu item by menuId in system id
     *
     * @param null $id
     * @param null $menuId
     */
    public static function _hide($id = null, $menuId = null){
        if(isset(self::$menus[$id])){
            /** @var CoreMenuObject $CoreMenuObject */
            foreach(self::$menus[$id] as &$CoreMenuObject){
                if($CoreMenuObject->getId() == $menuId){
                    $CoreMenuObject->setHide(true);
                    return;
                }
            }
        }
        CoreLog::error('Unable to hide ' . $menuId . ' in menu system ' . $id);
    }

    /**
     * Get menu to id
     *
     * @param null $id
     * @return bool or array
     */
    public static function _getMenu($id = null){
        if(isset(self::$menus[$id])){
            return self::$menus[$id];
        }
        CoreLog::debug('Unable to find menu by id: ' . $id);
        return false;
    }

    /**
     * Get menu system
     *
     * @param null $menuId Id reference of menu system
     * @return Array Menu system representation
     */
    public static function getMenuSystem($menuId = null){

        /**
         * Define menu system holder
         */
       return self::walkMenu($menuId, null);

    }

    /**
     * Custom sort based on zindex
     *
     * @param mixed $a
     * @param mixed $b
     * @return int
     */
    private static function cmpMenuItem($a = null, $b = null){

        //equal
        if ($a->getZindex() == $b->getZindex()) {
            return 0;
        }

        //or different
        return ($a->getZindex() > $b->getZindex()) ? +1 : -1;

    }

    /**
     * Walks menu system and returns representative array of menu system
     * TODO: Add markers for current page - and or - active page
     *
     * @param null $menuId
     * @param null $parentMenuObjectId
     * @return array
     */
    private static function walkMenu($menuId = null, $parentMenuObjectId = null){

        /**
         * Define return object
         */
        $return = array();

        /**
         * Assure menu exists
         */
        if(isset(self::$menus[$menuId]) && !empty(self::$menus[$menuId])){

            /**
             * @var CoreMenuObject $CoreMenuObject
             */
            foreach(self::$menus[$menuId] as &$CoreMenuObject){

                //skip hidden items
                if($CoreMenuObject->getHide() === true) continue;

                //this parent id
                $thisParentId = $CoreMenuObject->getParentId();

                //get this id
                $thisId = $CoreMenuObject->getId();

                //set active
                //under certain conditions currentRoute isn't set
                //for instance if the action is not executes by
                //the core controller
                if(!empty(CoreController::$currentRoute)) {
                    $CoreMenuObject->setActive((CoreController::$currentRoute->getMatchType() == CoreControllerObject::MATCH_TYPE_REGEX) ? preg_match(CoreController::$currentRoute->getMatch(), $CoreMenuObject->getHref()) : ((CoreController::$currentRoute->getMatch() == $CoreMenuObject->getHref()) ? 1 : 0) > 0);
                }

                //check this id
                if(empty($thisId)){
                    CoreLog::error('CoreMenuObject needs id!');
                    continue;
                }

                //if we are in the root level
                if(!$parentMenuObjectId && !$thisParentId){

                    //lookup children
                    $CoreMenuObject->setChildren(self::walkMenu($menuId, $CoreMenuObject->getId()));

                    /**
                     * Swap out placeholders
                     */
                    $CoreMenuObject->setName(CoreLanguage::get($CoreMenuObject->getName()));
                    $CoreMenuObject->setTitle(CoreLanguage::get($CoreMenuObject->getTitle()));

                    //add root level menu item
                    $return[$CoreMenuObject->getId()] = $CoreMenuObject;

                }

                //if we have a match for a parent id
                if($parentMenuObjectId == $thisParentId){

                    //lookup children
                    $CoreMenuObject->setChildren(self::walkMenu($menuId, $CoreMenuObject->getId()));

                    /**
                     * Swap out placeholders
                     */
                    $CoreMenuObject->setName(CoreLanguage::get($CoreMenuObject->getName()));
                    $CoreMenuObject->setTitle(CoreLanguage::get($CoreMenuObject->getTitle()));

                    //add item for this parent
                    $return[$CoreMenuObject->getId()] = $CoreMenuObject;

                }

            }

        }else{
            CoreLog::error('Unable to walk menu by id ' . $menuId);
        }

        /**
         * Sort based on z-index
         */
        usort($return, array(__CLASS__, self::MENU_SORT_METHOD_NAME));

        /**
         * Return menu system
         */
        return $return;

    }

    /**
     * @param mixed $menus
     */
    public static function setMenus($menus)
    {
        self::$menus = $menus;
    }

    /**
     * @return mixed
     */
    public static function getMenus()
    {
        return self::$menus;
    }

    /**
     * Register menus
     *
     */
    public static function registerMenus(){
        self::$menus = CoreCache::getCache(static::CACHE_MENUS_KEY, true, array(self::CACHE_MENUS_NS), false);
        if(!empty(self::$menus)) return;
        self::$menus = array();
        /** @var CoreModuleObject $coreModuleObject */
        foreach(CoreModule::$modules as $coreModuleObject){
            $menus = $coreModuleObject->getMenus();
            if(!empty($menus)){
                /** @var CoreMenuObject $coreMenuObject */
                foreach($menus as $coreMenuObject) {
                    self::add($coreMenuObject->getTarget(), $coreMenuObject);
                }
            }
        }
        CoreCache::saveCache(static::CACHE_MENUS_KEY, self::$menus, self::CACHE_MENUS_DURATION, true, array(self::CACHE_MENUS_NS), false);
    }

}