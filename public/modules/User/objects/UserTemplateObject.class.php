<?php

/**
 * User Template
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class UserTemplateObject { 

	public $username = null;
	public $email = null;
	public $password = null;
	private $properties = null;

	public $firstName;
	public $lastName;

	/**
	 * @return null
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param null $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @return null
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param null $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @return null
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param null $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * @return null
	 */
	public function getProperties()
	{
		return $this->properties;
	}

	/**
	 * @param null $properties
	 */
	public function setProperties($properties)
	{
		$this->properties = $properties;
	}

	/**
	 * @return mixed
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}

	/**
	 * @param mixed $firstName
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}

	/**
	 * @return mixed
	 */
	public function getLastName()
	{
		return $this->lastName;
	}

	/**
	 * @param mixed $lastName
	 */
	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}

}