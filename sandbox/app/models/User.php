<?php

/**
 * @table users
 * @admin-ignore
 */
class User extends Atomik_Auth_User
{
	public $firstName;
	
	public $lastName;
}
