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
 * @subpackage Form
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/** Atomik_Form_Field_Interface */
require_once 'Atomik/Form/Field/Interface.php';

/** Atomik_Form_Field_Abstract */
require_once 'Atomik/Form/Field/Abstract.php';

/**
 * @package Atomik
 * @subpackage Form
 */
abstract class Atomik_Form_Fieldset extends Atomik_Form_Field_Abstract
{
	/**
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * @var array
	 */
	protected $_labels = array();
	
	/**
	 * @var array
	 */
	protected $_data;
	
	/**
	 * Resets all fields
	 * 
	 * @param	array $fields
	 */
	public function setFields($fields)
	{
		$this->_fields = array();
		foreach ($fields as $label => $field) {
			$this->addField($field, is_int($label) ? null : $label);
		}
	}
	
	/**
	 * Adds a new field
	 * 
	 * @param	Atomik_Form_Field_Interface	$field
	 * @param	string						$label
	 */
	public function addField(Atomik_Form_Field_Interface $field, $label = null)
	{
		$this->_fields[$field->getName()] = $field;
		$this->_labels[$field->getName()] = $label === null ? $field->getName() : $label;
		$field->setParent($this);
		
		if (isset($this->_data[$field->getName()])) {
			$field->setValue($this->_data[$field->getName()]);
		}
	}
	
	/**
	 * Removes a field
	 * 
	 * @param	string|Atomik_Form_Field_Interface	$field
	 */
	public function removeField($field)
	{
		if ($field instanceof Atomik_Form_Field_Interface) {
			$field = $field->getName();
		}
		
		if (!isset($this->_fields[$field])) {
			return;
		}
		
		$this->_fields[$field]->setParent(null);
		unset($this->_fields[$field]);
		unset($this->_labels[$field]);
	}
	
	/**
	 * Returns a field object
	 * 
	 * @param $name
	 * @return Atomik_Form_Field_Interface
	 */
	public function getField($name)
	{
		if (!isset($this->_fields[$name])) {
			return null;
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
	 * Sets a label associated to a field
	 * 
	 * @param 	string	$fieldName
	 * @param 	string	$label
	 */
	public function setLabel($fieldName, $label)
	{
		$this->_labels[$fieldName] = $label;
	}
	
	/**
	 * Returns the label of a specified field
	 * 
	 * @param 	string|Atomik_Form_Field_Interface $fieldName
	 * @return	string
	 */
	public function getLabel($fieldName)
	{
		if ($fieldName instanceof Atomik_Form_Field_Interface) {
			$fieldName = $fieldName->getName();
		}
		
		if (!isset($this->_labels[$fieldName])) {
			return null;
		}
		return $this->_labels[$fieldName];
	}
	
	/**
	 * Returns all labels
	 * 
	 * @return array
	 */
	public function getLabels()
	{
		return $this->_labels;
	}
	
	/**
	 * Sets the value of the field
	 * 
	 * @param	string	$value
	 */
	public function setValue($value)
	{
		$this->setData($value);
	}
	
	/**
	 * Returns the value of the field
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return $this->getData();
	}
	
	/**
	 * Sets the data for the fields
	 * 
	 * @param array $data
	 */
	public function setData($data)
	{
		$this->_data = $data;
		
		foreach ($data as $key => $value) {
			if (!isset($this->_fields[$key])) {
				continue;
			}
			$this->_fields[$key]->setValue($value);
		}
	}
	
	/**
	 * Empties the data
	 */
	public function clearData()
	{
		$this->_data = array();
		foreach ($this->_fields as $field) {
			$field->setValue('');
		}
	}
	
	/**
	 * Checks if there is data available
	 *
	 * @return bool
	 */
	public function hasData()
	{
		return !empty($this->_data);
	}
	
	/**
	 * Gets the data
	 *
	 * @return array
	 */
	public function getData()
	{
		$data = array();
		foreach ($this->_fields as $field) {
			$data[$field->getName()] = $field->getValue();
		}
		return $data;
	}
	
	/**
	 * Gets the raw data
	 *
	 * @return array
	 */
	public function getRawData()
	{
		return $this->_data;
	}
	
	/**
	 * Checks if the fields have valid data
	 *
	 * @return bool
	 */
	public function isValid()
	{
		$this->_validationMessages = array();
		$valid = true;
		
		foreach ($this->_fields as $field) {
			if (!$field->isValid()) {
				$valid = false;
				$this->_validationMessages = array_merge(
					$this->_validationMessages, 
					$field->getValidationMessages()
				);
			}
		}
		
		return $valid;
	}
}