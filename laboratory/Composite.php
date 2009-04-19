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

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Adapter_Composite implements Atomik_Model_Adapter_Interface, Atomik_Db_Script_Model_Exportable 
{
	/**
	 * @var array
	 */
	protected $_adapters = array();
	
	/**
	 * Returns the fields for each adapters
	 * 
	 * @param 	Atomik_Model_Builder	$builder
	 * @return 	array
	 */
	public static function getFieldsByAdapter(Atomik_Model_Builder $builder)
	{
		$composedOf = (array) $builder->getOption('composed-of', array());
		if (count($composedOf) == 0) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Missing composed-of in ' . $builder->name);
		}
		
		$adapters = array();
		foreach ($composedOf as $spec) {
			$parts = explode(' ', trim($spec));
			$adapters[array_shift($parts)] = $parts;
		}
		
		return $adapters;
	}
	
	/**
	 * Returns the data for each adapters
	 * 
	 * @param 	Atomik_Model	$model
	 * @return 	array
	 */
	public static function getDataByAdapterFromModel(Atomik_Model $model)
	{
		$data = array();
		$adapters = self::getFieldsByAdapter($model->getBuilder());
		
		foreach ($adapters as $adapter => $fields) {
			$data[$adapter] = array();
			foreach ($fields as $field) {
				$data[$adapter][$field] = $model->{$field};
			}
		}
		
		return $data;
	}
	
	/**
	 * @param Atomik_Model_Builder $builder
	 * @return string
	 */
	public static function getSqlDefinition(Atomik_Model_Builder $builder)
	{
		$adapters = array_keys(self::getFieldsByAdapter($query->from));
		$sql = '';
		
		foreach ($adapters as $adapter) {
			$adapter = $this->_getAdapter($adapter);
			if ($adapter instanceof Atomik_Db_Script_Model_Exportable) {
				$sql .= call_user_func(array(get_class($adapter), 'getSqlDefinition'), $builder);
			}
		}
		
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
		$adapters = array_keys(self::getFieldsByAdapter($query->from));
		$data = array();
		
		foreach ($adapters as $adapterName) {
			$adapter = $this->_getAdapter($adapterName);
			$data[$adapterName] = $adapter->query($query);
		}
		
		$primaryKeyName = $query->from->getPrimaryKeyField()->name;
		$mergedData = array();
		
		foreach ($data as $adapterName => $adapterData) {
			foreach ($adapterData as $model) {
				$primaryKey = $model[$primaryKeyName];
				if (!isset($mergedData[$primaryKey])) {
					$mergedData[$primaryKey] = $model;
				} else {
					$mergedData[$primaryKey] = array_merge($mergedData[$primaryKey], $model);
				}
			}
		}
		
		return $mergedData;
	}
	
	/**
	 * Saves a model
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function save(Atomik_Model $model)
	{
		$data = self::getDataByAdapterFromModel($model);
	}
	
	/**
	 * Deletes a model
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function delete(Atomik_Model $model)
	{
	}
	
	/**
	 * Returns an adapter
	 * 
	 * @param 	string	$name
	 * @return	Atomik_Model_Adapter_Interface
	 */
	protected function _getAdapter($name)
	{
		if (!isset($this->_adapters[$name])) {
			$this->_adapters[$name] = Atomik_Model_Adapter_Factory::factory($name);
		}
		
		return $this->_adapters[$name];
	}
}