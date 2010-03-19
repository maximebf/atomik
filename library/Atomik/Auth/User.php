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

/**
 * The default user object. Store users in an array.
 * 
 * @package Atomik
 * @subpackage Auth
 * 
 * @Model(table="auth_users", inheritance="abstract")
 */
class Atomik_Auth_User extends Atomik_Model implements Atomik_Auth_User_Interface
{
	/**
	 * @Field(type="string", length=100, repr=true)
	 * @Form(label="Username")
	 */
	public $username;
	
	/**
	 * @Field(type="string", length=50)
	 * @Form(label="Password", helper="formPassword")
	 */
	public $password;
	
	/**
	 * @Field(type="string")
	 * @Form(label="Roles (comma-separated):")
	 */
	public $roles;
	
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
		return Atomik_Auth::isAllowed($roles, $this->getRoles());
	}
	
	/**
	 * Returns roles of the user
	 *
	 * @return array
	 */
	public function getRoles()
	{
		return array_map('trim', explode(',', $this->roles));
	}
}