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

/** Atomik_Auth_User_Interface */
require_once 'Atomik/Auth/User/Interface.php';

/**
 * Used to get a user object
 * 
 * @package Atomik
 * @subpackage Auth
 */
class Atomik_Auth_User_Locator
{
	/**
	 * @var string
	 */
	private static $_source;
	
	/**
	 * Sets the source to get user objects
	 * 
	 * If $source is an array, it will use the Atomik_Auth_User class and call {@see Atomik_Auth_User::setUsers()}
	 * Otherwise, $source should be a class name of a class implementing {@see Atomik_Auth_User_Interface}
	 * 
	 * @param	array|string	$source
	 */
	public static function setSource($source)
	{
		if (is_array($source)) {
			Atomik_Auth_User::setUsers($source);
			self::$_source = 'Atomik_Auth_User';
			return;
		}
		
		if (!class_exists($source)) {
			throw new Atomik_Auth_Exception('The source is a string but no class with this name was found: ' . $source);
		}
		
    	$class = new ReflectionClass($source);
    	if (!$class->implementsInterface('Atomik_Auth_User_Interface')) {
    		throw new Atomik_Auth_Exception('User class must implement Atomik_Auth_User_Interface (' . $source . ')');
    	}
    	
    	self::$_source = $source;
	}
	
	/**
	 * Returns the class name being used as the source
	 * 
	 * @return string
	 */
	public static function getSource()
	{
		return self::$_source;
	}
	
	/**
	 * Returns the object for the specified username
	 * 
	 * @param	string	$username
	 * @return 	Atomik_Auth_User_Interface
	 */
	public static function find($username)
	{
		return call_user_func(array(self::$_source, 'find'), $username);
	}
}