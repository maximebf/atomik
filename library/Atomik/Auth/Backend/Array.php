<?php

class Atomik_Auth_Backend_Array implements Atomik_Auth_Backend_Interface
{
	protected $_users = array();
	
	public function __construct($users = array())
	{
		$this->setUsers($users);
	}
	
	public function setUsers($users)
	{
		$this->_users = array();
		foreach ($users as $credentials => $roles) {
			list($username, $password) = explode(':', $credentials);
			$this->addUser($username, $password, $roles);
		}
	}
	
	public function addUser($username, $password, $roles = array())
	{
		$this->_users[$username] = new Atomik_Auth_User($username, $password, $roles);
	}
	
	public function removeUser($username)
	{
		if (isset($this->_users[$username])) {
			unset($this->_users[$username]);
		}
	}
	
	public function getUsernames()
	{
		return array_keys($this->_users);
	}
	
	public function authentify($username, $password)
	{
		if (isset($this->_users[$username]) && $this->_users[$username]->getPassword() == $password) {
			return $this->getUser($username);
		}
		
		return false;
	}
	
	public function getUser($username)
	{
		if (isset($this->_users[$username])) {
			return $this->_users[$username];
		}
	}
}