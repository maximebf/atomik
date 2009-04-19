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
 * @subpackage Db
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Db_Instance */
require_once 'Atomik/Db/Instance.php';

/**
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Object implements ArrayAccess
{
	/**
	 * @var Atomik_Db_Instance
	 */
	protected $_instance;
	
	/**
	 * @var string
	 */
	protected $_table;
	
	/**
	 * @var bool
	 */
	protected $_new = true;
	
	/**
	 * @var string
	 */
	protected $_primaryKey;
	
	/**
	 * @var string
	 */
	private static $_primaryKeyTemplate = 'id';
	
	/**
	 * @var array
	 */
	private static $_classNameMap = array();
	
	/**
	 * Creates a new object using the mapped class name
	 * 
	 * @see Atomik_Db_Object::__constructor()
	 * @return Atomik_Db_Object
	 */
	public static function create($table, $data = array(), $new = true, Atomik_Db_Instance $instance = null, $className = null)
	{
		if ($className === null) {
			$className = 'Atomik_Db_Object';
			if (isset(self::$_classNameMap[$table])) {
				$className = self::$_classNameMap[$table];
			}
		}
		
		return new $className($table, $data, $new, $instance);
	}
	
	/**
	 * Specifies which class to use for a specific table
	 * 
	 * @param 	string	$table
	 * @param 	string	$className
	 */
	public static function setClassForTable($table, $className)
	{
		self::$_classNameMap[$table] = $className;
	}
	
	/**
	 * Sets the primary key template
	 * 
	 * The template can contain %s which will be replaced by the table name
	 * 
	 * @param string $template
	 */
	public static function setPrimaryKeyTemplate($template)
	{
		self::$_primaryKeyTemplate = $template;
	}
	
	/**
	 * Returns the primary key template
	 * 
	 * @return string
	 */
	public static function getPrimaryKeyTemplate()
	{
		return self::$_primaryKeyTemplate;
	}
	
	/**
	 * Constructor
	 * 
	 * @param 	string				$table
	 * @param 	array				$data
	 * @param 	bool				$new
	 * @param 	Atomik_Db_Instance	$instance
	 */
	public function __construct($table, $data = array(), $new = true, Atomik_Db_Instance $instance = null)
	{
		$this->setInstance($instance);
		
		$this->_table = $table;
		$this->_new = $new;
		$this->_primaryKey = $this->_computePrimaryKeyTemplate($table);
		
		foreach ($data as $key => $value) {
			$this->{$key} = $value;
		}
	}
	
	/**
	 * Returns the primary key field for the specified table according to the template
	 * 
	 * @param 	string	$table
	 * @return 	string
	 */
	protected function _computePrimaryKeyTemplate($table)
	{
		return str_replace('%s', $table, self::getPrimaryKeyTemplate());
	}
	
	/**
	 * Sets the associated db instance
	 * 
	 * @param Atomik_Db_Instance $instance
	 */
	public function setInstance(Atomik_Db_Instance $instance = null)
	{
		if ($instance === null) {
			require_once 'Atomik/Db.php';
			$instance = Atomik_Db::getInstance();
		}
		$this->_instance = $instance;
	}
	
	/**
	 * Returns the associated db instance
	 * 
	 * @return Atomik_Db_Instance
	 */
	public function getInstance()
	{
		if ($this->_instance === null) {
			$this->setInstance();
		}
		return $this->_instance;
	}
	
	/**
	 * Returns the primary key value
	 * 
	 * @return mixed
	 */
	public function getPrimaryKey()
	{
		return $this->{$this->_primaryKey};
	}
	
	/**
	 * Magic methods to handle relationships between tables.
	 * 
	 * Available methods (replace Table by the table name, must start with a capital):
	 * - findTable($foreignKey)
	 * - findManyTable($foreignKey)
	 * - findParentTable($localKey)
	 * 
	 * @param	string	$method
	 * @param 	array	$args
	 * @return 	mixed
	 */
	public function __call($method, $args)
	{
		if (!preg_match('/^find(All|Parent|)(.+)$/', $method, $matches)) {
			return null;
		}
		
		$type = $matches[1];
		$table = strtolower($matches[2]);
		
		if ($type == 'Parent') {
			$foreignPrimaryKey = $this->_computePrimaryKeyTemplate($table);
			$where = array($foreignPrimaryKey => $this->{$args[0]});
		} else {
			$where = array($args[0] => $this->getPrimaryKey());
		}

		$rows = $this->_instance->findAll($table, $where);
		if ($rows === false) {
			return false;
		}
		$rows->setFetchMode(Atomik_Db_Query_Result::FETCH_OBJECT);
		
		if ($type == 'All') {
			return $rows;
		}
		
		$row = $rows->fetch();
		$rows->closeCursor();
		return $row;
	}
	
	/**
	 * Saves the current object by either performing an insert or an update
	 * 
	 * @return bool Success
	 */
	public function save()
	{
		if ($this->_new) {
			if (($primaryKey = $this->_instance->insert($this->_table, $this->toArray())) === false) {
				return false;
			}
			$this->{$this->_primaryKey} = $primaryKey;
			$this->_new = false;
			return true;
		}
		
		$where = array($this->_primaryKey => $this->getPrimaryKey());
		return $this->_instance->update($this->_table, $this->toArray(), $where);
	}
	
	/**
	 * Deletes the current object. Will also reset the primary key.
	 * 
	 * @return bool Success
	 */
	public function delete()
	{
		$where = array($this->_primaryKey => $this->getPrimaryKey());
		if ($this->_instance->delete($this->_table, $where)) {
			$this->{$this->_primaryKey} = null;
			$this->_new = true;
			return true;
		}
		return false;
	}
	
	/**
	 * Returns an Atomik_Form object. Can be used only if inherited.
	 * 
	 * Form fields will be added for each properties. Form fields options
	 * must use the form- prefix
	 * 
	 * @return Atomik_Form
	 */
	public function getForm()
	{
		require_once 'Atomik/Form/Class.php';
		return Atomik_Form_Class::create(get_class($this), 'form-');
	}
	
	/**
	 * Returns the data as an array
	 * 
	 * @return array
	 */
	public function toArray()
	{
		$data = array();
		$filter = array('_instance', '_table', '_new', '_primaryKey');
		
		foreach (get_object_vars($this) as $key => $value) {
			if (!in_array($key, $filter)) {
				$data[$key] = $value;
			}
		}
		
		return $data;
	}
	
	/**
	 * Returns the primary key value
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->getPrimaryKey();
	}
	
	/* -------------------------------------------------------------------------------------------
	 *  ArrayAccess
	 * ------------------------------------------------------------------------------------------ */
	
	public function offsetExists($index)
	{
		return array_key_exists($index, $this->_data);
	}
	
	public function offsetGet($index)
	{
		return $this->_data[$index];
	}
	
	public function offsetSet($index, $value)
	{
		$this->_data[$index] = $value;
	}
	
	public function offsetUnset($index)
	{
		unset($this->_data[$index]);
	}
}