<?php

/**
 * Core Render
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreRender {

    /**
     * Allow reflection
     */
    use ClassReflectionTrait;

    /**
     * Allow interceptors
     */
    use CoreInterceptorTrait;

    /**
     * Events
     */
    const EVENT_RENDER_PAGE_END_HEAD = 'event.render.before.end.head';
    const EVENT_RENDER_PAGE_END_BODY = 'event.render.before.end.body';

	/**
	 * Document type
	 */
	public static $doctype = '<!DOCTYPE html>';
	public static $emailDoctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

	/**
	 * Document attributes
	 */
	public static $title = "";
	public static $description = "";
	
	/**
	 * Data
	 */
	public static $data = array();

    /**
     * Body classes - for css scoping
     *
     * @var array
     */
    public static $bodyClasses = array();

	/**
	 * Request scoped head
	 *
	 * @var string
	 */
	public static $requestScopedHead = "";

	/**
	 * Append request scoped head
	 *
	 * @param null $string
	 * @param null $template
	 */
	public static function appendRequestScopedHead($string = null, $template =  null){
		self::$requestScopedHead .= '<!-- ' . $template . '-->' . $string . '<!-- /' . $template . '-->';
	}

    /**
     * Add body class
     *
     * @param null $class
     */
    public static function addBodyClass($class = null){
        array_push(self::$bodyClasses, $class);
    }

	/**
	 * Set data
	 */
	public static function setData($key = null, $value = null){
		
		//check for key
		if(empty($key)){
			CoreLog::error('Cannot set data with null key');
			return false;
		}
		
		//set data
		self::$data[$key] = $value;
		
		//return 
		return true;
	
	}
	 
	/**
	 * Output
	 */
	public static $output;
	
	/**
	 * Render crutches helper
	 *
	 * @param array $crutches Crutches array
	 * @return void
	 */
	public static function renderCrutches($crutches = null){
		if(!empty($crutches)) {
            foreach ($crutches as $crutchName => $theCrutches) {
                foreach ($theCrutches as $crutch) {
                    self::$output .= CoreCrutches::renderCrutch($crutch);
                }
            }
        }
	}
	
	/**
	 * Checks if headers have been sent 
	 */
	private static function assureNoHeadersSent(){
		if(headers_sent()){
			CoreLog::error('Sent headers before document was loaded');
		}
	}

    /**
     * Render email string
     *
     * @param null $templateString
     * @return string
     */
    public static function renderEmail($templateString = null){

		$output =
            self::$emailDoctype . '
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta name="viewport" content="width=device-width">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>' . SITE_NAME . '</title>';

        /**
         * Build Email Styles Block
         * Source: http://htmlemailboilerplate.com/
         */
		$output .= '<style>';
		$output .= <<<EOF

            * {
			  font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
			  font-size: 100%;
			  line-height: 1.6em;
			  margin: 0;
			  padding: 0;
			}
			img {
			  max-width: 600px;
			  width: auto;
			}
			body {
			  -webkit-font-smoothing: antialiased;
			  height: 100%;
			  -webkit-text-size-adjust: none;
			  width: 100% !important;
			}

			/* ELEMENTS */
			a {
			  color: #348eda;
			}
			.btn-primary {
			  Margin-bottom: 10px;
			  width: auto !important;
			}

EOF;

        $output .= '</style>
                    </head>';

        /**
         * Create Email Content Wrapper
         */
        $output .= '
    <body bgcolor="#ffffff">
    	' . $templateString . '
    </body>
</html>';

		return $output;

    }
	
	/**
	 * Render this template
     *
	 * @param String $templateString
	 * @return void
	 */
	public static function renderPage($templateString = null){

		//reset output
		self::$output = CoreStringUtils::EMPTY_STRING;

		//render the fe templates
		//enable again when needed
		CoreFeTemplate::stackFeTemplates();

		//stack language references
		CoreLanguage::stackLanguages();

		//build document output
		self::$output .= self::$doctype . '
<html>
    <head>
    	<title>' . self::$title . '</title>
		<meta name="description" content="' . self::$description . '" />
    	<meta charset="UTF-8">
        <meta id="rhubViewport" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
        <script type="text/javascript">
          var viewportInterval = setInterval(function(){
            if(window.screen.width < 400) {
              var mvp = document.getElementById("rhubViewport");
              mvp.setAttribute("content","width=400, initial-scale=" + (window.screen.width / 400) + ", maximum-scale=" + (window.screen.width / 400) + ", user-scalable=no");
              clearInterval(viewportInterval);
            }
          }, 50);
        </script>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
		<!--
					_             _            _           _             _             _
				   / /\      _   /\ \         /\ \        /\ \         /\ \           /\_\
				  / / /    / /\ /  \ \       /  \ \      /  \ \       /  \ \         / / /  _
				 / / /    / / // /\ \ \     / /\ \ \    / /\ \ \     / /\ \ \       / / /  /\_\
				/ / /_   / / // / /\ \_\   / / /\ \_\  / / /\ \ \   / / /\ \ \     / / /__/ / /
			   / /_//_/\/ / // /_/_ \/_/  / / /_/ / / / / /  \ \_\ / / /  \ \_\   / /\_____/ /
			  / _______/\/ // /____/\    / / /__\/ / / / /   / / // / /    \/_/  / /\_______/
			 / /  \____\  // /\____\/   / / /_____/ / / /   / / // / /          / / /\ \ \
			/_/ /\ \ /\ \// / /______  / / /\ \ \  / / /___/ / // / /________  / / /  \ \ \
			\_\//_/ /_/ // / /_______\/ / /  \ \ \/ / /____\/ // / /_________\/ / /    \ \ \
				\_\/\_\/ \/__________/\/_/    \_\/\/_________/ \/____________/\/_/      \_\_\

		//-->' . "\n";

		//get head crutches
		self::renderCrutches(CoreCrutches::getMarkedCrutches(CoreCrutches::DOCUMENT_HEAD));

		//print less compiled stylesheet
		if(CoreLess::haveLessMatch(CoreLess::ALL_STYLES)){
			self::$output .= CoreHtmlUtils::link(
                null,
                array(
					'href' 	=> CoreLess::getCssFile(CoreLess::ALL_STYLES),
					'rel' 	=> 'stylesheet',
					'type' 	=> 'text/css',
					'media' => 'screen',
					'lazyload' => 'true'
				)
			);
		}

        //print scss compiled stylesheet
        if(CoreSCSS::haveSCSSMatch(CoreSCSS::ALL_STYLES)){
            self::$output .= CoreHtmlUtils::link(
                null,
                array(
                    'href' 	=> CoreSCSS::getCssFile(CoreSCSS::ALL_STYLES),
                    'rel' 	=> 'stylesheet',
                    'type' 	=> 'text/css',
                    'media' => 'screen',
					'lazyload' => 'true'
                )
            );
        }

		//if less ie8
		if(CoreLess::haveLessMatch(CoreLess::IE_8_STYLES)){
			self::$output .= '<!--[if IE 8]>' . "\n";
			self::$output .= CoreHtmlUtils::link(
                null,
                array(
					'href' 	=> CoreLess::getCssFile(CoreLess::IE_8_STYLES),
					'rel' 	=> 'stylesheet',
					'type' 	=> 'text/css',
					'media' => 'screen'
				)
			);
			self::$output .= '<![endif]-->' . "\n";
		}

        //if SCSS ie8
        if(CoreSCSS::haveSCSSMatch(CoreLess::IE_8_STYLES)){
            self::$output .= '<!--[if IE 8]>' . "\n";
            self::$output .= CoreHtmlUtils::link(
                null,
                array(
                    'href' 	=> CoreSCSS::getCssFile(CoreSCSS::IE_8_STYLES),
                    'rel' 	=> 'stylesheet',
                    'type' 	=> 'text/css',
                    'media' => 'screen'
                )
            );
            self::$output .= '<![endif]-->' . "\n";
        }

		//if LESS ie8
		if(CoreLess::haveLessMatch(CoreLess::IE_7_STYLES)){
			self::$output .= '<!--[if IE 7]>' . "\n";		
			self::$output .= CoreHtmlUtils::link(
				null,
                array(
					'href' 	=> CoreLess::getCssFile(CoreLess::IE_7_STYLES),
					'rel' 	=> 'stylesheet',
					'type' 	=> 'text/css',
					'media' => 'screen'
				)
			);
			self::$output .= '<![endif]-->' . "\n";
		}

        //if SCSS ie8
        if(CoreSCSS::haveSCSSMatch(CoreSCSS::IE_7_STYLES)){
            self::$output .= '<!--[if IE 7]>' . "\n";
            self::$output .= CoreHtmlUtils::link(
                null,
                array(
                    'href' 	=> CoreSCSS::getCssFile(CoreSCSS::IE_7_STYLES),
                    'rel' 	=> 'stylesheet',
                    'type' 	=> 'text/css',
                    'media' => 'screen'
                )
            );
            self::$output .= '<![endif]-->' . "\n";
        }

		//if lt ie7 - LESS
		if(CoreLess::haveLessMatch(CoreLess::LT_IE_7_STYLES)){
			self::$output .= '<!--[if lt IE 7]>'	. "\n";	
			self::$output .= CoreHtmlUtils::link(
                null,
                array(
					'href' 	=> CoreLess::getCssFile(CoreLess::LT_IE_7_STYLES),
					'rel' 	=> 'stylesheet',
					'type' 	=> 'text/css',
					'media' => 'screen'
				)
			);						
			self::$output .= '<![endif]-->' . "\n";
		}

        //if lt ie7 - SCSS
        if(CoreSCSS::haveSCSSMatch(CoreSCSS::LT_IE_7_STYLES)){
            self::$output .= '<!--[if lt IE 7]>'	. "\n";
            self::$output .= CoreHtmlUtils::link(
                null,
                array(
                    'href' 	=> CoreSCSS::getCssFile(CoreSCSS::LT_IE_7_STYLES),
                    'rel' 	=> 'stylesheet',
                    'type' 	=> 'text/css',
                    'media' => 'screen'
                )
            );
            self::$output .= '<![endif]-->' . "\n";
        }

		/**
		 * Append request scoped head
		 */
		self::$output .= self::$requestScopedHead;

        /**
         * Dispatch listeners
         *
         */
        CoreObserver::dispatch(self::EVENT_RENDER_PAGE_END_HEAD, null);

        //close head block
		self::$output .= '
    </head>
    <body' . (!empty(self::$bodyClasses) ? ' class="' . implode(' ', self::$bodyClasses) . '"' : '') . '>';

        /**
         * Load the guts of the page
         */
        self::$output .= $templateString;

		/**
		 * Dispatch listeners
		 *
		 */
		CoreObserver::dispatch(self::EVENT_RENDER_PAGE_END_BODY, null);

        /**
         * Inject crutches
         */
        self::renderCrutches(CoreCrutches::getMarkedCrutches(CoreCrutches::DOCUMENT_BODY)) . "\n";

        /**
         * Register mustache js
         */
        CoreFeTemplate::registerMustacheJS();

        /**
         * Compiled scripts
         */
        self::$output .= CoreHtmlUtils::script(
            null,
            array(
                'src' 	=> CoreScript::getScriptFile(),
                'type' 	=> 'text/javascript'
            )
        );

		/**
		 * Load request scoped javascript
		 */
		self::$output .= CoreScript::getRequestScopedScript();

		/**
		 * Show debug block if in DEV_MODE
		 */
		if(DEV_MODE){
			self::$output .= CoreLog::buildDebugBlock(true);
		}

        /**
         * Always show error block
         */
        self::$output .= CoreLog::buildErrorBlock() . '
    </body>
</html>';

		/**
		 * There should not have been any output at this point
		 */
		self::assureNoHeadersSent();

	}
	
	public static function renderElement(){
		
	}
	
	public static function renderModal(){

	}

	/** @var boolean $noBody */
	public static $noBody;

	/**
	 * Get the output
	 * run this through the interceptor
	 */
	public static function _getOutput(){
		if(self::$noBody) return null;
        return self::$output;
	}
	
}