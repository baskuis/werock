<?php

/**
 * User Authentication Object
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserAuthenticationObject { 
	
	/**
	 * Properties
	 */
	public $username = null;
	public $email = null;
	public $password = null;
	public $externalIds = array();
	
	/** 
	 * Setters
	 */
	public function setUsername($username = null){
		$this->username = $username;
	}
	public function setEmail($email = null){
		$this->email = $email;
	}
	public function setPassword($password = null){
		$this->password = $password;
	}
	public function setExternalIds($externalIds = array()){
		$this->externalIds = $externalIds;
	}
	
	/**
	 * Getters
	 */
	public function getUsername(){
		return $this->username;
	}
	public function getEmail(){
		return $this->email;
	}
	public function getPassword(){
		return $this->password;
	}
	public function getExternalIds(){
		return $this->externalIds;
	}
	
}