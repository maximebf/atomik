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

/** Atomik_Db */
require_once 'Atomik/Db.php';

/** Atomik_Model_Collection */
require_once 'Atomik/Model/Collection.php';

/** Atomik_Model_EventListener */
require_once 'Atomik/Model/EventListener.php';

/** Atomik_Model_Validator */
require_once 'Atomik/Model/Validator.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Session
{
	/** @var Atomik_Db_Instance */
	protected $_dbInstance;
	
	/** @var array of Atomik_Model_EventListener */
	protected $_listeners = array();
	
	/** @var array of Atomik_Model_Session */
	private static $_instances = array();
	
	/**
	 * @param string $name
	 * @return Atomik_Model_Session
	 */
	public static function getInstance($name = 'default')
	{
	    $dbInstance = Atomik_Db::getInstance($name);
	    
	    if (!isset(self::$_instances[$name])) {
	        self::$_instances[$name] = new Atomik_Model_Session($dbInstance);
	    }
	    
		return self::$_instances[$name];
	}
	
	/**
	 * @param Atomik_Db_Instance $instance
	 */
	public function __construct(Atomik_Db_Instance $instance)
	{
		$this->_dbInstance = $instance;
	}
	
	/**
	 * @return Atomik_Db_Instance
	 */
	public function getDbInstance()
	{
		return $this->_dbInstance;
	}
	
	/**
	 * @param Atomik_Model_EventListener $listener
	 */
	public function addListener(Atomik_Model_EventListener $listener)
	{
	    $this->_listeners[] = $listener;
	}
	
	/**
	 * @param Atomik_Model_Descriptor $descriptor
	 * @param string $event
	 * @param array $args
	 */
	public function notify($event, Atomik_Model_Descriptor $descriptor)
	{
	    $args = func_get_args();
	    array_shift($args);
	    
		foreach ($this->_listeners as $listener) {
			call_user_func_array(array($listener, $event), $args);
		}
	}
	
	/**
	 * @param Atomik_Model_Query $query
	 * @return  Atomik_Model_Collection
	 */
	public function executeQuery(Atomik_Model_Query $query)
	{
	    $info = $query->toArray();
	    $descriptor = $info['from'];
	    $dbQuery = $query->getDbQuery($this->_dbInstance);
	    
		$this->notify('BeforeQuery', $descriptor, $dbQuery);
		
		if (($result = $this->_dbInstance->query($dbQuery)) === false) {
			return new Atomik_Model_Collection($descriptor, array());
		}
		
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$collection = new Atomik_Model_Collection($descriptor, $result);
		
		$this->notify('AfterQuery', $descriptor, $collection);
		
		return $collection;
	}
	
	/**
	 * @param Atomik_Model $model
	 * @return bool
	 */
	public function isValid(Atomik_Model $model)
	{
		$descriptor = $model->getDescriptor();
		
		foreach ($descriptor->getFields() as $field) {
		    $value = $model->_get($field->getName());
		    if (!$field->isValid($value)) {
		        return false;
		    }
		}
	    
	    return true;
	}
	
	/**
	 * Saves a model
	 *
	 * @param 	Atomik_Model $model
	 * @return 	bool
	 */
	public function save(Atomik_Model $model, $validate = true)
	{
		$descriptor = $model->getDescriptor();
		$success = true;
		
	    if ($validate && !$this->isValid($model)) {
	        throw new Atomik_Model_Exception("'{$descriptor->getName()}' failed to validate");
	    }
	    
		$this->notify('BeforeSave', $descriptor, $model);
		
		$data = array();
		foreach ($descriptor->getFields() as $field) {
			$data[$field->getColumnName()] = $field->getType()->filterOutput(
			                                $model->_get($field->getName()));
		}
		
		if ($model->isNew()) {
			// insert
			if (($id = $this->_dbInstance->insert($descriptor->getTableName(), $data)) === false) {
				$success = false;
			} else {
				$model->setPrimaryKey($id);
			}
		} else {
			// update
			$where = array($descriptor->getPrimaryKeyField()->getColumnName() => $model->getPrimaryKey());
			$success = $this->_dbInstance->update($descriptor->getTableName(), $data, $where);
		}
		
		if (!$success) {
			$this->notify('FailSave', $descriptor, $model);
			return false;
		}
		$this->notify('AfterSave', $descriptor, $model);
		return true;
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
		
		$descriptor = $model->getDescriptor();
		$this->notify('BeforeDelete', $descriptor, $model);
		
		$where = array($descriptor->getPrimaryKeyField()->getColumnName() => $model->getPrimaryKey());
		if ($this->_dbInstance->delete($descriptor->getTableName(), $where) === false) {
			$this->notify('FailDelete', $descriptor, $model);
			return false;
		}
		$this->notify('AfterDelete', $descriptor, $model);
		return true;
	}
}