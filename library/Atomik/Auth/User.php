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

/** Atomik_Model */
require_once 'Atomik/Model.php';

/** Atomik_Auth_User_Interface */
require_once 'Atomik/Auth/User/Interface.php';

/** Atomik_Auth_User_Role */
require_once 'Atomik/Auth/User/Role.php';

/**
 * The default user object. Store users in an array.
 * 
 * @package Atomik
 * @subpackage Auth
 * 
 * @adapter Db
 * @table auth_users
 * @has many Atomik_Auth_User_Role as roles
 * @inheritance abstract
 */
class Atomik_Auth_User extends Atomik_Model implements Atomik_Auth_User_Interface
{
	/**
	 * @var string
	 * @sql-type varchar(100)
	 */
	public $username;
	
	/**
	 * @var string
	 * @sql-type varchar(50)
	 */
	public $password;
	
	/**
	 * Checks if the user has access to the specified resource
	 * 
	 * @param string $resource
	 * @return bool
	 */
	public function hasAccessTo($resource)
	{
		return Atomik_Auth::hasAccessTo($resource, $this->getRoles());
	}
	
	/**
	 * Checks if the user roles matches with the needed roles
	 * 
	 * @param string|array $roles
	 * @return bool
	 */
	public function isAllowed($roles)
	{
		return Atomik_Auth::isAllowed($resource, $this->getRoles());
	}
	
	/**
	 * Returns roles of the user
	 *
	 * @return array
	 */
	public function getRoles()
	{
		$roles = array();
		foreach ($this->roles as $role) {
			$roles[] = $role->name;
		}
		return $roles;
	}
}