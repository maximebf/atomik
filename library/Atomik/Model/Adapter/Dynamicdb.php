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

/** Atomik_Model_Adapter_Db */
require_once 'Atomik/Model/Adapter/Db.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Adapter_Dynamicdb extends Atomik_Model_Adapter_Db
{
	/**
	 * Returns the sql needed to create the associated table
	 */
	public static function getSqlDefinition(Atomik_Model_Builder $builder)
	{
		$sql = Atomik_Model_Adapter_Db::getSqlDefinition($builder);
		
		$tpl = "CREATE TABLE %s_dynamic (\n\tdynamic_id %s,\n\tdynamic_%s INT NOT NULL,\n\t"
			 . "dynamic_field_name VARCHAR(100) NOT NULL,\n\t"
			 . "dynamic_field_value TEXT NOT NULL\n);\n"
			 . "CREATE INDEX idx_%s_dynamic_%s ON %s (%s);\n";
			 
		$tableName = Atomik_Model_Adapter_Db::getTableNameFromBuilder($builder);
		$primaryKeySpec = Atomik_Model_Adapter_Db::getPrimaryKeySpecFromBuilder($builder);
		$foreignFieldName = $tableName . '_' . $builder->getPrimaryKeyField()->name;
		$sql .= sprintf($tpl, $tableName, $primaryKeySpec, $foreignFieldName, $tableName, $foreignFieldName, $tableName, $foreignFieldName);
		
		return $sql;
	}
	
	/**
	 * Query the adapter
	 * 
	 * @param	Atomik_Model_Query	$query
	 * @return 	Atomik_Model_Modelset
	 */
	public function query(Atomik_Model_Query $query)
	{
		$builder = $query->from;
		$tableName = Atomik_Model_Adapter_Db::getTableNameFromBuilder($builder);
		$primaryKeyName = $builder->getPrimaryKeyField()->name;
		$foreignFieldName = 'dynamic_' . $tableName . '_' . $primaryKeyName;
		$on = sprintf('%s_dynamic.%s = %s.%s', $tableName, $foreignFieldName, $tableName, $primaryKeyName);
		
		// add a join clause to the query
		$dbQuery = Atomik_Model_Adapter_Db::convertModelQueryToDbQuery($query);
		$dbQuery->join($tableName . '_dynamic', $on, null, 'LEFT');
		
		// execute query
		$rows = array();
		$results = Atomik_Model_Adapter_Db::getDbInstanceFromBuilder($builder)->query($dbQuery);
		$results->setFetchMode(PDO::FETCH_ASSOC);
		
		// parses the results to rebuild the row with the dynamic fields
		foreach ($results as $row) {
			$primaryKey = $row[$primaryKeyName];
			if ($row['dynamic_id'] === null) {
				// no dyn field
				$rows[$primaryKey] = $this->_stripDynamicKeysFromRow($row);
				continue;
			}
			if (!isset($rows[$primaryKey])) {
				$rows[$primaryKey] = $this->_stripDynamicKeysFromRow($row);;
			}
			$rows[$primaryKey][$row['dynamic_field_name']] = $row['dynamic_field_value'];
		}
		
		return array_values($rows);
	}
	
	/**
	 * Strips a row array from the dynamic table columns
	 * 
	 * @param	array	$row
	 * @return 	array
	 */
	protected function _stripDynamicKeysFromRow($row)
	{
		$strippedRow = array();
		foreach ($row as $key => $value) {
			if (substr($key, 0, 8) == 'dynamic_') {
				continue;
			}
			$strippedRow[$key] = $value;
		}
		return $strippedRow;
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
		$isNew = $model->isNew();
		$fullDynamic = $builder->getOption('dynamic', false);
		
		if (!$fullDynamic && !parent::save($model)) {
			return false;
		}
		
		$db = Atomik_Model_Adapter_Db::getDbInstanceFromBuilder($builder);
		$tableName = Atomik_Model_Adapter_Db::getTableNameFromBuilder($builder);
		$foreignFieldName = 'dynamic_' . $tableName . '_' . $builder->getPrimaryKeyField()->name;
		$primaryKey = $model->getPrimaryKey();
		$tableName .= '_dynamic';
		
		foreach (get_object_vars($model) as $name => $value) {
			if (!$fullDynamic && ($builder->hasField($name) || $builder->hasReference($name))) {
				continue;
			}
			
			$data = array(
				$foreignFieldName => $primaryKey,
				'dynamic_field_name' => $name,
				'dynamic_field_value' => $value
			);
			
			if ($isNew) {
				$db->insert($tableName, $data);
			} else {
				$db->set($tableName, $data, $foreignFieldName);
			}
		}
	}
	
	/**
	 * Deletes a model
	 *
	 * @param 	Atomik_Model $model
	 * @return 	bool
	 */
	public function delete(Atomik_Model $model)
	{
		$builder = $model->getBuilder();
		$primaryKey = $model->getPrimaryKey();
		
		if (!parent::delete($model)) {
			return false;
		}
		
		$db = Atomik_Model_Adapter_Db::getDbInstanceFromBuilder($builder);
		$tableName = Atomik_Model_Adapter_Db::getTableNameFromBuilder($builder);
		$foreignFieldName = 'dynamic_' . $tableName . '_' . $builder->getPrimaryKeyField()->name;
		$tableName .= '_dynamic';
		
		return $db->delete($tableName, array($foreignFieldName => $primaryKey));
	}
}