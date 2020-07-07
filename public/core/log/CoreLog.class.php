<?php

/**
 * This class could be responsible for registering 
 * errors throughout the application, simply stacking
 * them for reference
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreLog {

    /**
     * Log location
     */
    const LOG_LOCATION = HOSTS_ROOT . HOST_NAME . '/logs/' . DOMAIN_NAME . '.log';

    /**
     * Events
     */
    const WRITE_LOG_MESSAGE_AFTER_EVENT = 'log:write:after';

    /**
     * Current log level
     *
     * @var string
     */
    public static $logLevel = (DEV_MODE) ? self::LEVEL_DEBUG : self::LEVEL_INFO;

    /**
     * Log levels
     */
    const LEVEL_TRACE = 'TRACE';
    const LEVEL_DEBUG = 'DEBUG';
    const LEVEL_INFO = 'INFO';
    const LEVEL_WARN = 'WARN';
    const LEVEL_ERROR = 'ERROR';
    const LEVEL_FATAL = 'FATAL';

    /**
     * Used strings
     */
    const LOG_DATE_FORMAT = 'Y-m-d H:i:s';
    const REMOTE_ADDR = 'REMOTE_ADDR';
    const REQUEST_URI = 'REQUEST_URI';
    const EMPTY_STRING = '';
    const OPEN_SQUARE_BRACE = ' [';
    const CLOSE_SQUARE_BRACE = '] ';
    const NEWLINE = "\n";
    const SINGLE_SPACE = ' ';
    const PROP_FILE = 'file';
    const PROD_LINE = 'line';
    const CLI = 'CLI';
    const REMOTE = 'REMOTE';

    const MODE_READ = 'r';

    const MAX_PATH_LENGTH = 125;

    /**
     * Show stacktrace for
     */
    const DUMP_STACKTRACE_FOR = [
        self::LEVEL_ERROR,
        self::LEVEL_FATAL
    ];

    /**
     * Maximum depth of logged stack traces
     *
     * @var int
     */
    public static $maxStackDepth = 8;

	/**
	 * Error stack
	 */
	private static $errors = array();
    private static $warn = array();
    private static $info = array();
	private static $debug = array();
    private static $trace = array();
	private static $fatal = "";
	
	/**
	 * Listener keys
	 * for the error() method
	 */
	const ERROR_EVENT_DEBUG_BEFORE = "CORE:DEBUG:BEFORE";
	const ERROR_EVENT_DEBUG_AFTER = "CORE:DEBUG:AFTER";

    /**
     * Listener keys
     * for the warn() method
     */
    const ERROR_EVENT_WARN_BEFORE = "CORE:WARN:BEFORE";
    const ERROR_EVENT_WARN_AFTER = "CORE:WARN:AFTER";

	/**
	 * Listener keys
	 * for the error() method
	 */
	const ERROR_EVENT_ERROR_BEFORE = "CORE:ERROR:BEFORE";
	const ERROR_EVENT_ERROR_AFTER = "CORE:ERROR:AFTER";
	
	/**
	 * Listener keys
	 * for the fatal() method
	 */
	const ERROR_EVENT_FATAL_BEFORE = "CORE:FATAL:BEFORE";
	const ERROR_EVENT_FATAL_AFTER = "CORE:FATAL:AFTER";
	
	/**
	 * This method handles and debug reported in the application
	 *
	 * @param String $message
	 * @return null
	 */
	public static function debug($message = null){
        if(self::$logLevel !== self::LEVEL_DEBUG || self::$logLevel !== self::LEVEL_TRACE){
            return;
        }
		array_push(self::$debug, $message);
        self::writeMessage($message, self::LEVEL_DEBUG);
	}

    /**
     * This method handles info log statements
     *
     * @param String $message
     * @return null
     */
    public static function info($message = null){
        array_push(self::$info, $message);
        self::writeMessage($message, self::LEVEL_INFO);
    }

    /**
     * This method handles info log statements
     *
     * @param String $message
     * @return null
     */
    public static function trace($message = null){
        if(self::$logLevel !== self::LEVEL_TRACE){
            return;
        }
        array_push(self::$trace, $message);
        self::writeMessage($message, self::LEVEL_TRACE);
    }

    /**
     * Write line to log
     *
     * @param string $level
     * @param null $message
     */
    private static function writeMessage($message = null, $level = self::LEVEL_INFO){
        $CoreLogObject = new CoreLogObject();
        $CoreLogObject->setType((CoreSysUtils::isCommandLine()) ? self::CLI : self::REMOTE);
        $CoreLogObject->setLevel($level);
        $CoreLogObject->setDate(date(self::LOG_DATE_FORMAT));
        $CoreLogObject->setIp(isset($_SERVER[self::REMOTE_ADDR]) ? $_SERVER[self::REMOTE_ADDR] : null);
        $CoreLogObject->setPath(isset($_SERVER[self::REQUEST_URI]) ? CoreStringUtils::limitString($_SERVER[self::REQUEST_URI], self::MAX_PATH_LENGTH) : null);
        $CoreLogObject->setMessage($message);
        file_put_contents(
            self::LOG_LOCATION,
            self::OPEN_SQUARE_BRACE . $CoreLogObject->getLevel() . self::CLOSE_SQUARE_BRACE . DOMAIN_NAME . self::SINGLE_SPACE .
            ((CoreSysUtils::isCommandLine()) ? self::CLI : self::REMOTE . self::OPEN_SQUARE_BRACE . $_SERVER[self::REMOTE_ADDR] . self::CLOSE_SQUARE_BRACE . self::SINGLE_SPACE . $CoreLogObject->getPath()) .
            self::SINGLE_SPACE . $CoreLogObject->getDate() . self::SINGLE_SPACE . $CoreLogObject->getMessage() . self::NEWLINE,
            FILE_APPEND);
        if(in_array($level, self::DUMP_STACKTRACE_FOR)){
            $simple_stack = self::EMPTY_STRING;
            $backtrace = debug_backtrace();
            foreach($backtrace as $key => $trace){
                if($key > self::$maxStackDepth) break;
                $simple_stack .= $trace[self::PROP_FILE] . self::OPEN_SQUARE_BRACE . $trace[self::PROD_LINE] . self::CLOSE_SQUARE_BRACE . self::NEWLINE;
            }
            file_put_contents(self::LOG_LOCATION, $simple_stack, FILE_APPEND);
        }
        CoreObserver::dispatch(self::WRITE_LOG_MESSAGE_AFTER_EVENT, $CoreLogObject);
    }

    /**
     * Read end of log file
     *
     * @param int $number_of_lines Number of lines to read
     * @return string
     */
    public static function readLog($number_of_lines = 1000){
        $lines = array();
        $fp = fopen(self::LOG_LOCATION, self::MODE_READ);
        if($fp) {
            while (!feof($fp)) {
                $line = fgets($fp, 4096);
                array_push($lines, $line);
                if (count($lines) > $number_of_lines)
                    array_shift($lines);
            }
            fclose($fp);
        }
        return join(self::NEWLINE, $lines);
    }

    /**
     * This method handles any warn reported in the application
     *
     * @param String $warn
     * @return null
     */
    public static function warn($warn = null){
        CoreObserver::dispatch(self::ERROR_EVENT_WARN_BEFORE, null);
        array_push(self::$warn, $warn);
        self::writeMessage($warn, self::LEVEL_WARN);
        CoreObserver::dispatch(self::ERROR_EVENT_WARN_AFTER, null);
    }

	/**
	 * This method handles any error reported in the application
	 *
	 * @param String $error
	 * @return null
	 */
	public static function error($error = null){
		CoreObserver::dispatch(self::ERROR_EVENT_ERROR_BEFORE, null);
		array_push(self::$errors, $error);
        self::writeMessage($error, self::LEVEL_ERROR);
		CoreObserver::dispatch(self::ERROR_EVENT_ERROR_AFTER, null);
        if(DEV_MODE) self::fatal($error);
	}
	
	/**
	 * Invoke a fatal error
	 * the application can not continue to run
	 *
	 * @param String $fatal 
	 * @return null	 
	 */
	public static function fatal($fatal = null){
		CoreObserver::dispatch(self::ERROR_EVENT_FATAL_BEFORE, null);
		self::$fatal = $fatal;
        self::writeMessage($fatal, self::LEVEL_FATAL);
		CoreObserver::dispatch(self::ERROR_EVENT_FATAL_AFTER, null);

        /**
         * Pull information from stacktrace
         */
        $trace = debug_backtrace();

        /**
         * Ignore assets
         */
        $ignoreAssets = array('Utils.class.php', 'Log.class.php');

        /**
         * Find what index to start looking
         */
        $index = 0;
        $looking = true;
        while($looking){

            //see if we have a match
            foreach($ignoreAssets as $ignoreAsset){
                if(substr($trace[$index][self::PROP_FILE], -strlen($ignoreAsset)) == $ignoreAsset && isset($trace[$index + 1])){
                    $index++;
                    continue; //keep looking
                }
            }

            //we're done looking
            $looking = false;

        }

        /**
         * Set error details
         */
        $type = $trace[$index]['type'];
        $code = 'Fatal Error';
        $title = 'Unrecoverable error';
        $message = isset($trace[$index]['args'][0]) ? $trace[$index]['args'][0] . ' Info: ' . $fatal : $trace[$index]['class'] . ' Info: ' . $fatal;
        $path = isset($trace[$index][self::PROP_FILE]) ? $trace[$index][self::PROP_FILE] : '';
        $line = isset($trace[$index][self::PROD_LINE]) ? $trace[$index][self::PROD_LINE] : '';

        /**
         * Trigger error
         */
        CoreErrors::error($type, $code, $title, $message, $path, $line);
		
	}

    /**
     * Show Exception
     *
     * @param Exception $e
     */
    public static function exception(Exception $e){

        /**
         * Trigger error
         */
        CoreErrors::error(get_class($e), $e->getCode(), 'An Exeption Occurred', $e->getMessage(), $e->getFile(), $e->getLine());

    }

    /**
     * Prints the fatal error block
     *
     * @print html
     */
    private static function printFatalErrorBlock(){

        /**
         * Build fatal error block
         * use large font and strong colors
         */
        $string = '<div style="margin: 20px; padding: 20px; border: 4px dotted red; font-size: 16px; color: #666;">';
        $string .= '<h1 style="color: red;">' . self::$fatal . '</h1>';
        $string .= '</div>';
        $string .= self::buildErrorBlock();
        $string .= self::buildDebugBlock();

        /**
         * Print to screen
         */
        echo $string;

    }

    /**
     * Builds error block output
     *
     * @return String error block output
     */
    public static function buildErrorBlock(){

        //cut short if not needed
        if(!self::$errors){
            return null;
        }

        //Build the debug string
        $string = '<div style="margin: 20px; padding: 20px; border: 4px dotted red; font-size: 16px; color: #666;">';
        $string .= '<h2>Error Messages</h2>';
        $string .= '<ul>';
        foreach(self::$errors as $error){
            $string .= '<li>';
            $string .= '<strong style="color:red;">Error:</strong> ';
            $string .= $error;
            $string .= '</li>';
        }
        $string .= '</ul>';
        $string .= '</div>';

        //return the debug string
        return $string;

    }

    /**
     * Builds debug block output
     *
     * @param bool $stealth
     * @return String debug block output
     */
    public static function buildDebugBlock($stealth = false){

        //cut short if not needed
        if(!self::$debug){
            return null;
        }

        //allow stealth
        if($stealth){
            $string = '<!-- DEBUG MESSAGES' . "\n";
            foreach(self::$debug as $item){
                $string .= "\t\t" . $item . "\n";
            }
            $string .= ' END DEBUG MESSAGES -->' . "\n";
            return $string;
        }

        //Build the debug string
        $string = '<div style="margin: 20px; padding: 20px; border: 4px dotted #ccc; font-size: 16px; color: #666;">';
        $string .= '<h2>Debug Messages</h2>';
        $string .= '<ul>';
        foreach(self::$debug as $item){
            $string .= '<li>';
            $string .= $item;
            $string .= '</li>';
        }
        $string .= '</ul>';
        $string .= '</div>';

        //return the debug string
        return $string;

    }
	
}