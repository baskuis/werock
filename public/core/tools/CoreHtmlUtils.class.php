<?php

/**
 * Core HTML Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreHtmlUtils {

	/**
	 * Constants
	 */
	const TAG_CLEAR = 'clear';
	const TAG_DIV = 'div';
	const ATTR_STYLE = 'style';
	const CSS_CLEAR = 'clear: both;';
	const TAG_OPEN = '<';
	const TAG_CLOSE = '>';
	const TAG_C_OPEN = '</';
	const TAG_SELF_CLOSE = '/>';
	const NL = "\n";
	const SPACE = ' ';
	const ATTR_START = '="';
	const ATTR_END = '"';
	const ATTR_END_SPACE = '" ';
	const ATTR_QUOTE = '"';

	/**
	 * Html config
	 */
	private static $self_closing_tags = array('area', 'base', 'basefont', 'br', 'col', 'frame', 'hr', 'img', 'input', 'link', 'meta', 'param');
	private static $no_break_tags = array('textarea', 'pre');

	/**
	 * Catches build tag requests
	 * @param string $name Name of method name
	 * @param array $arguments Arguments array
	 * @return String of html tag 
	 */
	public static function __callStatic($name, $arguments = array()){
		switch($name){
			
			//clear floats
			case self::TAG_CLEAR:
				return self::_tag(self::TAG_DIV, null, array(self::ATTR_STYLE => self::CSS_CLEAR));
			break;
			
			//build html tag
			default:
				return self::_tag($name, array_shift($arguments), array_shift($arguments));
			break;
			
		}
	}

	/**
	 * Get the tag and and build
	 * @param string $name Name of html tag
	 * @param string $body Contents of tag
	 * @param array $attributes Tag attributes of tag
	 * @return string Html string
	 */
	public static function _tag($name, $body, $attributes = array()){
        return self::_build($name, $body, $attributes);
	}
	
	/**
	 * Build the tag
	 * @param string $name Name of html tag
	 * @param string $body Contents of tag
	 * @param array $attributes Tag attributes of tag
	 * @return string Html string
	 */
	public static function _build($tag, $body, $attributes){
		if(in_array($tag, self::$self_closing_tags)){
			return self::TAG_OPEN . $tag . self::_attributes($attributes) . self::TAG_SELF_CLOSE . self::NL;
		}
		return self::TAG_OPEN . $tag . self::_attributes($attributes) . self::TAG_CLOSE . (!in_array($tag , self::$no_break_tags) && !empty($body) ? self::NL : null) . $body . (!in_array($tag , self::$no_break_tags) && !empty($body) ? self::NL : null) . self::TAG_C_OPEN . $tag . self::TAG_CLOSE . self::NL;
	}

	/**
	 * Build the attributes string
	 * @param array $attributes Attributes array
	 * @return string Returns attributes string
	 */	
	public static function _attributes($attributes = array()){
		$attributes_string = null;
		if(!empty($attributes)){
			if(is_array($attributes)){
				$attributes_string .= self::SPACE;
				foreach($attributes as $key => $value){
					$attributes_string .= $key . self::ATTR_START . CoreStringUtils::strip($value, self::ATTR_QUOTE) . self::ATTR_END_SPACE;
				}
			}else{
				$attributes_string .= self::SPACE . $attributes;
			}
		}
		return $attributes_string;
	}
		
	/**
	 * Render tag
	 * @param String $tag Html tag name
	 * @param Array $attributes Html tag attributes
	 * @param String $content
	 * @param Boolean $selfClose
	 * @return String Rendered tag
	 */
	public static function renderTag($tag = null, $attributes = array(), $content = null, $selfClose = false){
		$return = self::TAG_OPEN . $tag;
		if(!empty($attributes)){
			foreach($attributes as $key => $value){
				$return .= self::SPACE . $key . self::ATTR_START . $value . self::ATTR_QUOTE;
			}
		}
		if(!$selfClose){
			$return .= self::TAG_CLOSE . $content . self::TAG_C_OPEN . $tag . self::TAG_CLOSE;
		}else{
			$return .= self::TAG_SELF_CLOSE;
		}
		return $return . self::NL;
	}

	/**
	 * Html 2 Text
	 *
	 * @param null $html
	 * @param bool $clean Clean up link references?
	 * @return string
	 */
	public static function html2Text($html = null, $clean = false){
		if(!class_exists('\\Html2Text\\Html2Text')) {
			require(__DIR__ . '/lib/Html2Text.php');
		}
		$Html2Text = new \Html2Text\Html2Text($html);
		$text = $Html2Text->getText();
		if($clean) $text = preg_replace('/\[([^\]]+)\]/', '', $text);
		return $text;
	}

    /**
     * Escape HTML
     *
     * @param null $string
     * @return string
     */
    public static function escape($string = null){
        return strip_tags($string);
    }
	
}