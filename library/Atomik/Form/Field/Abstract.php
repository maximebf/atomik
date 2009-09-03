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

/** Atomik_Options */
require_once 'Atomik/Options.php';

/** Atomik_Form_Field_Interface */
require_once 'Atomik/Form/Field/Interface.php';

/**
 * @package Atomik
 * @subpackage Form
 */
abstract class Atomik_Form_Field_Abstract extends Atomik_Options implements Atomik_Form_Field_Interface
{
	/**
	 * @var string
	 */
	protected $_name;
	
	/**
	 * @var Atomik_Form_Field_Interface
	 */
	protected $_parent;
	
	/**
	 * @var array
	 */
	protected $_attributes = array();
	
	/**
	 * @var string
	 */
	protected $_value;
	
	/**
	 * @var string
	 */
	protected $_defaultId = true;
	
	/**
	 * @var array
	 */
	protected $_validationMessages = array();
	
	/**
	 * Constructor
	 * 
	 * @param	string	$name
	 * @param	array	$options
	 */
	public function __construct($name, $options = array())
	{
		$this->setName($name);
		$this->setOptions($options);
		$this->_init();
	}
	
	/**
	 * Called after the constructor
	 */
	protected function _init()
	{
		
	}
	
	/**
	 * Sets the name of the field
	 * 
	 * @param	string	$name
	 */
	public function setName($name)
	{
		$this->_name = $name;
		$this->_setDefaultId();
	}
	
	/**
	 * Returns the name of the field
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Returns the full name of the field (including the parent name)
	 * 
	 * @return string
	 */
	public function getFullname()
	{
		if ($this->_parent === null || $this->_parent->getFullname() === null) {
			return $this->_name;
		}
		return $this->_parent->getFullname() . '[' . $this->_name . ']';
	}
	
	/**
	 * Sets the field that contain this field
	 * 
	 * @param 	Atomik_Form_Field_Interface	$parent
	 */
	public function setParent(Atomik_Form_Field_Interface $parent)
	{
		$this->_parent = $parent;
		$this->_setDefaultId();
	}
	
	/**
	 * Returns the parent field
	 * 
	 * @return Atomik_Form_Field_Interface
	 */
	public function getParent()
	{
		return $this->_parent;
	}
	
	/**
	 * Sets the value of the field
	 * 
	 * @param	string	$value
	 */
	public function setValue($value)
	{
		$this->_value = $value;
	}
	
	/**
	 * Returns the value of the field
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return $this->_value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see library/Atomik/Atomik_Options#setOption()
	 */
	public function setOption($name, $value)
	{
		if (substr($name, 0, 5) == 'attr-') {
			return $this->setAttribute(substr($name, 5), $value);
		}
		parent::setOption($name, $value);
	}
	
	/**
	 * Resets the form attributes
	 *
	 * @param array $attributes
	 */
	public function setAttributes($attributes)
	{
		$this->_attributes = array();
		foreach ($attributes as $name => $value) {
			$this->setAttribute($name, $value);
		}
	}
	
	/**
	 * Sets a form attribute
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function setAttribute($name, $value)
	{
		$this->_attributes[$name] = $value;
	}
	
	/**
	 * Returns an attribute value
	 *
	 * @param string $name
	 * @param string $de
	 * @return string
	 */
	public function getAttribute($name, $default = null)
	{
		if (!isset($this->_attributes[$name])) {
			return $default;
		}
		return $this->_attributes[$name];
	}
	
	/**
	 * Returns all the attributes
	 *
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}
	
	/**
	 * Returns all the attributes as an html attributes formatted string
	 *
	 * @param	array	$filter		Filters which attributes to include or exclude
	 * @param	bool	$exclude	Whether to include or exclude attributes from the filter
	 * @return string
	 */
	public function getAttributesAsString($filter = null, $exclude = true)
	{
		return $this->_getArrayAsAttributeString($this->_attributes, $filter, $exclude);
	}
	
