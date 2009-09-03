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
	public $keyword = 'extfields';
	
	public function beforeExport(Atomik_Model_Builder $builder, Atomik_Db_Definition $definition)
	{
		$tableName = $builder->tableName;
		$foreignFieldName = $tableName . '_' . $builder->getPrimaryKeyField()->name;
		
		$definition->table($this->_suffix($tableName))
			->column($this->_prefix('id'), 'int')
			->column($this->_prefix($foreignFieldName), 'int')
			->column($this->_prefix('field_name'), 'varchar', 100)
			->column($this->_prefix('field_value'), 'text')
			->primaryKey('id')
			->index($foreignFieldName);
	}
	
	public function beforeQuery(Atomik_Model_Builder $builder, Atomik_Db_Query $query)
	{
		$tableName = $builder->tableName;
		$primaryKeyName = $builder->getPrimaryKeyField()->name;
		$foreignFieldName = $this->_prefix($tableName . '_' . $primaryKeyName);
		$on = sprintf('%s.%s = %s.%s', $this->_suffix($tableName), $foreignFieldName, $tableName, $primaryKeyName);
		
		// add a join clause to the query
		$query->join($this->_suffix($tableName), $on, null, 'LEFT');
	}
	
	public function afterQuery(Atomik_Model_Builder $builder, Atomik_Model_Modelset $modelSet)
	{
		$primaryKeyName = $builder->getPrimaryKeyField()->name;
		$rows = array();
		
		// parses the results to rebuild the row with the dynamic fields
		foreach ($modelSet as $row) {
			$primaryKey = $row[$primaryKeyName];
			if ($row[$this->_prefix('id')] === null) {
				// no dyn field
				$rows[$primaryKey] = $this->_stripDynamicKeysFromRow($row);
				continue;
			}
			if (!isset($rows[$primaryKey])) {
				$rows[$primaryKey] = $this->_stripDynamicKeysFromRow($row);;
			}
			$rows[$primaryKey][$row[$this->_prefix('field_name')]] = $row[$this->_prefix('field_value')];
		}
		
		$modelSet->setData(array_values($rows));
	}
	
	public function afterSave(Atomik_Model_Builder $builder, Atomik_Model $model)
	{
		$db = $builder->getManager()->getDbInstance();
		$fullDynamic = $builder->getOption('dynamic', false);
		
		$tableName = $builder->tableName;
		$foreignFieldName = $this->_prefix($tableName) . '_' . $builder->getPrimaryKeyField()->name;
		$primaryKey = $model->getPrimaryKey();
		$tableName = $this->_suffix($tableName);
		
		foreach (get_object_vars($model) as $name => $value) {
			if (!$fullDynamic && ($builder->hasField($name) || $builder->hasReference($name))) {
				continue;
			}
			
			$data = array(
				$foreignFieldName => $primaryKey,
				$this->_prefix('field_name') => $name,
				$this->_prefix('field_value') => $value
			);
			
			$db->set($tableName, $data, $foreignFieldName);
		}
	}
	
	public function afterDelete(Atomik_Model_Builder $builder, Atomik_Model $model)
	{
		$primaryKey = $model->getPrimaryKey();
		
		$db = $builder->getManager()->getDbInstance();
		$tableName = $builder->tableName;
		$foreignFieldName = $this->_prefix($tableName) . '_' . $builder->getPrimaryKeyField()->name;
		$tableName = $this->_suffix($tableName);
		
		$db->delete($tableName, array($foreignFieldName => $primaryKey));
	}
	
	protected function _prefix($string)
	{
		return $this->keyword . '_' . $string;
	}
	
	protected function _suffix($string)
	{
		return $string . '_' . $this->keyword;
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
			if (substr($key, 0, strlen($this->keyword) + 1) == $this->keyword . '_') {
				continue;
			}
			$strippedRow[$key] = $value;
		}
		return $strippedRow;
	}
}