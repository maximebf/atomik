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
 * @subpackage Db
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Db_Instance */
require_once 'Atomik/Db/Instance.php';

/**
 * Static class that acts as a proxy for one Atomik_Db_Instance.
 *
 * @see Atomik_Db_Instance
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db
{
	/**
	 * Available db instances
	 *
	 * @var array
	 */
	protected static $_availableInstances = array();
	
	/**
	 * The db instance
	 *
	 * @var Atomik_Db_Instance
	 */
	protected static $_instance;
	
	/**
	 * Resets all available instances
	 * 
	 * @param array $instances
	 */
	public static function setAvailableInstances($instances)
	{
		self::$_availableInstances = array();
		foreach ($instances as $name => $instance) {
			self::addAvailableInstance($name, $instance);
		}
	}
	
	/**
	 * Adds a new available instance
	 * 
	 * @param string $name
	 * @param Atomik_Db_Instance $instance
	 */
	public static function addAvailableInstance($name, Atomik_Db_Instance $instance)
	{
		self::$_availableInstances[$name] = $instance;
	}
	
	/**
	 * Checks if an instance with the specified name exists
	 * 
	 * @param	string	$name
	 * @return	bool
	 */
	public static function isInstanceAvailable($name)
	{
		return isset(self::$_availableInstances[$name]);
	}
	
	/**
	 * Returns all available instances
	 * 
	 * @return array
	 */
	public static function getAvailableInstances()
	{
		return self::$_availableInstances;
	}
	
	/**
	 * Creates a new Atomik_Db_Instance and sets it as the current one
	 * 
	 * @param string $name Instance name
	 * @param string $dsn
	 * @param string $username
	 * @param string $password
	 * @param bool	 $default
	 * @return Atomik_Db_Instance
	 */
	public static function createInstance($name, $dsn, $username, $password, $default = true)
	{
		$instance = new Atomik_Db_Instance($dsn, $username, $password);
		self::addAvailableInstance($name, $instance);
		
		if ($default) {
			self::setInstance($instance);
		}
		
		return $instance;
	}
	
	/**
	 * Sets the instance
	 *
	 * @param Atomik_Db_Instance|string $instance An instance name or an Atomik_Db_Instance object
	 */
	public static function setInstance($instance = null)
	{
		if (is_string($instance)) {
			if (isset(self::$_availableInstances[$instance])) {
				$instance = self::$_availableInstances[$instance];
			} else {
				require_once 'Atomik/Db/Exception.php';
				throw new Atomik_Db_Exception('The instance named ' . $instance . ' does not exist');
			}
		} else if ($instance === null) {
			$instance = new Atomik_Db_Instance();
			if (count(self::$_availableInstances) == 0) {
				self::$_availableInstances['default'] = $instance;
			}
		}
		self::$_instance = $instance;
	}
	
	/**
	 * Gets the instance
	 *
	 * @param	string				$name
	 * @return 	Atomik_Db_Instance
	 */
	public static function getInstance($name = null)
	{
		if ($name !== null) {
			if (!self::isInstanceAvailable($name)) {
				require_once 'Atomik/Db/Exception.php';
				throw new Atomik_Db_Exception('No instance named ' . $name . ' were found');
			}
			return self::$_availableInstances[$name];
		}
		
		if (self::$_instance === null) {
			self::setInstance();
		}
		return self::$_instance;
	}
	
	/**
	 * @see Atomik_Db_Instance::connect()
	 */
	public static function connect($dsn = null, $username = null, $password = null)
	{
		return self::getInstance()->connect($dsn, $username, $password);
	}
	
	/**
	 * @see Atomik_Db_Instance::disconnect()
	 */
	public static function disconnect()
	{
		return self::getInstance()->disconnect();
	}
	
	/**
	 * @see Atomik_Db_Instance::getPdo()
	 */
	public static function getPdo()
	{
		return self::getInstance()->getPdo();
	}
	
	/**
	 * @see Atomik_Db_Instance::setTablePrefix()
	 */
	public static function setTablePrefix($prefix)
	{
		return self::getInstance()->setTablePrefix($prefix);
	}
	
	/**
	 * @see Atomik_Db_Instance::getTablePrefix()
	 */
	public static function getTablePrefix()
	{
		return self::getInstance()->getTablePrefix();
	}
	
	/**
	 * @see Atomik_Db_Instance::enableQueryCache()
	 */
	public static function enableQueryCache($enable = true)
	{
		return self::getInstance()->enableQueryCache($enable);
	}
	
	/**
	 * @see Atomik_Db_Instance::isQueryCacheEnabled()
	 */
	public static function isQueryCacheEnabled()
	{
		return self::getInstance()->isQueryCacheEnabled();
	}
	
	/**
	 * @see Atomik_Db_Instance::enableResultCache()
	 */
	public static function enableResultCache($enable = true)
	{
		return self::getInstance()->enableResultCache($enable);
	}
	
	/**
	 * @see Atomik_Db_Instance::isResultCacheEnabled()
	 */
	public static function isResultCacheEnabled()
	{
		return self::getInstance()->isResultCacheEnabled();
	}
	
	/**
	 * @see Atomik_Db_Instance::emptyCache()
	 */
	public static function emptyCache(Atomik_Db_Query $query = null)
	{
		return self::getInstance()->emptyCache($query);
	}
	
	/**
	 * @see Atomik_Db_Instance::getErrorInfo()
	 */
	public static function getErrorInfo()
	{
		return self::getInstance()->getErrorInfo();
	}
	
	/**
	 * @see Atomik_Db_Instance::q()
	 */
	public function q()
	{
		return self::getInstance()->q();
	}
	
	/**
	 * @see Atomik_Db_Instance::query()
	 */
	public static function query($query, $params = array())
	{
		return self::getInstance()->query($query, $params);
	}
	
	/**
	 * @see Atomik_Db_Instance::exec()
	 */
	public static function exec($query)
	{
		return self::getInstance()->exec($query);
	}
	
	/**
	 * @see Atomik_Db_Instance::prepare()
	 */
	public static function prepare($query, $options = array())
	{
		return self::getInstance()->prepare($query, $options);
	}
	
	/**
	 * @see Atomik_Db_Instance::find()
	 */
	public static function find($table, $where = null, $orderBy = null, $offset = 0, $fields = null)
	{
		return self::getInstance()->find($table, $where, $orderBy, $offset, $fields);
	}
	
	/**
	 * @see Atomik_Db_Instance::findAll()
	 */
	public static function findAll($table, $where = null, $orderBy = null, $limit = null, $fields = null)
	{
		return self::getInstance()->findAll($table, $where, $orderBy, $limit, $fields);
	}
	
	/**
	 * @see Atomik_Db_Instance::findValue()
	 */
	public static function findValue($table, $column, $where = null, $orderBy = null, $offset = 0)
	{
		return self::getInstance()->findValue($table, $column, $where, $orderBy, $offset);
	}
	
	/**
	 * @see Atomik_Db_Instance::count()
	 */
	public static function count($table, $where = null, $limit = null)
	{
		return self::getInstance()->count($table, $where, $limit);
	}
	
	/**
	 * @see Atomik_Db_Instance::has()
	 */
	public static function has($table, $where, $limit = null)
	{
		return self::getInstance()->has($table, $where, $limit);
	}
	
	/**
	 * @see Atomik_Db_Instance::insert()
	 */
	public static function insert($table, $data)
	{
		return self::getInstance()->insert($table, $data);
	}
	
	/**
	 * @see Atomik_Db_Instance::update()
	 */
	public static function update($table, $data, $where)
	{
		return self::getInstance()->update($table, $data, $where);
	}
	
	/**
	 * @see Atomik_Db_Instance::set()
	 */
	public static function set($table, $data, $where = null)
	{
		return self::getInstance()->set($table, $data, $where);
	}
	
	/**
	 * @see Atomik_Db_Instance::delete()
	 */
	public static function delete($table, $where = array())
	{
		return self::getInstance()->delete($table, $where);
	}
}

