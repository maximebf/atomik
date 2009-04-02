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

/** Atomik_Model_Adapter_Interface */
require_once 'Atomik/Model/Adapter/Interface.php';

/** Atomik_Db */
require_once 'Atomik/Db.php';

/**
 * Stores models as database tables
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Adapter_Db implements Atomik_Model_Adapter_Interface
{
	/**
	 * @var Atomik_Db_Instance
	 */
	protected static $_db;
	
	/**
	 * Sets the database instance
	 *
	 * @param Atomik_Db_Instance $db
	 */
	public static function setDb(Atomik_Db_Instance $db = null)
	{
		if ($db === null) {
			if (($db = Atomik_Db::getInstance()) === null) {
				require_once 'Atomik/Model/Exception.php';
				throw new Atomik_Model_Exception('No database instances found');
			}
		}
		self::$_db = $db;
	}
	
	/**
	 * Gets the database instance
	 *
	 * @return Atomik_Db_Instance
	 */
	public static function getDb()
	{
		if (self::$_db === null) {
			self::setDb();
		}
		return self::$_db;
	}
	
	/**
	 * Converts a model query to a db query
	 * 
	 * @param	Atomik_Model_Query	$query
	 * @return 	Atomik_Db_Query
	 */
	public static function convertModelQueryToDbQuery(Atomik_Model_Query $query)
	{
		$dbQuery = new Atomik_Db_Query();
		$dbQuery->from(self::getTableNameFromBuilder($query->from));
		
		if (!empty($query->where)) {
			$dbQuery->where($query->where);
		}
		if (!empty($query->orderByField)) {
			$dbQuery->orderBy($query->orderByField, $query->orderByDirection);
		}
		if (!empty($query->length)) {
			$dbQuery->limit($query->limitOffset, $query->limitLength);
		}
		
		return $dbQuery;
	}
	
	/**
	 * Returns the table name associated to a model
	 *
	 * @param Atomik_Model_Builder $builder
	 * @return string
	 */
	public static function getTableNameFromBuilder(Atomik_Model_Builder $builder)
	{
		return $builder->getOption('table', $builder->name);
	}
	
	/**
	 * Query the adapter
	 * 
	 * @param	Atomik_Model_Query	$query
	 * @return 	Atomik_Model_Modelset
	 */
	public function query(Atomik_Model_Query $query)
	{
		$dbQuery = self::convertModelQueryToDbQuery($query);
		$rows = self::getDb()->query($dbQuery);
		return new Atomik_Model_Modelset($query->builder, $rows);
	}
	
	/**
	 * Saves a model
	 *
	 * @param 	Atomik_Model $model
	 * @return 	bool
	 */
	public function save(Atomik_Model $model)
	{
		$data = $model->toArray();
		$builder = $model->getBuilder();
		$tableName = self::getTableNameFromBuilder($builder);
		
		// insert
		if ($model->isNew()) {
			if (($id = self::getDb()->insert($tableName, $data)) === false) {
				return false;
			}
			$model->setPrimaryKey($id);
			return true;
		}
		
		// update
		$where = array($builder->getPrimaryKeyField()->name => $model->getPrimaryKey());
		return self::getDb()->update($tableName, $data, $where);
	}
	
	/**
	 * Deletes a model
	 *
	 * @param 	Atomik_Model $model
	 * @return 	bool
	 */
	public function delete(Atomik_Model $model)
	{
		if ($model->isNew()) {
			return false;
		}
		
		$builder = $model->getBuilder();
		$tableName = self::getTableNameFromBuilder($builder);
		
		$where = array($builder->getPrimaryKeyField()->name => $model->getPrimaryKey());
		return self::getDb()->delete($tableName, $where);
	}
}