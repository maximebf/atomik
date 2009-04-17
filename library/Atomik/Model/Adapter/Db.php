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

/** Atomik_Db_Script_Model_Exportable */
require_once 'Atomik/Db/Script/Model/Exportable.php';

/** Atomik_Db */
require_once 'Atomik/Db.php';

/**
 * Stores models as database tables
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Adapter_Db implements Atomik_Model_Adapter_Interface, Atomik_Db_Script_Model_Exportable 
{
	/**
	 * @var array
	 */
	public static $primaryKeyMap = array(
		'mysql' => ' int PRIMARY KEY AUTO_INCREMENT',
		'sqlite' => ' int PRIMARY KEY AUTOINCREMENT',
		'pgsql' => ' serial PRIMARY KEY'
	);
	
	/**
	 * @var Atomik_Db_Instance
	 */
	protected static $_defaultDbInstance;
	
	/**
	 * Sets the default database instance
	 *
	 * @param Atomik_Db_Instance $db
	 */
	public static function setDefaultDbInstance(Atomik_Db_Instance $db = null)
	{
		if ($db === null) {
			$db = Atomik_Db::getInstance();
		}
		self::$_defaultDbInstance = $db;
	}
	
	/**
	 * Gets the default database instance
	 *
	 * @return Atomik_Db_Instance
	 */
	public static function getDefaultDbInstance()
	{
		if (self::$_defaultDbInstance === null) {
			self::setDefaultDbInstance();
		}
		return self::$_defaultDbInstance;
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
		$dbQuery->select()->from(self::getTableNameFromBuilder($query->from));
		
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
	 * Returns the table name associated to a builder
	 *
	 * @return string
	 */
	public static function getTableNameFromBuilder(Atomik_Model_Builder $builder)
	{
		return $builder->getOption('table', $builder->name);
	}
	
	/**
	 * Returns the db instance associated to the builder
	 *
	 * @return Atomik_Db_Instance
	 */
	public static function getDbInstanceFromBuilder(Atomik_Model_Builder $builder)
	{
		if (($instance = $builder->getOption('db-instance')) !== null) {
			return Atomik_Db::getInstance($instance);
		}
		return self::getDefaultDbInstance();
	}
	
	/**
	 * Returns the sql needed to create the associated table
	 * 
	 * @param Atomik_Model_Builder $builder
	 * @return string
	 */
	public static function getSqlDefinition(Atomik_Model_Builder $builder)
	{
		$primaryKeyField = $builder->getPrimaryKeyField();
		$tableName = self::getTableNameFromBuilder($builder);
		
		$fields = array();
		$indexes = array();
		
		$fields[] = "\t" . $primaryKeyField->name . self::getPrimaryKeySpecFromBuilder($builder);
		
		foreach ($builder->getFields() as $field) {
			if ($field == $primaryKeyField) {
				continue;
			}
			
			$type = $field->getOption('sql-type', $field->getOption('var', 'varchar(50)'));
			$null = $field->getOption('sql-nullable', false) ? 'NULL' : 'NOT NULL';
			$fields[] = "\t" . $field->name . ' ' . $type . ' ' . $null;
			
			if ($field->hasOption('sql-index') || $type == 'int') {
				if (($indexName = $field->getOption('sql-index', true)) === true) {
					$indexName = 'idx_' . $tableName . '_' . $field->name;
				}
				$indexes[] = sprintf("CREATE INDEX %s ON %s (%s);\n", $indexName, $tableName, $field->name);
			}
		}
		
		$sql = sprintf("DROP TABLE IF EXISTS %s;\n", $tableName);
		$sql .= sprintf("CREATE TABLE %s (\n%s\n);\n", $tableName, implode(", \n", $fields));
		$sql .= implode('', $indexes);
		
		return $sql;
	}
	
	/**
	 * Returns the sql string for a primary key field definition
	 * 
	 * @param Atomik_Model_Builder $builder
	 * @return string
	 */
	public static function getPrimaryKeySpecFromBuilder(Atomik_Model_Builder $builder)
	{
		$driver = self::getDbInstanceFromBuilder($builder)->getPdoDriverName();
		return isset(self::$primaryKeyMap[$driver]) ? 
			self::$primaryKeyMap[$driver] : ' int PRIMARY KEY';
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
		$rows = self::getDbInstanceFromBuilder($query->from)->query($dbQuery);
		$rows->setFetchMode(PDO::FETCH_ASSOC);
		return new Atomik_Model_Modelset($query->from, $rows);
	}
	
	/**
	 * Saves a model
	 *
	 * @param 	Atomik_Model $model
	 * @return 	bool
	 */
	public function save(Atomik_Model $model)
	{
		$builder = $model->getBuilder();
		$db = self::getDbInstanceFromBuilder($builder);
		$tableName = self::getTableNameFromBuilder($builder);
		$data = $model->toArray();
		
		// insert
		if ($model->isNew()) {
			if (($id = $db->insert($tableName, $data)) === false) {
				return false;
			}
			$model->setPrimaryKey($id);
			return true;
		}
		
		// update
		$where = array($builder->getPrimaryKeyField()->name => $model->getPrimaryKey());
		return $db->update($tableName, $data, $where);
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
		$db = self::getDbInstanceFromBuilder($builder);
		$tableName = self::getTableNameFromBuilder($builder);
		
		$where = array($builder->getPrimaryKeyField()->name => $model->getPrimaryKey());
		return $db->delete($tableName, $where);
	}
}