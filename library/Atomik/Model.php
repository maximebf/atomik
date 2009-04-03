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

/** Atomik_Model_Locator */
require_once 'Atomik/Model/Locator.php';

/** Atomik_Model_Builder */
require_once 'Atomik/Model/Builder.php';

/** Atomik_Model_Builder_Factory */
require_once 'Atomik/Model/Builder/Factory.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model extends Atomik_Model_Locator
{
	/**
	 * @var Atomik_Model_Builder
	 */
	protected $_builder;
	
	/**
	 * @var bool
	 */
	protected $_new = true;
	
	/**
	 * @var array
	 */
	protected $_references = array();
	
	/**
	 * @var array
	 */
	protected $_validationMessages = array();
	
	/**
	 * Constructor
	 *
	 * @param 	array 					$data
	 * @param 	bool 					$new	Whether the model is already saved or not
	 * @param	Atomik_Model_Builder	$builder
	 */
	public function __construct($data = array(), $new = true, Atomik_Model_Builder $builder = null)
	{
		$this->_builder = $builder;
		$this->_new = $new;
		$this->populate($data);
		
		if ($this->getBuilder()->getOption('no-lazy-loading', false)) {
			$this->initReferences();
		}
	}
	
	/**
	 * Returns the builder associated to this model
	 * 
	 * @return Atomik_Model_Builder
	 */
	public function getBuilder()
	{
		if ($this->_builder === null) {
			$this->_builder = Atomik_Model_Builder_Factory::get($this);
		}
		return $this->_builder;
	}
	
	/**
	 * Checks if the model has never been saved
	 *
	 * @return bool
	 */
	public function isNew()
	{
		return $this->_new;
	}
	
	/**
	 * Sets the primary key value
	 *
	 * @param mixed $value
	 */
	public function setPrimaryKey($value)
	{
		$this->{$this->getBuilder()->getPrimaryKeyField()->name} = $value;
	}
	
	/**
	 * Returns the primary key value
	 * 
	 * @return mixed
	 */
	public function getPrimaryKey()
	{
		return $this->{$this->getBuilder()->getPrimaryKeyField()->name};
	}
	
	/**
	 * Sets multiple fields value using an array (cannot set references)
	 *
	 * @param array $data
	 */
	public function populate($data)
	{
		$fields = $this->getBuilder()->getFields();
		foreach ($fields as $field) {
			if (array_key_exists($field->name, $data)) {
				$this->{$field->name} = $data[$field->name];
			}
		}
	}
	
	/**
	 * Initializes all references
	 */
	public function initReferences()
	{
		foreach ($this->getBuilder()->getReferences() as $ref) {
			$this->initReference($ref->name);
		}
	}
	
	/**
	 * Initializes the specified reference
	 * 
	 * @param	string	$name
	 * @return 	Atomik_Model_Builder_Reference
	 */
	public function initReference($name)
	{
		if (($reference = $this->getBuilder()->getReference($name)) === false) {
			throw new Atomik_Model_Exception('Reference ' . $name . ' does not exist');
		}
		
		$query = $reference->getQuery($this);
		
		if ($reference->isHasMany()) {
			$modelSet = Atomik_Model_Locator::query($reference->target, $query);
			$this->_references[$name] = new Atomik_Model_ReferenceArray($this, $reference, $modelSet);
			return $reference;
		}
		
		$query->limit(1, $offset);
		$modelSet = Atomik_Model_Locator::query($builder, $query);
		if (count($modelSet) == 0) {
			$this->_references[$name] = null;
		}
		$this->_references[$name] = $modelSet[0]; 
		
		return $reference;
	}
	
	/**
	 * Sets the value of a reference
	 * 
	 * @param	string				$name
	 * @param 	Atomik_Model|array	$value
	 */
	public function setReference($name, $value)
	{
		$reference = $this->initReference($name);
		
		if ($reference->isHasMany()) {
			$this->_references[$name]->clear($value);
			return;
		}
		
		if (isset($this->_references[$name])) {
			$this->_references[$name]->{$reference->targetField} = null;
			$this->_references[$name]->save();
		}
		
		$value->{$reference->targetField} = $this->{$reference->sourceField};
		$this->_references[$name] = $value;
	}
	
	/**
	 * Returns the value of the specified reference
	 * 
	 * @param	string	$name	Reference name
	 * @return 	Atomik_Model|Atomik_Model_Reference
	 */
	public function getReference($name)
	{
		if (!isset($this->_references[$name])) {
			$this->initReference($name);
		}
		return $this->_references[$name];
	}
	
	/**
	 * Sets a field or a reference
	 * 
	 * @param 	string	$name
	 * @param	mixed	$value
	 */
	public function __set($name, $value)
	{
		if ($this->getBuilder()->hasReference($name)) {
			return $this->setReference($name, $value);
		}
		$this->{$name} = $value;
	}
	
	/**
	 * Returns a field or a reference
	 * 
	 * @param 	string $name
	 * @return 	mixed
	 */
	public function __get($name)
	{
		if ($this->getBuilder()->hasReference($name)) {
			return $this->getReference($name);
		}
		
		if (property_exists($this, $name)) {
			return $this->{$name};
		} else if ($this->getBuilder()->hasField($name)) {
			return null;
		}
		
		throw new Atomik_Model_Exception('Field does not exist: ' . $name);
	}
	
	/**
	 * Saves the data using the adapter
	 *
	 * @return bool Success
	 */
	public function save()
	{
		if ($this->getBuilder()->getOption('validate-on-save', false)) {
			if (!$this->isValid()) {
				require_once 'Atomik/Model/Exception.php';
				throw new Atomik_Model_Exception('Model failed to validate before saving:<br/>' . 
					implode('<br/>', $this->getValidationMessages()));
			}
		}
		
		if (!$this->getBuilder()->getAdapter()->save($this)) {
			return false;
		}
		
		// checks if cascade is enabled
		if ($this->getBuilder()->getOption('cascade-save', false)) {
			foreach ($this->getBuilder()->getReferences() as $reference) {
				$this->initReference($reference->name);
				$this->_references[$reference->name]->save();
			}
		}
		
		return true;
	}
	
	/**
	 * Deletes the model from the data source
	 *
	 * @return bool Success
	 */
	public function delete()
	{
		if (!$this->getBuilder()->getAdapter()->delete($this)) {
			return false;
		}
		
		// checks if cascade is enabled
		if ($this->getBuilder()->getOption('cascade-delete', false)) {
			foreach ($this->getBuilder()->getReferences() as $reference) {
				$this->initReference($reference->name);
				$this->_references[$reference->name]->delete();
			}
			$this->_references = array();
		}
		
		$this->_new = true;
		return true;
	}
	
	/**
	 * Validates the model's fields value
	 *
	 * @return bool
	 */
	public function isValid()
	{
		$isValid = true;
		$this->_validationMessages = array();
		$fields = $this->getBuilder()->getFields();
		
		foreach ($fields as $field) {
			if (isset($data[$field->name])) {
				if (!$field->isValid($data[$field->name])) {
					$this->_validationMessages += $field->getValidationMessages();
					$isValid = false;
				}
			} else if ($field->getOption('required', false)) {
				$this->_validationMessages[] = 'Missing required field: ' . $field->name;
				$isValid = false;
			}
		}
		
		return $isValid;
	}
	
	/**
	 * Returns the messages generated during the validation
	 *
	 * @return array
	 */
	public function getValidationMessages()
	{
		return $this->getBuilder()->getValidationMessages();
	}
	
	/**
	 * Returns the data as an array (references won't be included)
	 *
	 * @return array
	 */
	public function toArray()
	{
		$data = array();
		$fields = $this->getBuilder()->getFields();
		
		foreach ($fields as $field) {
			$data[$field->name] = $this->{$field->name};
		}
		
		return $data;
	}
	
	/**
	 * Returns a form for this model
	 *
	 * @return Atomik_Model_Form
	 */
	public function getForm()
	{
		require_once 'Atomik/Model/Form.php';
		return new Atomik_Model_Form($this);
	}
	
	/**
	 * Drops the primary key and sets the model as new
	 */
	public function __clone()
	{
		$this->_new = true;
		$this->setPrimaryKey(null);
	}
}