	/**
	 * Returns all options as an html attribute string
	 * 
	 * @param	array	$filter		Filters which options to include or exclude
	 * @param	bool	$exclude	Whether to include or exclude options from the filter
	 * @return	string
	 */
	public function getOptionsAsAttributeString($filter = null, $exclude = true)
	{
		return $this->_getArrayAsAttributeString($this->getOptions(), $filter, $exclude);
	}
	
	/**
	 * Returns an array as an html attribute string
	 * 
	 * @param	array	$filter		Filters which keys to include or exclude
	 * @param	bool	$exclude	Whether to include or exclude keys from the filter
	 * @return	string
	 */
	protected function _getArrayAsAttributeString($array, $filter = null, $exclude = true)
	{
		$string = '';
		foreach ($array as $key => $value) {
			if (!is_string($value) || (!empty($filter) && ((!$exclude && !in_array($key, (array) $filter)) || 
				($exclude && in_array($key, (array) $filter))))) {
				continue;
			}
			$string .= ' ' . $key . '="' . $value . '"';
		}
		return trim($string);
	}
	
	/**
	 * Sets the id attribute
	 * 
	 * @param string $id
	 */
	public function setId($id = null)
	{
		if ($id === null) {
			$this->_setDefaultId(true);
			return;
		}
		
		$this->_attributes['id'] = $id;
		$this->_defaultId = false;
	}
	
	/**
	 * Returns the id attributes or creates one if none exist
	 * 
	 * @return string
	 */
	public function getId()
	{
		if (!array_key_exists('id', $this->_attributes)) {
			return null;
		}
		return $this->_attributes['id'];
	}
	
	/**
	 * Sets a default id
	 * 
	 * @param bool $force
	 */
	protected function _setDefaultId($force = false)
	{
		if (!$force && !$this->_defaultId) {
			return;
		}
		
		$id = $this->_name;
		if ($this->_parent !== null && (($parentId = $this->_parent->getId()) !== null)) {
			$id = $parentId . '_' . $id;
		}
		$this->_defaultId = true;
		$this->_attributes['id'] = $id;
	}
	
	/**
	 * Checks if the field's value is valid
	 * 
	 * @return bool
	 */
	public function isValid()
	{
		$isValid = true;
		$this->_validationMessages = array();
		
		if ($this->getOption('required', false) && empty($this->_value)) {
			$this->_validationMessages[] = $this->_name . ' is required';
			return false;
		}
		
		if ($this->hasOption('validate-with')) {
			$callback = $this->getOption('validate-with');
			if (!call_user_func($callback, $value)) {
				$this->_validationMessages[] = $this->_name . ' failed to validate because '
											 . $this->getOption('validate-with') . '() returned false';
				return false;
			}
			return true;
		}
		
		if (!$this->hasOption('validate')) {
			return true;
		}
			
		$filter = $this->getOption('validate');
		$options = array();
		
		if (in_array($filter, filter_list())) {
			// filter name from the extension filters
			$filter = filter_id($filter);
			
		} else if (preg_match('@/.+/[a-zA-Z]*@', $filter)) {
			// regexp
			$options = array('options' => array('regexp' => $filter));
			$filter = FILTER_VALIDATE_REGEXP;
			
		} else {
			$this->_validationMessages[] = $this->_name . ' failed to validate because the validation string is neither a filter or a regexp';
			return false;
		}
		
		
		if (!filter_var($this->_value, $filter, $options)) {
			$this->_validationMessages[] = $this->_name . ' is not valid';
			return false;
		}
		
		return true;
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
	
	/**
	 * @see Atomik_Form_Field_Abstract::render()
	 */
	public function __toString()
	{
		return $this->render();
	}
	
	protected function _getCssClasses($appendClasses = '')
	{
		return trim($this->getOption('class', '') . $appendClasses);
	}
}