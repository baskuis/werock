<?php

/**
 * Allows for more helpful error handling
 *
 * Class CoreErrors
 */
class CoreErrors {

    /**
     * Constants
     */
    const PHP_OPENING_TAG_HELPER = '/\<span.*?\>.*?\&lt;\?.*?\<\/span\>/i';

    /**
     * Notices
     * Warnings
     *
     * @var array
     */
    public static $notices = array();
    public static $warnings = array();
    public static $unknown = array();

    /**
     * Init
     */
    public static function init(){

        /**
         * Don't show regular errors
         */
        ini_set('display_errors', 0);

        /**
         * Set error handler
         */
        if(WE_ERROR_HANDLING_ENABLED) set_error_handler(array('CoreErrors', 'weErrorHandler'));

        /**
         * Set register shutdown
         */
        if(WE_FATAL_HANDLING_ENABLED) register_shutdown_function(array('CoreErrors', 'weFatalHandler'));

    }

    /**
     * Fatal error handler
     *
     *
     */
    public static function weFatalHandler(){

        /**
         * Get error details
         */
        $error = error_get_last();

        /**
         * Set response
         */
        if($error !== NULL) {

            /** Server side error */
            if(!DEV_MODE) http_response_code(500);

            /**
             * Handle special error
             */
            $error = self::specialError($error);

            /**
             * Render if error has been found
             */
            $errno   = $error["type"];
            $errfile = $error["file"];
            $errline = $error["line"];
            $errstr  = $error["message"];

            /**
             * Render error
             */
            echo self::render('Fatal Error', $errno, 'Fatal Error', $errstr, $errfile, $errline);

            /**
             * Stop here
             */
            exit(0);

        }

    }

    /**
     * Error
     *
     * @param $type
     * @param $code
     * @param $title
     * @param $message
     * @param $path
     * @param $line
     */
    public static function error($type, $code, $title, $message, $path, $line){

        /** Server side error */
        if(!DEV_MODE) http_response_code(500);

        //show error
        echo self::render($type, $code, $title, $message, $path, $line);

        //stop
        exit(0);

    }

    /**
     * Special error
     *
     * @param $error
     */
    private static function specialError($error){
        switch(true){

            /**
             * Warning when memcache has already been loaded
             *
             */
            case ($error['message'] == "Module 'memcache' already loaded"):

                /**
                 * Give more helpful message
                 */
                $error['message'] = "Module 'memcache' already loaded. Please remove the reference in php.ini";

            break;

            default:
                return $error;
            break;

        }
    }

