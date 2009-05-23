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

require_once 'Atomik/Auth/User/Locator/Interface.php';

/**
 * Used to get a user object
 * 
 * @package Atomik
 * @subpackage Auth
 */
class Atomik_Auth_User_Locator_Model implements Atomik_Auth_User_Locator_Interface
{
	/**
	 * @var string
	 */
	private static $_modelName;
	
	/**
	 * Sets the model name to use for user objects
	 * 
	 * @param	array|string	$source
	 */
	public static function setModelName($name)
	{
    	self::$_modelName = $name;
	}
	
	/**
	 * Returns the model name used for user objects
	 * 
	 * @return string
	 */
	public static function getModelName()
	{
		if (self::$_modelName === null) {
			require_once 'Atomik/Auth/Exception.php';
			throw new Atomik_Auth_Exception('A model name must be specified for Atomik_Auth_User_Locator_Model');
		}
		return self::$_modelName;
	}
	
	/**
	 * Returns the object for the specified username
	 * 
	 * @param	string	$username
	 * @return 	Atomik_Auth_User_Interface
	 */
	public static function find($username)
	{
		return Atomik_Model_Locator::findOne(self::$_modelName, array('username' => $username));
	}
}