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
class Atomik_Model_Adapter_Table implements Atomik_Model_Adapter_Interface
{
	/**
	 * @var Atomik_Db_Instance
	 */
	protected $_db;
	
	/**
	 * Singleton instance
	 *
	 * @var Atomik_Model_Adapter_Table
	 */
	protected static $_instance;
	
	/**
	 * Gets the singleton
	 *
	 * @return Atomik_Model_Adapter_Table
	 */
	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Sets the database instance
	 *
	 * @param Atomik_Db_Instance $db
	 */
	public function setDb(Atomik_Db_Instance $db = null)
	{
		if ($db === null) {
			if (($db = Atomik_Db::getInstance()) === null) {
				require_once 'Atomik/Model/Exception.php';
				throw new Atomik_Model_Exception('No database instance found');
			}
		}
		$this->_db = $db;
	}
	
	/**
	 * Gets the database instance
	 *
	 * @return Atomik_Db_Instance
	 */
	public function getDb()
	{
		if ($this->_db === null) {
			$this->setDb();
		}
		return $this->_db;
	}
	
	/**
	 * Finds many models
	 *
	 * @param Atomik_Model_Builder $builder
	 * @param array $where
	 * @param string $orderBy
	 * @param string $limit
	 * @return array
	 */
	public function findAll(Atomik_Model_Builder $builder, $where = null, $orderBy = '', $limit = '')
	{
		$rows = $this->getDb()->findAll($this->getTableName($builder), $where, $orderBy, $limit);
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
	 * @param array $where
	 * @param string $orderBy
	 * @param string $limit
	 * @return Atomik_Model
	 */
	public function find(Atomik_Model_Builder $builder, $where, $orderBy = '', $limit = '')
	{
		$row = $this->getDb()->find($this->getTableName($builder), $where, $orderBy, $limit);
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
		$primaryKey = $this->getPrimaryKey($builder);
		
		// insert
		if ($model->isNew()) {
			if (($id = $this->getDb()->insert($tableName, $data)) === false) {
				return false;
			}
			$model->{$primaryKey} = $id;
			return true;
		}
		
		// update
		$where = array($primaryKey => $model->{$primaryKey});
		return $this->getDb()->update($tableName, $data, $where);
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
		$primaryKey = $this->getPrimaryKey($builder);
		
		$where = array($primaryKey => $model->{$primaryKey});
		return $this->getDb()->delete($tableName, $where);
	}
	
	/**
	 * Gets the table name associated to a model
	 *
	 * @param Atomik_Model_Builder $builder
	 * @return string
	 */
	protected function getTableName($builder)
	{
		$table = $builder->getMetadata('table');
		if ($table === null) {
			throw new Exception('Table not set on model ' . $builder->getClass());
		}
		return $table;
	}
	
	/**
	 * Gets the primary key of the associated table
	 *
	 * @param Atomik_Model_Builder $builder
	 * @return string
	 */
	protected function getPrimaryKey($builder)
	{
		return $builder->getMetadata('primary-key', 'id');
	}
}