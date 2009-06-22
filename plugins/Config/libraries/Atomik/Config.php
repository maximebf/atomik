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
 * @subpackage Config
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/**
 * @package Atomik
 * @subpackage Config
 */
class Atomik_Config
{
	/**
	 * @var Atomik_Config_Backend_Interface
	 */
	protected static $_backend;
	
	/**
	 * Sets the backend to store the configuration
	 * 
	 * @param Atomik_Config_Backend_Interface $backen
	 */
	public static function setBackend(Atomik_Config_Backend_Interface $backend)
	{
		self::$_backend = $backend;
	}
	
	/**
	 * Returns the storage backend
	 * 
	 * @return Atomik_Config_Backend_Interface
	 */
	public static function getBackend()
	{
		return self::$_backend;
	}
	
	/**
	 * Returns the backend or throw an exception if no backend is defined
	 * 
	 * @return Atomik_Config_Backend_Interface
	 */
	protected static function _getBackend()
	{
		if (self::$_backend === null) {
			require_once 'Atomik/Config/Exception.php';
			throw new Atomik_Config_Exception('A backend must be specified for Atomik_Config');
		}
		return self::$_backend;
	}
	
	/**
	 * Returns a dimensionized array of all stored value
	 * 
	 * @return array
	 */
	public static function getAll()
	{
		return self::_getBackend()->getAll();
	}
	
	/**
	 * Same as {@see Atomik::set()} but also store the key in the backend
	 * 
	 * @param array|string 	$key 			Can be an array to set many key/value
	 * @param mixed 		$value
	 * @param bool 			$dimensionize 	Whether to use Atomik::_dimensionizeArray() on $key
	 */
	public static function set($key, $value = null, $dimensionize = true)
	{
		if (is_array($key)) {
			self::_setArray($key);
			
		} else {
			self::_getBackend()->set($key, $value);
		}
		
		$null = null;
		return Atomik::set($key, $value, $dimensionize, $null);
	}
	
	/**
	 * Sets recursively all keys from the array in the backend
	 * 
	 * @param	array	$array
	 * @param	string	$parent		Parent key
	 */
	protected static function _setArray($array, $parent = '')
	{
		foreach ($array as $key => $value) {
			$realKey = trim($parent . '/' . $key, '/');
			if (is_array($value)) {
				self::_setArray($value, $realKey);
				continue;
			}
			self::_getBackend()->set($realKey, $value);
		}
	}
	
	/**
	 * Same as {@see Atomik::delete()} but also delete the key from the backend
	 * 
	 * @param 	string 	$key
	 * @return 	mixed 			The deleted value
	 */
	public static function delete($key)
	{
		self::deleteFromBackendOnly($key);
		return Atomik::delete($key);
	}
	
	/**
	 * Only deletes the key from the backend
	 * 
	 * @param 	string 	$key
	 */
	public static function deleteFromBackendOnly($key)
	{
		self::_getBackend()->delete($key);
	}
}