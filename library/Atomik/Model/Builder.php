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

/** Atomik_Model_Options */
require_once 'Atomik/Model/Options.php';

/** Atomik_Model_Adapter_Interface */
require_once 'Atomik/Model/Adapter/Interface.php';

/** Atomik_Model_Builder_Field */
require_once 'Atomik/Model/Builder/Field.php';

/** Atomik_Model_Builder_Reference */
require_once 'Atomik/Model/Builder/Reference.php';

/**
 * A model builder can be used to programatically build models
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Builder extends Atomik_Model_Options
{
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $className;
	
	/**
	 * @var Atomik_Model_Builder
	 */
	protected $_parentModelBuilder;
	
	/**
	 * @var array
	 */
	protected $_adapter;
	
	/**
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * @var Atomik_Model_Field_Abstract
	 */
	protected $_primaryKeyField;
	
	/**
	 * @var array
	 */
	protected $_references = array();
	
	/**
	 * @var Atomik_Model_Adapter_Interface
	 */
	protected static $_defaultAdapter;
	
	/**
	 * Sets the default adapter 
	 *
	 * @param Atomik_Model_Adapter_Interface $adapter
	 */
	public static function setDefaultAdapter(Atomik_Model_Adapter_Interface $adapter = null)
	{
		if ($adapter === null) {
			require_once 'Atomik/Model/Adapter/Local.php';
			$adapter = new Atomik_Model_Adapter_Local();
		}
		self::$_defaultAdapter = $adapter;
	}
	
	/**
	 * Returns the default adapter
	 *
	 * @return Atomik_Model_Adapter_Interface
	 */
	public static function getDefaultAdapter()
	{
		if (self::$_defaultAdapter === null) {
			self::setDefaultAdapter();
		}
		return self::$_defaultAdapter;
	}
	
	/**
	 * Constructor
	 *
	 * @param 	string 	$name
	 * @param 	array 	$metadata
	 */
	public function __construct($name, $className = null)
	{
		$this->name = $name;
		$this->className = $className;
	}
	
	/**
	 * Sets the parent model
	 * 
	 * @param	string|Atomik_Model_Builder	$parentModel
	 */
	public function setParentModel($parentModel)
	{
		$builder = Atomik_Model_Builder_Factory::get($parentModel);
		
		if ($this->_adapter !== null) {
			throw new Atomik_Model_Builder_Exception('Inherited model can\'t have a different adapter');
		}
		
		$this->_adapter = $builder->getAdapter();
		$this->_options = array_merge($builder->getOptions(), $this->_options);
		$this->_parentModelBuilder = $builder;
	}
	
	/**
	 * Checks if it as a parent
	 * 
	 * @return bool
	 */
	public function hasParentModel()
	{
		return $this->_parentModelBuilder !== null;
	}
	
	/**
	 * Returns the parent model builder or null
	 * 
	 * @return Atomik_Model_Builder
	 */
	public function getParentModel()
	{
		return $this->_parentModelBuilder;
	}
	
	/**
	 * Sets the adapter
	 *
	 * @param Atomik_Model_Adapter_Interface $adapter
	 */
	public function setAdapter(Atomik_Model_Adapter_Interface $adapter = null)
	{
		if ($adapter === null) {
			if ($this->_parentModelBuilder !== null) {
				throw new Atomik_Model_Builder_Exception('Inherited model can\'t have a different adapter');
			}
			$adapter = self::getDefaultAdapter();
		}
		$this->_adapter = $adapter;
	}
	
	/**
	 * Returns the adapter associated to the model
	 *
	 * @return Atomik_Model_Adapter_Interface
	 */
	public function getAdapter()
	{
		if ($this->_adapter === null) {
			$this->setAdapter();
		}
		return $this->_adapter;
	}
	
	/**
	 * Resets all the fields
	 *
	 * @param array $fields
	 */
	public function setFields($fields = array())
	{
		$this->_fields = array();
		foreach ($fields as $field) {
			$this->addField($field);
		}
	}
	
	/**
	 * Adds a new field
	 *
	 * @param Atomik_Model_Builder_Field $field
	 */
	public function addField(Atomik_Model_Builder_Field $field)
	{
		$this->_fields[$field->name] = $field;
		
		if ($field->getOption('primary-key', false)) {
			$this->setPrimaryKeyField($field);
		}
	}
	
	/**
	 * Checks if a field exists
	 *
	 * @param 	string $name
	 * @return 	bool
	 */
	public function hasField($name)
	{
		return isset($this->_fields[(string) $name]);
	}
	
	/**
	 * Returns a field object
	 *
	 * @param 	string 	$name
	 * @return 	Atomik_Model_Field_Abstract|bool	False if the field does not exist
	 */
	public function getField($name)
	{
		if (!isset($this->_fields[$name])) {
			return false;
		}
		return $this->_fields[$name];
	}
	
	/**
	 * Returns all fields
	 *
	 * @return array
	 */
	public function getFields()
	{
		return $this->_fields;
	}
	
	/**
	 * Sets the primary key field
	 * 
	 * If $field is null, will use or create a field named id
	 * 
	 * @param Atomik_Model_Builder_Field $field
	 */
	public function setPrimaryKeyField(Atomik_Model_Builder_Field $field = null)
	{
		if ($field === null) {
			if ($this->_primaryKeyField !== null) {
				// primary key already defined
				return;
			}
			
			// checks if there is a field named id
			if (($field = $this->getField('id')) === false) {
				// creates a new id field
				$field = new Atomik_Model_Builder_Field('id', array('form-ignore' => true));
				$this->addField($field);
			}
		}
		
		$this->_primaryKeyField = $field;
	}
	
	/**
	 * Returns the field used as the primary key.
	 *
	 * @return Atomik_Model_Field_Abstract
	 */
	public function getPrimaryKeyField()
	{
		if ($this->_primaryKeyField === null) {
			$this->setPrimaryKeyField();
		}
		return $this->_primaryKeyField;
	}
	
	/**
	 * Resets all the references
	 *
	 * @param array $references
	 */
	public function setReferences($references = array())
	{
		$this->_references = array();
		foreach ($references as $reference) {
			$this->addReference($reference);
		}
	}
	
	/**
	 * Adds a new reference
	 * 
	 * @param	Atomik_Model_Builder_Reference	$reference
	 */
	public function addReference(Atomik_Model_Builder_Reference $reference)
	{
		$reference->source = $this->name;
		if (!$this->hasField($reference->sourceField)) {
			$this->addField(new Atomik_Model_Builder_Field($reference->sourceField, array('form-ignore' => true)));
		}
		$this->_references[$reference->name] = $reference;
	}
	
	/**
	 * Checks if a reference exists
	 * 
	 * @param	string	$name
	 * @return 	bool
	 */
	public function hasReference($name)
	{
		return isset($this->_references[$name]);
	}
	
	/**
	 * Returns a reference object
	 *
	 * @param 	string 		$name 
	 * @return 	Atomik_Model_Builder_Reference|bool 	False if not found
	 */
	public function getReference($name)
	{
		if (!isset($this->_references[$name])) {
			return false;
		}
		return $this->_references[$name];
	}
	
	/**
	 * Returns all references or only the one associated to a model
	 *
	 * @param 	string 	$modelName
	 * @param 	string 	$type 		Reference type
	 * @return 	array
	 */
	public function getReferences($modelName = null, $type = null)
	{
		if ($modelName === null) {
			return $this->_references;
		}
		
		$references = array();
		foreach ($this->_references as $reference) {
			if ($reference->isSource($modelName)) {
				if ($type === null || $reference->type == $type) {
					$references[] = $reference;
				}
			}
		}
		return $references;
	}
	
	/**
	 * Creates a model instance
	 *
	 * @see Atomik_Model::__construct()
	 * @param 	array 			$values
	 * @param 	bool 			$new	
	 * @return 	Atomik_Model
	 */
	public function createInstance($values = array(), $new = true)
	{
		$className = $this->className;
		if ($className === null) {
			/** Atomik_Model */
			require_once 'Atomik/Model.php';
			$className = 'Atomik_Model';
		}
		
		$instance = new $className($values, $new, $this);
		return $instance;
	}
	
	/**
	 * Returns a form for this builder
	 *
	 * @return Atomik_Model_Form
	 */
	public function getForm()
	{
		require_once 'Atomik/Model/Form.php';
		return new Atomik_Model_Form($this);
	}
}