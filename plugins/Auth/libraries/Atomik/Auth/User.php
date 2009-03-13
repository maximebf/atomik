<?php

class Atomik_Auth_User
{
	/**
	 * @var string
	 */
	protected $_username;
	
	/**
	 * @var string
	 */
	protected $_password;
	
	/**
	 * @var array
	 */
	protected $_roles = array();
	
	/**
	 * Constructor
	 * 
	 * @param string $username
	 */
	public function __construct($username, $password = null, $roles = array())
	{
		$this->_username = $username;
		$this->_password = $password;
		$this->_roles = $roles;
	}
	
	/**
	 * Returns the username
	 * 
	 * @return string
	 */
	public function getUsername()
	{
		return $this->_username;
	}
	
	/**
	 * Sets the password
	 * 
	 * @param string $password
	 */
	public function setPassword($password)
	{
		$this->_password = $password;
	}
	
	/**
	 * Returns the user's password
	 * 
	 * @return string
	 */
	public function getPassword()
	{
		return $this->_password;
	}
	
	/**
	 * Resets all roles associated to the user
	 * 
	 * @param array $roles An array of string
	 */
	public function setRoles($roles)
	{
		$this->_roles = (array) $roles;
	}
	
	/**
	 * Adds a role to the user
	 * 
	 * @param string $role
	 */
	public function addRole($role)
	{
		$this->_roles[] = (string) $role;
	}
	
	/**
	 * Removes a role from the user
	 * 
	 * @param string $role
	 */
	public function removeRole($role)
	{
		for ($i = 0, $c = count($this->_roles); $i < $c; $i++) {
			if ($this->_roles[$i] == $role) {
				unset($this->_roles[$i]);
				break;
			}
		}
	}
	
	/**
	 * Returns all user's roles
	 * 
	 * @return array
	 */
	public function getRoles()
	{
		return $this->_roles;
	}
	
	/**
	 * Checks if the user has access to the specified resource
	 * 
	 * @param	string	$resource
	 * @return 	boolean
	 */
	public function isAllowed($resource)
	{
		return Atomik_Auth::isAllowed($resource, $this->_roles);
	}
}