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

/** Atomik_Model_Builder */
require_once 'Atomik/Model/Builder.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Builder_Factory
{
	/**
	 * @var array
	 */
	protected static $_cache = array();
	
	/**
	 * Returns a builder instance for to the model of the specified name
	 * 
	 * @param 	string|objet 			$name
	 * @return 	Atomik_Model_Builder
	 */
	public static function get($name)
	{
		if ($name instanceof Atomik_Model_Builder) {
			return $name;
		}
		
		if (is_object($name)) {
			$name = get_class($name);
		}
		
		if (isset(self::$_cache[$name])) {
			return self::$_cache[$name];
		}
		
		if (class_exists($name)) {
			require_once 'Atomik/Model/Builder/ClassMetadata.php';
			self::$_cache[$name] = Atomik_Model_Builder_ClassMetadata::read($name);
			return self::$_cache[$name];
		}
		
		require_once 'Atomik/Model/Builder/Exception.php';
		throw new Atomik_Model_Builder_Exception('No model builder named ' . $name . ' were found');
	}
	
	/**
	 * Returns the builder associated to the specified table name
	 * 
	 * @param 	string	$tableName
	 * @return 	Atomik_Model_Builder
	 */
	public static function getFromTableName($tableName)
	{
		foreach (self::$_cache as $builder) {
			if ($builder->tableName == $tableName) {
				return $builder;
			}
		}
		
		return self::get($tableName);
	}
	
	/**
	 * Creates a new builder object and store it in cache
	 * 
	 * @param	string	$name
	 * @return 	Atomik_Model_Builder
	 */
	public static function create($name, $className = null)
	{
		$builder = new Atomik_Model_Builder($name, $className);
		self::$_cache[$name] = $builder;
		return $builder;
	}
	
	/**
	 * Empties the cache
	 */
	public static function invalidateCache()
	{
		self::$_cache = array();
	}
}