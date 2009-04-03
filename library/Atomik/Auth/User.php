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

/**
 * The default user object. Store users in an array.
 * 
 * @package Atomik
 * @subpackage Auth
 */
class Atomik_Auth_User implements Atomik_Auth_User_Interface 
{
	/**
	 * @var array
	 */
	private static $_users = array();
	
	/**
	 * @var string
	 */
	public $username;
	
	/**
	 * @var string
	 */
	public $password;
	
	/**
	 * @var username
	 */
	public $roles = array();
	
	/**
	 * Sets users
	 * 
	 * Array values can be instances of Atomik_Auth_User or specify the username 
	 * as the key and the password as the value. Instead of the password as the value you
	 * can also use an array with the password and roles keys.
	 * 
	 * @param	array	$users
	 */
	public static function setUsers($users)
	{
		self::$_users = array();
		foreach ($users as $key => $value) {
			if ($value instanceof Atomik_Auth_User) {
				self::addUser($value);
				continue;
			}
			
			$password = $value;
			$roles = array();
			if (is_array($value)) {
				$password = isset($value['password']) ? $value['password'] : '';
				$roles = isset($value['roles']) ? $value['roles'] : array();
			}
			self::addUser($key, $password, $roles);
		}
	}
	
	/**
	 * Adds a new user
	 * 
	 * @param	string|Atomik_Auth_User	$username
	 * @param	string					$password
	 * @param	array					$roles
	 */
	public static function addUser($username, $password = '', $roles = array())
	{
		if ($username instanceof Atomik_Auth_User) {
			self::$_users[$username->username] = $username;
			return;
		}
		
		self::$_users[$username] = new Atomik_Auth_User($username, $password, $roles);
	}
	
	/**
	 * Returns all users
	 * 
	 * @return array
	 */
	public static function getUsers()
	{
		return self::$_users;
	}
	
	/**
	 * Creates and returns an {@see Atomik_Auth_Backend_Array} instance
	 * 
	 * @return Atomik_Auth_Backend_Array
	 */
	public static function getArrayBackend()
	{
		$backend = Atomik_Auth_Backend_Factory::factory('Array');
		foreach (self::$_users as $user) {
			$backend->users[$user->username] = $user->password;
		}
		return $backend;
	}
	
	/**
	 * Returns the user object associated to the specified username
	 * 
	 * @param	string	$username
	 * @return Atomik_Auth_User_Interface
	 */
	public static function find($username)
	{
		if (!isset(self::$_users[$username])) {
			return null;
		}
		return self::$_users[$username];
	}
	
	/**
	 * Constructor
	 * 
	 * @param	string	$username
	 * @param	string	$password
	 * @param	array	$roles
	 */
	public function __construct($username, $password, $roles = array())
	{
		$this->username = $username;
		$this->password = $password;
		$this->roles = $roles;
	}
	
	/**
	 * Returns roles of the user
	 *
	 * @return array
	 */
	public function getRoles()
	{
		return $this->roles;
	}
}