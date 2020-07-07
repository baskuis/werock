<?php

class PageNotFoundPlugin {
	
	/**
	 * Init plugin, register subscribers and interceptors
	 */
	public function __init__(){
		
		//intercept page not found
		CoreInterceptor::subscribe("CoreController", "handleRequest", __CLASS__, 'pageNotFoundInterceptor');
		
	}
	
	/**
	 * Destroy
	 */
	public function destroy(){
		
	}
	
	/**
	 * Page not found interceptor
	 * @param Object $object Intercepted object
	 * @param Mixed $param Original params
	 * @return 
	 */
	public static function pageNotFoundInterceptor($return = null, $params = array()){
		if($return == null){
			echo '<h1 style="text-align: center; font-size: 200px; padding: 100px 0 0 0; color: #ddd; -webkit-filter: blur(10px); transition: -webkit-filter 3s;">404</h1>';
		}
		return $return;
	}

}