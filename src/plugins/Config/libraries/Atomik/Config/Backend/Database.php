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

/** Atomik_Config_Backend_Interface */
require_once 'Atomik/Config/Backend/Interface.php';

/**
 * @package Atomik
 * @subpackage Config
 */
class Atomik_Config_Backend_Database implements Atomik_Config_Backend_Interface
{
	/**
	 * @var string
	 */
	public static $tableName = 'config';
	
	/**
	 * @var Atomik_Db_Instance
	 */
	protected $_dbInstance;
	
	/**
	 * Returns the sql code needed to create the database table to store the config
	 * 
	 * @return string
	 */
	public static function getTableSql()
	{
		return "DROP TABLE IF EXISTS " . self::$tableName . ";\n"
			 . "CREATE TABLE " . self::$tableName . " (\n\t"
			 . "id INT PRIMARY KEY AUTO_INCREMENT,\n\tparent_id INT,\n\tname VARCHAR(100),\n\tvalue TEXT\n);\n";
	}
	
	/**
	 * Constructor
	 * 
	 * @param string|Atomik_Db_Instance $dbInstance
	 */
	public function __construct($tableName = 'config', $dbInstance = null)
	{
		self::$tableName = $tableName;
		$this->setDbInstance($dbInstance);
	}
	
	/**
	 * Sets the db instance to use
	 * 
	 * @param string|Atomik_Db_Instance $dbInstance
	 */
	public function setDbInstance($dbInstance)
	{
		if ($dbInstance instanceof Atomik_Db_Instance) {
			$this->_dbInstance = $dbInstance;
			return;
		}
		
		$this->_dbInstance = Atomik_Db::getInstance($dbInstance);
	}
	
	/**
	 * Returns the db instance. If none is set, returns the current one from {@see Atomik_Db}
	 * 
	 * @return Atomik_Db_Instance
	 */
	public function getDbInstance()
	{
		if ($this->_dbInstance === null) {
			return Atomik_Db::getInstance();
		}
		return $this->_dbInstance;
	}
	
	/**
	 * Returns a dimensionized array of all keys from the database
	 * 
	 * @return array
	 */
	public function getAll()
	{
		return $this->_get();
	}
	
	/**
	 * Recursively fetch all keys
	 * 
	 * @param 	string	$parent
	 * @return 	array
	 */
	protected function _get($parentId = 0)
	{
		$array = array();
		
		if (($rows = $this->getDbInstance()->findAll(self::$tableName, array('parent_id' => $parentId))) === false) {
			return $array;
		}
		
		foreach ($rows as $row) {
			if ($this->getDbInstance()->has(self::$tableName, array('parent_id' => $row['id']))) {
				$array[$row['name']] = $this->_get($row['id']);
			} else {
				$array[$row['name']] = unserialize($row['value']);
			}
		}
		
		return $array;
	}
	
	/**
	 * Sets a key in the database
	 * 
	 * @param	string	$key
	 * @param 	mixed	$value
	 */
	public function set($key, $value)
	{
		return $this->_setRecursive($key, $value);
	}
	
	/**
	 * Sets a key in the database
	 * 
	 * @param	string	$key
	 * @param 	mixed	$value
	 * @param 	int		$parentId
	 */
	protected function _setRecursive($key, $value, $parentId = 0)
	{
		if (strpos($key, '/') !== false) {
			$segments = explode('/', $key);
			$key = array_shift($segments);
			
			$this->set($key, array(), $parentId);
			$parentId = $this->getDbInstance()->findValue(self::$tableName, 'id', array('name' => $key, 'parent_id' => $parentId));
			
			return $this->_setRecursive(implode('/', $segments), $value, $parentId);
		}
		
		$value = serialize($value);
		return $this->getDbInstance()->set(self::$tableName, 
			array('name' => $key, 'value' => $value, 'parent_id' => $parentId), 
			array('name', 'parent_id'));
	}
	
	/**
	 * Deletes a key in the database
	 * 
	 * @param 	string	$key
	 */
	public function delete($key)
	{
		$keyId = $this->_findKeyId($key);
		return $this->_deleteRecursive($keyId);
	}
	
	/**
	 * Finds the id of the specified key
	 * 
	 * @param	string	$key
	 * @param	int		$parentId
	 * @return 	int
	 */
	protected function _findKeyId($key, $parentId = 0)
	{
		if (strpos($key, '/') !== false) {
			$segments = explode('/', $key);
			$key = array_shift($segments);

			return $this->_findKeyId(implode('/', $segments), $this->_findKeyId($key, $parentId));
		}
		
		return $this->getDbInstance()->findValue(self::$tableName, 'id', array('name' => $key, 'parent_id' => $parentId));
	}
	
	/**
	 * Deletes recursively the key and all its children
	 * 
	 * @param	int		$keyId
	 */
	protected function _deleteRecursive($keyId)
	{
		if ($this->getDbInstance()->has(self::$tableName, array('parent_id' => $keyId))) {
			$children = $this->getDbInstance()->findAll(self::$tableName, array('parent_id' => $keyId), null, null, array('id'));
			foreach ($children as $child) {
				$this->_deleteRecursive($child['id']);
			}
		}
		
		return $this->getDbInstance()->delete(self::$tableName, array('id' => $keyId));
	}
}