<?php

/**
 * @table users
 */
class User extends Atomik_Auth_User
{
	/**
	 * @form-required
	 * @var string
	 * @length 100
	 */
	public $firstName;
	
	/**
	 * @form-required
	 * @var string
	 * @length 100
	 */
	public $lastName;
}
