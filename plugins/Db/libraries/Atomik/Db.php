<?php
/**
 * Atomik Framework
 * Copyright (c) 2008 Maxime Bouroumeau-Fuseau
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
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
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
	 * The db instance
	 *
	 * @var Atomik_Db_Instance
	 */
	protected static $_instance;
	
	/**
	 * Sets the instance
	 *
	 * @param Atomik_Db_Instance $instance OPTIONAL
	 */
	public static function setInstance($instance = null)
	{
		if ($instance === null) {
			$instance = new Atomik_Db_Instance();
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
	public function count($tables, $where = null, $orderBy = '', $limit = '')
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
		return self::getInstance()->delete($table, $where);
	}
}

