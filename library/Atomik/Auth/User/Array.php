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
 * Store users in an array.
 * 
 * @package Atomik
 * @subpackage Auth
 */
class Atomik_Auth_User_Array
{
	/** @var string */
	protected $_username;
	
	/** @var string */
	protected $_password;
	
	/** @var username */
	protected $_roles = array();
	
	/**
	 * @param string $username
	 * @param string $password
	 * @param array $roles
	 */
	public function __construct($username, $password, $roles = array())
	{
		$this->_username = $username;
		$this->_password = $password;
		$this->_roles = $roles;
	}
	
	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
	    $this->_username = $username;
	}
	
	/**
	 * @return string
	 */
	public function getUsername()
	{
	    return $this->_username;
	}
	
	/**
	 * @param string $password
	 */
	public function setPassword($password)
	{
	    $this->_password = $password;
	}
	
	/**
	 * @return string
	 */
	public function getPassword()
	{
	    return $this->_password;
	}
	
	/**
	 * @param array $roles
	 */
	public function setRoles($roles)
	{
	    $this->_roles = $roles;
	}
	
	/**
	 * @return array
	 */
	public function getRoles()
	{
		return $this->roles;
	}
	
	/**
	 * Checks if the user has access to the specified resource
	 * 
	 * @param string $resource
	 * @return bool
	 */
	public function hasAccessTo($resource)
	{
		return Atomik_Auth::hasAccessTo($resource, $this->roles);
	}
	
	/**
	 * Checks if the user roles matches with the needed roles
	 * 
	 * @param string|array $roles
	 * @return bool
	 */
	public function isAllowed($roles)
	{
		return Atomik_Auth::isAllowed($roles, $this->roles);
	}
}