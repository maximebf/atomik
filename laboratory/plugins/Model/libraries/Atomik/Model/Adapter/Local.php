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
 * Stores models in an array
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Adapter_Local implements Atomik_Model_Adapter_Interface
{
	/**
	 * Models store
	 *
	 * @var array
	 */
	protected static $_models = array();
	
	/**
	 * Not supported on this adapter
	 */
	public function query(Atomik_Model_Builder $builder, $query)
	{
		return array();
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
		$models = array();
		if (!isset(self::$_models[$builder->getName()])) {
			return $models;
		}
		
		foreach (self::$_models[$builder->getName()] as $model) {
			$match = true;
			if ($where !== null) {
				foreach ($where as $key => $value) {
					if (!isset($model->{$key}) || $model->{$key} != $value) {
						$match = false;
						break;
					}
				}
			}
			if ($match) {
				$models[] = $model;
			}
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
		$models = $this->findAll($builder, $where, $orderBy, $limit);
		if (count($models)) {
			return $models[0];
		}
		return null;
	}
	
	/**
	 * Saves a model
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function save(Atomik_Model $model)
	{
		if (!$model->isNew()) {
			return true;
		}
		
		$name = $model->getBuilder()->getName();
		if (!isset(self::$_models[$name])) {
			self::$_models[$name] = array();
		}
		
		$model->setPrimaryKey(count(self::$_models[$name]));
		self::$_models[$name] = $model;
	}
	
	/**
	 * Deletes a model
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function delete(Atomik_Model $model)
	{
		$name = $model->getBuilder()->getName();
		if (isset(self::$_models[$name][$model->getPrimaryKey()])) {
			unset(self::$_models[$name][$model->getPrimaryKey()]);
			return true;
		}
		return false;
	}
}