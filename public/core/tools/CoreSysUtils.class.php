<?php

class CoreSysUtils {

    const CLI = 'cli';

    private static $isCli;

    /**
     * Is this the command line or web server
     *
     * @return bool
     */
    public static final function isCommandLine(){
        if(self::$isCli !== null) return self::$isCli;
        self::$isCli = (php_sapi_name() === self::CLI);
    }

}