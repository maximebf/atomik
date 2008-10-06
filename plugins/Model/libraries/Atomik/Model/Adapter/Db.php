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
 * @subpackage Model
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Model_Adapter_Interface */
require_once 'Atomik/Model/Adapter/Interface.php';

/** Atomik_Model */
require_once 'Atomik/Model.php';

/** Atomik_Model_Builder */
require_once 'Atomik/Model/Builder.php';

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
				throw new Atomik_Model_Exception('No database instance found');
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
	 * Perform an sql query an returns rows as models
	 *
	 * @param Atomik_Model_Builder $builder
	 * @param string $query
	 * @return array
	 */
	public function query(Atomik_Model_Builder $builder, $query)
	{
		// TODO: implement Atomik_Model_Adapter_Db::query()
		return array();
	}
	
	/**
	 * Finds many models
	 *
	 * @param Atomik_Model_Builder $builder
	 * @param array|string $where
	 * @param string $orderBy
	 * @param string $limit
	 * @return array
	 */
	public function findAll(Atomik_Model_Builder $builder, $where = null, $orderBy = '', $limit = '')
	{
		$rows = self::getDb()->findAll($this->getTableName($builder), $where, $orderBy, $limit);
		$models = array();
		
		foreach ($rows as $row) {
			$models[] = $builder->createInstance($row, false);
		}
		
		return $models;
	}
	
	/**
	 * Finds one model
	 *
	 * @param Atomik_Model_Builder $builder
	 * @param array|string $where
	 * @param string $orderBy
	 * @param string $limit
	 * @return Atomik_Model
	 */
	public function find(Atomik_Model_Builder $builder, $where, $orderBy = '', $limit = '')
	{
		if (($row = self::getDb()->find($this->getTableName($builder), $where, $orderBy, $limit)) === null) {
			return null;
		}
		return $builder->createInstance($row, false);
	}
	
	/**
	 * Saves a model
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function save(Atomik_Model $model)
	{
		$data = $model->toArray();
		$builder = $model->getBuilder();
		$tableName = $this->getTableName($builder);
		
		// insert
		if ($model->isNew()) {
			if (($id = self::getDb()->insert($tableName, $data)) === false) {
				return false;
			}
			$model->setPrimaryKey($id);
			return true;
		}
		
		// update
		$where = array($builder->getPrimaryKeyField() => $model->getPrimaryKey());
		return self::getDb()->update($tableName, $data, $where);
	}
	
	/**
	 * Deletes a model
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function delete(Atomik_Model $model)
	{
		if ($model->isNew()) {
			return;
		}
		
		$builder = $model->getBuilder();
		$tableName = $this->getTableName($builder);
		
		$where = array($builder->getPrimaryKeyField() => $model->getPrimaryKey());
		return self::getDb()->delete($tableName, $where);
	}
	
	/**
	 * Gets the table name associated to a model
	 *
	 * @param Atomik_Model_Builder $builder
	 * @return string
	 */
	protected function getTableName($builder)
	{
		$table = $builder->getOption('table');
		if ($table === null) {
			throw new Exception('Table not set on model ' . $builder->getName());
		}
		return $table;
	}
}