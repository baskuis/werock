<?php

/**
 * Core GIT Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreGitUtils {

    /**
     * Get current branch
     *
     * @return mixed
     */
    public static function getCurrentBranch(){
        try {
            $stringfromfile = file(HOSTS_ROOT . HOST_NAME . '/.git/HEAD', FILE_USE_INCLUDE_PATH);
            $firstLine = $stringfromfile[0];
            $explodedstring = explode("/", $firstLine, 3);
            $branchname = trim($explodedstring[2]);
        } catch(Exception $e){
            CoreLog::error('Unable to get git branch. Info: ' . $e->getMessage());
        }
        return $branchname;
    }

}