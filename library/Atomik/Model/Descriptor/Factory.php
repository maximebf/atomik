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
 * @subpackage Model
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Model_Descriptor */
require_once 'Atomik/Model/Descriptor.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Descriptor_Factory
{
	/**
	 * @var array
	 */
	protected static $_cache = array();
	
	/**
	 * Returns a descriptor instance for to the model of the specified name
	 * 
	 * @param 	string|objet 			$name
	 * @return 	Atomik_Model_Descriptor
	 */
	public static function get($name)
	{
		if ($name instanceof Atomik_Model_Descriptor) {
			return $name;
		}
		
		if (is_object($name)) {
			$name = get_class($name);
		}
		
		if (isset(self::$_cache[$name])) {
			return self::$_cache[$name];
		}
		
		if (class_exists($name)) {
			require_once 'Atomik/Model/Descriptor/ClassMetadata.php';
			self::$_cache[$name] = Atomik_Model_Descriptor_ClassMetadata::read($name);
			return self::$_cache[$name];
		}
		
		require_once 'Atomik/Model/Descriptor/Exception.php';
		throw new Atomik_Model_Descriptor_Exception('No model descriptor named ' . $name . ' were found');
	}
	
	/**
	 * Returns the descriptor associated to the specified table name
	 * 
	 * @param 	string	$tableName
	 * @return 	Atomik_Model_Descriptor
	 */
	public static function getFromTableName($tableName)
	{
		foreach (self::$_cache as $descriptor) {
			if ($descriptor->tableName == $tableName) {
				return $descriptor;
			}
		}
		
		return self::get($tableName);
	}
	
	/**
	 * Creates a new descriptor object and store it in cache
	 * 
	 * @param	string	$name
	 * @return 	Atomik_Model_Descriptor
	 */
	public static function create($name, $className = null)
	{
		$descriptor = new Atomik_Model_Descriptor($name, $className);
		self::$_cache[$name] = $descriptor;
		return $descriptor;
	}
	
	/**
	 * Empties the cache
	 */
	public static function invalidateCache()
	{
		self::$_cache = array();
	}
}