    /**
     * Render and return error output
     *
     * @param $type
     * @param $code
     * @param $title
     * @param $message
     * @param $path
     * @param $line
     *
     * @return String
     */
    private static function render($type, $code, $title, $message, $path, $line){

        /**
         * Get array of lines
         */
        $filecontents = is_file($path) ? file_get_contents($path) : false;

        /**
         * If we can find a file to preview
         */
        if(false !== $filecontents){

            /**
             * Break up file
             */
            $filelines = explode("\n", $filecontents);

            $contextarray = $filelines;

            /**
             * Extract meaningful information
             */
            $showline = 'Unable to find line with issue.';
            $kstart = 1;
            $keys = '';
            foreach($contextarray as $key => &$theline){
                if($line == (int)($kstart + $key - 0)){
                    $keys .= '<strong style="color: red;" id="issue_line">' . ($kstart + $key) . ' -&gt;</strong><br />';
                    $showline = $theline;
                    $theline .= ' //<- ' . $title . ' ' . $message;
                }else{
                    $keys .= ($kstart + $key) . '<br />';
                }
            }

            /**
             * File Context
             */
            $filecontext = implode("\n", $contextarray);

            /**
             * Highlighted string
             */
            $highlightedString = @highlight_string($filecontext, true);

        }

        /**
         * Render output
         */
        return '<!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8" />
                    <title>' . $title . '</title>
                    <script src="//code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
                    <style>
                        * {
                            font-family: Arial;
                        }
                        body {
                            padding: 5px;
                            margin: 0;
                            background-color: black;
                        }
                        .wrapper {
                            background-color: white;
                        }

                        .details-block {

                        }
                        .details-block .header {
                            background: #ccc;
                            text-style: italic;
                            padding: 5px;
                        }
                        .details-block .title {
                            color: white;
                            background-color: red;
                            font-size: 18px;
                            padding: 5px;
                        }
                        .details-block .message {
                            color: white;
                            background-color: #444;
                            font-size: 18px;
                            padding: 5px;
                        }
                        .details-block .details {
                            font-size: 14px;
                        }
                        .details-block table.details {
                            width: 100%;
                        }
                        .details-block table.details td {
                            padding: 5px;
                            border-bottom: 1px solid #ccc;
                            background-color: #eee;
                        }

                        .context-viewer {
                            padding: 0px;
                            margin: 0px;
                            border: 3px solid #ccc;
                            font-family: Georgia;
                            background-color: #ccc;
                            overflow: auto;
                            overflow-x: hidden;
                            -ms-overflow-x: hidden;
                            height: 400px;
                            line-height: 18px;
                        }
                        .context-viewer table {
                            background-color: white;
                            width: 100%;
                            table-layout: fixed;
                        }
                        .context-viewer table td {
                            padding: 5px;
                            background-color: #fffeee;
                        }
                        .context-viewer table td.linenumbers {
                            padding-right: 15px;
                            text-align: right;
                            border-right: 1px solid #ccc;
                            width: 50px;
                        }
                        .context-viewer table td.preview {
                            padding: 0px;
                        }
                        .context-viewer table td.preview .overflow {
                            padding: 5px;
                            overflow: auto;
                            overflow-y: hidden;
                            -ms-overflow-y: hidden;
                            white-space: nowrap;
                        }

                    </style>
                </head>
                <body>
                    <div class="wrapper">
                        <div class="details-block">
                            <div class="header">Oops, WeRock compilation ran into an issue unfortunately. Here is what we know:</div>
                            <div class="title">Type: <strong>' . $title . '</strong></div>
                            <div class="message">Issue: ' . $message . '</div>
                            <table cellspacing="0" cellpadding="0" class="details">
                                <tbody>
                                    <tr>
                                        <td style="text-align: right;"><strong>Server:</strong></td>
                                        <td>' . $_SERVER['SERVER_NAME'] . '</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;"><strong>Request:</strong></td>
                                        <td>' . $_SERVER['REQUEST_URI'] . '</td>
                                    </tr>
                                    ' . (!empty($_POST) ? '
                                    <tr>
                                        <td style="text-align: right;"><strong>Post:</strong></td>
                                        <td><pre>' . print_r($_POST, true) . '</pre></td>
                                    </tr>
                                    ' : '') . '
                                    <tr>
                                        <td style="text-align: right;"><strong>File:</strong></td>
                                        <td>' . $path . ' (' . $line . ')</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;"><strong>Line:</strong></td>
                                        <td>' . $showline . '</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;"><strong>PHP Version:</strong></td>
                                        <td>' . PHP_VERSION . '</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right;"><strong>OS:</strong></td>
                                        <td>' . PHP_OS . '</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div style="background-color: black; padding: 5px 5px 0  5px;">
                           <input type="text" style="width: 80%; background-color: black; border: none; color: white; font-size: 18px; padding: 5px 0; margin-bottom: 5px;" onclick="javascript: $(this).focus();" value="' . $path . '" />
                        </div>
                        <script type="text/javascript">
                            $().ready(function(){
                                $(".context-viewer").scrollTop(' . ( $line - SHOW_ERROR_CONTEXT_LINE_PADDING ) * 18 . ');
                            });
                        </script>
                        <div class="context-viewer">
                            <table cellspacing="0" cellpadding="0" id="preview_table">
                                <tbody>
                                    <tr>
                                        <td valign="top" class="linenumbers">
                                            ' . (isset($keys) ? $keys : null) . '
                                        </td>
                                        <td valign="top" class="preview">
                                            <div class="overflow">
                                                ' . ((false === $filecontents || !$highlightedString) ? 'No preview available' : $highlightedString) . '
                                            </div>
                                        </td>
                                </tbody>
                            </table>
                        </div>
                        <div class="extra">
                            <p>
                                <strong>Warnings:</strong><br />
                                ' . implode('<br />', self::$warnings) . '
                            </p>
                            <p>
                                <strong>Notices:</strong><br />
                                ' . implode('<br />', self::$notices) . '
                            </p>
                            <p>
                                <strong>Unknown:</strong><br />
                                ' . implode('<br />', self::$unknown) . '
                            </p>
                        </div>
                    </div>
                </body>
            </html>';

    }

    /**
     * @param $errorNumber
     * @param $errorString
     * @param $errorFile
     * @param $errorLine
     * @return bool
     */
    public static function weErrorHandler($errorNumber, $errorString, $errorFile, $errorLine){

        if (!(error_reporting() & $errorNumber)) {
            // This error code is not included in error_reporting
            return;
        }

        switch ($errorNumber) {
            case E_USER_ERROR:

                /**
                 * Output error details block
                 */
                echo self::render('Fatal Error', $errorNumber, 'Fatal Error', $errorString, $errorFile, $errorLine);

                /**
                 * Stop here
                 */
                exit(0);

            break;

            case E_USER_WARNING:
                if(DEV_MODE) {

                    /**
                     * Output error details block
                     */
                    echo self::render('Warning', $errorNumber, 'Fatal Error', $errorString, $errorFile, $errorLine);

                    /**
                     * Stop here
                     */
                    exit(0);

                }
                array_push(self::$warnings, $errorNumber . ' ' . $errorString);
            break;

            case E_USER_NOTICE:
                if(DEV_MODE) {

                    /**
                     * Output error details block
                     */
                    echo self::render('Notice', $errorNumber, 'Fatal Error', $errorString, $errorFile, $errorLine);

                    /**
                     * Stop here
                     */
                    exit(0);

                }
                array_push(self::$notices, $errorNumber . ' ' . $errorString);
            break;

            default:
                if(DEV_MODE) {
                    echo self::render('Unknown Error', $errorNumber, 'Fatal Error', $errorString, $errorFile, $errorLine);
                    exit(0);
                }
                array_push(self::$unknown, $errorNumber . ' ' . $errorString);
            break;

        }

        /* Don't execute PHP internal error handler */
        return true;

    }

}