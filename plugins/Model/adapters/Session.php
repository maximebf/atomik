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

/** LocalModelAdapter */
require_once dirname(__FILE__) . '/Local.php';

/**
 * Stores models in the session
 * 
 * @package Atomik
 * @subpackage Model
 */
class SessionModelAdapter extends LocalModelAdapter
{
	/**
	 * Singleton instance
	 *
	 * @var SessionModelAdapter
	 */
	protected static $_instance;
	
	/**
	 * Gets the singleton
	 *
	 * @return SessionModelAdapter
	 */
	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		/* bind the _models property to the session array */
		if (!isset($_SESSION['__MODELS'])) {
			$_SESSION['__MODELS'] = array();
		}
		$this->_models = &$_SESSION['__MODELS'];
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
		if (!isset($this->_models[$builder->getName()])) {
			return $models;
		}
		
		foreach ($this->_models[$builder->getName()] as $model) {
			$match = true;
			if ($where !== null) {
				foreach ($where as $key => $value) {
					if (!isset($model[$key]) || $model[$key] != $value) {
						$match = false;
						break;
					}
				}
			}
			if ($match) {
				$models[] = $builder->createInstance($model, false);
			}
		}
		
		return $models;
	}
	
	/**
	 * Saves a model
	 *
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function save(Atomik_Model $model)
	{
		$name = $model->getBuilder()->getName();
		if (!isset($this->_models[$name])) {
			$this->_models[$name] = array();
		}
		
		/* data saved as array to avoid any serialization error */
		if ($model->isNew()) {
			$model->id = count($this->_models[$name]);
			$this->_models[$name][] = $model->toArray();
		} else {
			if (!isset($this->_models[$name][$model->id])) {
				return false;
			}
			$this->_models[$name][$model->id] = $model->toArray();
		}
		
		return true;
	}
}