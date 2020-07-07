<?php

/**
 * Form Module
 * Mostly this module adds form templates
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class FormModule implements CoreModuleInterface {
	
	/**
	 * Module description
	 */
	public static $name = 'Form Module';
	public static $description = 'Adds form templates';
	public static $version = '1.0.0';
	public static $dependencies = array(
        'CrutchKit' => array(
			'min' => '1.0.0',
			'max' => '1.9.9'
		)
    );

	/**
	 * Get listeners
	 *
	 * @return mixed
	 */
	public static function getListeners()
	{

	}

	/**
	 * Get interceptors
	 *
	 * @return mixed
	 */
	public static function getInterceptors()
	{

	}

	/**
	 * Get menus
	 *
	 * @return mixed
	 */
	public static function getMenus()
	{

	}

	/**
	 * Get routes
	 *
	 * @return mixed
	 */
	public static function getRoutes()
	{

        $routes = array();

        array_push($routes, CoreControllerObject::buildMethod('/form/captcha/image', __CLASS__, 'captchaImage', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_GET));

		array_push($routes, CoreControllerObject::buildApi('/api/form/altcaptcha/key', __CLASS__, 'altCaptchaKey', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_POST));

		array_push($routes, CoreControllerObject::buildApi('/api/v1/form/regex/tester', __CLASS__, 'testRegex', CoreControllerObject::MATCH_TYPE_STRING, CoreControllerObject::REQUEST_POST));

        return $routes;

	}

	/**
	 * UserRegisterAction listeners, toMethod
	 */	
	public static function __init__(){

	}

	/**
	 * Capture alt captcha key
	 */
	public static function altCaptchaKey(){
		CoreSessionUtils::assureSession();
		$key = isset($_POST['key']) ? $_POST['key'] : false;
		$name = isset($_POST['name']) ? $_POST['name'] : false;
		$_SESSION['form']['altcaptcha'][$name] = $key;
		$response = new stdClass();
		$response->ok = true;
		CoreApi::setData('response', $response);
	}

	/**
	 * Test regex
	 */
	public static function testRegex(){
		$regex = isset($_POST['regex']) ? $_POST['regex'] : null;
		$content = isset($_POST['content']) ? $_POST['content'] : null;
		$matches = array();
		preg_match($regex, $content, $matches);
		CoreApi::setData('matches', $matches);
	}

	/**
	 * Render captcha image
	 */
	public static function captchaImage(){

		CoreSessionUtils::assureSession();

		$image = imagecreatetruecolor(140, 28);

		$grey = imagecolorallocate($image, 128, 128, 128);
		$black = imagecolorallocate($image, 0, 0, 0);
		$background_color = imagecolorallocate($image, 255, 255, 255);
		imagefilledrectangle($image,0,0,200,50,$background_color);

		/**
		 * Fill with lines
		 */
		$line_color = imagecolorallocate($image, 155, 155, 155);
		for($i=0;$i<10;$i++) {
			imageline($image , 0 , rand() % 50, 200, rand() % 50, $line_color);
		}

		/**
		 * Fill with pixels
		 */
		$pixel_color = imagecolorallocate($image, 0,0,255);
		for($i = 0; $i < 1000; $i++) {
			imagesetpixel($image, rand() % 200, rand() % 50, $pixel_color);
		}

		$letters = 'abcdefghijklmnopqrstuvxyz';
		$len = strlen($letters);

		$word = '';

		/** @var resource $text_color */
		$text_color = imagecolorallocate($image, 0,0,0);

		/** print random text */
		for ($i = 1; $i< 6;$i++) {
			$letter = $letters[rand(0, $len-1)];
			$word .= $letter;
		}

		/**
		 * Load font
		 */
		$font = __DIR__ . '/resources/captcha.ttf';

		// Add some shadow to the text
		imagettftext($image, 20, 0, 21, 23, $grey, $font, $word);

		// Add the text
		imagettftext($image, 20, 0, 20, 22, $black, $font, $word);

		/** set captcha value */
		$_SESSION['captcha'] = $word;

		/** set content header */
		CoreHeaders::add('Content-type', 'image/png');

		/** output image */
		imagepng($image);

		/** cleanup */
		imagedestroy($image);

	}

	/**
	 * Run on update
	 *
	 * @param $previousVersion
	 * @param $newVersion
	 *
	 * @return void
	 */
	public static function __update__($previousVersion, $newVersion)
	{

	}

	/**
	 * Run on enable
	 *
	 * @return void
	 */
	public static function __enable__()
	{

	}

	/**
	 * Run on disable
	 *
	 * @return mixed
	 */
	public static function __disable__()
	{

	}

	/**
	 * Run on install
	 *
	 */
	public static function __install__()
	{

	}
	
}