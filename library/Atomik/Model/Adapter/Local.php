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

/**
 * Stores models in an array
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Adapter_Local implements Atomik_Model_Adapter_Interface
{
	/**
	 * @var array
	 */
	protected static $_data = array();
	
	/**
	 * Query the adapter
	 * 
	 * @param	Atomik_Model_Query	$query
	 * @return 	Atomik_Model_Modelset
	 */
	public function query(Atomik_Model_Query $query)
	{
		$builder = $query->from;
		
		if (!isset(self::$_data[$builder->name])) {
			return new Atomik_Model_Modelset($builder, array());
		}
		
		$data = array();
		foreach (self::$_data[$builder->name] as $modelData) {
			$match = true;
			foreach ($query->where as $key => $value) {
				if (!isset($modelData[$key]) || $modelData[$key] != $value) {
					$match = false;
					break;
				}
			}
			if ($match) {
				$data[] = $modelData;
			}
		}
		
		return new Atomik_Model_Modelset($builder, $data);
	}
	
	/**
	 * Saves a model
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function save(Atomik_Model $model)
	{
		$name = $model->getBuilder()->name;
		if (!isset(self::$_data[$name])) {
			self::$_data[$name] = array();
		}
		
		if ($model->isNew()) {
			$model->setPrimaryKey(count(self::$_data[$name]));
			self::$_data[$name][] = $model->toArray();
			
		} else {
			if (!isset(self::$_data[$name][$model->getPrimaryKey()])) {
				return false;
			}
			self::$_data[$name][$model->getPrimaryKey()] = $model->toArray();
		}
	}
	
	/**
	 * Deletes a model
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function delete(Atomik_Model $model)
	{
		$name = $model->getBuilder()->name;
		if (isset(self::$_models[$name][$model->getPrimaryKey()])) {
			unset(self::$_models[$name][$model->getPrimaryKey()]);
			return true;
		}
		return false;
	}
}