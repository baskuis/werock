<?php

/**
 * User
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserObject implements CoreUserObject {
	
	/**
	 * Properties
	 */
    public $id = null;
    public $username = null;
    public $email = null;
    public $properties = null;
    public $firstname = null;
    public $lastname = null;
	
	/**
	 * Setters
	 */
	public function setId($id = null){
		$this->id = (int)$id;
	}
	public function setUsername($username = null){
		$this->username = (string)$username;
	}
	public function setEmail($email){
		$this->email = $email;
	}
	public function setProperties($properties = array()){
		$this->properties = (array)$properties;
	}
    public function setFirstname($firstname = null){
        $this->firstname = $firstname;
    }
    public function setLastname($lastname = null){
        $this->lastname = $lastname;
    }
	
	/**
	 * Getters
	 */
	public function getId(){
		return $this->id;
	}
	public function getUsername(){
		return $this->username;
	}
	public function getEmail(){
		return $this->email;
	}
	public function getProperties(){
		return $this->properties;
	}
    public function getFirstname(){
        return $this->firstname;
    }
    public function getLastname(){
        return $this->lastname;
    }
	
}