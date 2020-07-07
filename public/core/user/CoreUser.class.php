<?php

class CoreUser { 

	/** @var string $id */
	public static $id;

	/** @var CoreUserObject $user */
	public static $user;

	/** @var bool $anonymous */
	public static $anonymous = true;

	/**
	 * @return mixed
	 */
	public static function getId()
	{
		return self::$id;
	}

	/**
	 * @param mixed $id
	 */
	public static function setId($id)
	{
		self::$id = $id;
	}

	/**
	 * @return CoreUserObject
	 */
	public static function getUser()
	{
		return self::$user;
	}

	/**
	 * @param mixed $user
	 */
	public static function setUser(CoreUserObject $user)
	{
		self::$anonymous = false;
		self::$user = $user;
		if(method_exists($user, 'getId')) {
			self::$id = $user->getId();
		}
	}

	/**
	 * @return mixed
	 */
	public static function getAnonymous()
	{
		return self::$anonymous;
	}

	/**
	 * @param mixed $anonymous
	 */
	public static function setAnonymous($anonymous)
	{
		self::$anonymous = $anonymous;
	}

}