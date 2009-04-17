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

/** Atomik_Form_Field_Abstract */
require_once 'Atomik/Form/Field/Abstract.php';

/**
 * @package Atomik
 * @subpackage Form
 */
class Atomik_Form_Fieldset
{
	/**
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * @var array
	 */
	protected $_data;
	
	/**
	 * @var array
	 */
	protected $_validationMessages = array();
	
	/**
	 * Resets all fields
	 * 
	 * @param	array $fields
	 */
	public function setFields($fields)
	{
		$this->_fields = array();
		foreach ($fields as $field) {
			$this->addField($field);
		}
	}
	
	/**
	 * Adds a new field
	 * 
	 * @param	Atomik_Form_Field_Abstract	$field
	 */
	public function addField(Atomik_Form_Field_Abstract $field)
	{
		$this->_fields[$field->getName()] = $field;
		
		if (isset($this->_data[$field->getName()])) {
			$field->setValue($this->_data[$field->getName()]);
		}
	}
	
	/**
	 * Removes a field
	 * 
	 * @param	Atomik_Form_Field_Abstract	$field
	 */
	public function removeField(Atomik_Form_Field_Abstract $field)
	{
		for ($i = 0, $c = count($this->_fields); $i < $c; $i++) {
			if ($this->_fields[$i] == $field) {
				unset($this->_fields[$i]);
				break;
			}
		}
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
	 * @param array $data OPTIONAL
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
	
	/**
	 * Returns the messages generated during the last validation
	 *
	 * @return array
	 */
	public function getValidationMessages()
	{
		return $this->_validationMessages;
	}
}