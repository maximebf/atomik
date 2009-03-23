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
	 * @return Atomik_Db_Instance
	 */
	public static function createInstance($name, $dsn, $username, $password)
	{
		$instance = new Atomik_Db_Instance($dsn, $username, $password);
		self::addAvailableInstance($name, $instance);
		self::setInstance($instance);
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
	 * @return Atomik_Db_Instance
	 */
	public static function getInstance()
	{
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
	public static function find($tables, $where = null, $orderBy = '', $limit = '', $fields = '*')
	{
		return self::getInstance()->find($tables, $where, $orderBy, $limit, $fields);
	}
	
	/**
	 * @see Atomik_Db_Instance::findAll()
	 */
	public static function findAll($tables, $where = null, $orderBy = '', $limit = '', $fields = '*')
	{
		return self::getInstance()->findAll($tables, $where, $orderBy, $limit, $fields);
	}
	
	/**
	 * @see Atomik_Db_Instance::count()
	 */
	public static function count($tables, $where = null, $orderBy = '', $limit = '')
	{
		return self::getInstance()->count($tables, $where, $orderBy, $limit);
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
	 * @see Atomik_Db_Instance::delete()
	 */
	public static function delete($tables, $where = array())
	{
		return self::getInstance()->delete($tables, $where);
	}
}

