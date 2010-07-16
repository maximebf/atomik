<?php
/**
 * Atomik Framework
 * Copyright (c) 2008-2009 Maxime Bouroumeau-Fuseau
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package Atomik
 * @subpackage Auth
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Auth_User */
require_once 'Atomik/Auth/User.php';

/**
 * Store users in an array
 * 
 * @package Atomik
 * @subpackage Auth
 */
class Atomik_Auth_Backend_Array implements Atomik_Auth_Backend_Interface
{
	/** @var array */
	public $_users = array();
	
	/**
	 * @param array $users
	 */
	public function __construct($users = array())
	{
		$this->users = $users;
	}
	
	/**
	 * Sets users
	 * 
	 * Array values can be instances of Atomik_Auth_User or specify the username 
	 * as the key and the password as the value. Instead of the password as the value you
	 * can also use an array with the password and roles keys.
	 * 
	 * @param array $users
	 */
	public function setUsers($users)
	{
		$this->users = array();
		foreach ($users as $key => $value) {
			if ($value instanceof Atomik_Auth_User_Array) {
				$this->addUser($value);
				continue;
			}
			
			$password = $value;
			$roles = array();
			if (is_array($value)) {
				$password = isset($value['password']) ? $value['password'] : '';
				$roles = isset($value['roles']) ? $value['roles'] : array();
			}
			$this->addUser($key, $password, $roles);
		}
	}
	
	/**
	 * @param string|Atomik_Auth_User $username
	 * @param string $password
	 * @param array $roles
	 */
	public function addUser($username, $password = '', $roles = array())
	{
		if ($username instanceof Atomik_Auth_User) {
			$this->_users[$username->username] = $username;
			return;
		}
		
		$this->_users[$username] = new Atomik_Auth_User($username, $password, $roles);
	}
	
	/**
	 * @param string $username
	 * @return Atomik_Auth_User
	 */
	public function getUser($username)
	{
	    if (!isset($this->_users[$username])) {
	        return null;
	    }
	    return $this->_users[$username];
	}
	
	/**
	 * @return array
	 */
	public function getUsers()
	{
		return $this->_users;
	}
	
	/**
	 * @return Atomik_Auth_User_Locator_Array
	 */
	public function getLocator()
	{
	    /** Atomik_Auth_Locator_Array */
	    require_once 'Atomik/Auth/Locator/Array.php';
	    return new Atomik_Auth_Locator_Array($this);
	}
	
	/**
	 * Checks whether a user exists with the specified credentials
	 * 
	 * @param string $username
	 * @param string $password
	 * @return bool
	 */
	public function authentify($username, $password)
	{
		if(isset($this->users[$username]) && $this->users[$username] == $password) {
			return $username;
		}
		return false;
	}
}