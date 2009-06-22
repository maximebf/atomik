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

/** Atomik_Model_Behaviour_Abstract */
require_once 'Atomik/Model/Behaviour/Abstract.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Behaviour_Extendable extends Atomik_Model_Behaviour_Abstract
{
	public function setupSqlGeneration()
	{
		// TODO
		
		$tpl = "CREATE TABLE %s_dynamic (\n\tdynamic_id %s,\n\tdynamic_%s INT NOT NULL,\n\t"
			 . "dynamic_field_name VARCHAR(100) NOT NULL,\n\t"
			 . "dynamic_field_value TEXT NOT NULL\n);\n"
			 . "CREATE INDEX idx_%s_dynamic_%s ON %s (%s);\n";
			 
		/*$tableName = $this->_builder->tableName;
		$primaryKeySpec = Atomik_Model_Adapter_Db::getPrimaryKeySpecFromBuilder($builder);
		$foreignFieldName = $tableName . '_' . $builder->getPrimaryKeyField()->name;
		$sql .= sprintf($tpl, $tableName, $primaryKeySpec, $foreignFieldName, $tableName, $foreignFieldName, $tableName, $foreignFieldName);*/
		
		return null;
	}
	
	public function beforeQuery(Atomik_Db_Query $query)
	{
		$tableName = $this->_builder->tableName;
		$primaryKeyName = $this->_builder->getPrimaryKeyField()->name;
		$foreignFieldName = 'dynamic_' . $tableName . '_' . $primaryKeyName;
		$on = sprintf('%s_dynamic.%s = %s.%s', $tableName, $foreignFieldName, $tableName, $primaryKeyName);
		
		// add a join clause to the query
		$query->join($tableName . '_dynamic', $on, null, 'LEFT');
	}
	
	public function afterQuery(Atomik_Model_Modelset $modelSet)
	{
		$primaryKeyName = $this->_builder->getPrimaryKeyField()->name;
		$rows = array();
		
		// parses the results to rebuild the row with the dynamic fields
		foreach ($modelSet as $row) {
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
		
		$modelSet->setData(array_values($rows));
	}
	
	public function afterSave(Atomik_Model $model)
	{
		$db = $this->_builder->getManager()->getDbInstance();
		$fullDynamic = $this->_builder->getOption('dynamic', false);
		
		$tableName = $this->_builder->tableName;
		$foreignFieldName = 'dynamic_' . $tableName . '_' . $this->_builder->getPrimaryKeyField()->name;
		$primaryKey = $model->getPrimaryKey();
		$tableName .= '_dynamic';
		
		foreach (get_object_vars($model) as $name => $value) {
			if (!$fullDynamic && ($this->_builder->hasField($name) || $this->_builder->hasReference($name))) {
				continue;
			}
			
			$data = array(
				$foreignFieldName => $primaryKey,
				'dynamic_field_name' => $name,
				'dynamic_field_value' => $value
			);
			
			$db->set($tableName, $data, $foreignFieldName);
		}
	}
	
	public function afterDelete(Atomik_Model $model)
	{
		$primaryKey = $model->getPrimaryKey();
		
		$db = $this->_builder->getManager()->getDbInstance();
		$tableName = $this->_builder->tableName;
		$foreignFieldName = 'dynamic_' . $tableName . '_' . $this->_builder->getPrimaryKeyField()->name;
		$tableName .= '_dynamic';
		
		$db->delete($tableName, array($foreignFieldName => $primaryKey));
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
}