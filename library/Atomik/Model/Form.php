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

/** Atomik_Model */
require_once 'Atomik/Model.php';

/** Atomik_Model_Builder */
require_once 'Atomik/Model/Builder.php';

/**
 * Creates a form for a model
 * 
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Form
{
	const POST = 'POST';
	
	const GET = 'GET';
	
	const URL_ENCODED = 'application/x-www-form-urlencoded';
	
	const FORM_DATA = 'multipart/form-data';
	
	/**
	 * @var string
	 */
	public $action = '';
	
	/**
	 * @var string
	 */
	public $enctype = 'application/x-www-form-urlencoded';
	
	/**
	 * @var string
	 */
	public $method = 'post';
	
	/**
	 * @var Atomik_Model_Builder
	 */
	protected $_builder;
	
	/**
	 * @var array
	 */
	protected $_attributes = array();
	
	/**
	 * @var array
	 */
	protected $_fields = array();
	
	/**
	 * @var Atomik_Model
	 */
	protected $_model;
	
	/**
	 * @var string
	 */
	protected $_template;
	
	/**
	 * @var array
	 */
	protected $_data;
	
	/**
	 * @var bool
	 */
	protected $_modelUpdated = false;

	/**
	 * @var string
	 */
	protected static $_defaultTemplate = 'Atomik/Model/Form/Template/Dl.php';
	
	/**
	 * Sets the default template 
	 *
	 * @param string $filename
	 */
	public static function setDefaultTemplate($filename)
	{
		self::$_defaultTemplate = $filename;
	}
	
	/**
	 * Returns the default template
	 *
	 * @return string
	 */
	public static function getDefaultTemplate()
	{
		return self::$_defaultTemplate;
	}
	
	/**
	 * Constructor
	 *
	 * @param string|Atomik_Model_Builder|Atomik_Model $object
	 */
	public function __construct($object)
	{
		$this->setBuilder(Atomik_Model_Builder_Factory::get($object));
		if ($object instanceof Atomik_Model) {
			$this->setModel($object);
		}
	}
	
	public function setBuilder(Atomik_Model_Builder $builder)
	{
		$this->_builder = $builder;
		$this->_fields = array();
		
		foreach ($builder->getField() as $builderField) {
			$this->_fields[] = Atomik_Model_Form_Field_Factory::factory(
				$builderField->getOption('form-field', 'Default'), 
				$builderField->name,
				$builderField->getOptions()
			);
		}
	}
	
	public function getBuilder()
	{
		return $this->_builder;
	}
	
	public function setFields($fields)
	{
		$this->_fields = array();
		foreach ($fields as $field) {
			$this->addField($field);
		}
	}
	
	public function addField(Atomik_Model_Form_Field_Interface $field)
	{
		$this->_fields[] = $field;
	}
	
	public function removeField(Atomik_Model_Form_Field_Interface $field)
	{
		for ($i = 0, $c = count($this->_fields); $i < $c; $i++) {
			if ($this->_fields[$i] == $field) {
				unset($this->_fields[$i]);
				break;
			}
		}
	}
	
	public function getFields()
	{
		return $this->_fields;
	}
	
	/**
	 * Sets the template filename used to render this form
	 *
	 * @param string $filename
	 */
	public function setTemplate($filename = null)
	{
		if ($filename === null) {
			$filename = self::getDefaultTemplate();
		}
		$this->_template = $filename;
	}
	
	/**
	 * Gets the template filename
	 *
	 * @return string
	 */
	public function getTemplate()
	{
		if ($this->_template === null) {
			$this->setTemplate();
		}
		return $this->_template;
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
		switch($name) {
			case 'enctype':
				$this->enctype = $value;
				return;
			case 'action':
				$this->action = $value;
				return;
			case 'method':
				$this->method = $value;
				return;
		}
		$this->_attributes[$name] = $value;
	}
	
	/**
	 * Returns an attribute value
	 *
	 * @param string $name
	 * @return string
	 */
	public function getAttribute($name)
	{
		if (!isset($this->_attributes[$name])) {
			switch($name) {
				case 'enctype':
					return $this->enctype;
				case 'action':
					return $this->action;
				case 'method':
					return $this->method;
				default:
					return null;
			}
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
		return array_merge($this->_attributes, array(
			'action' => $this->action,
			'method' => $this->method,
			'enctype' => $this->enctype
		));
	}
	
	/**
	 * Returns all the attributes as an html attributes formatted string
	 *
	 * @return string
	 */
	public function getAttributesAsString()
	{
		$string = '';
		foreach ($this->getAttributes() as $name => $value) {
			$string .= ' ' . $name . '="' . $value . '"';
		}
		return trim($string);
	}
	
	/**
	 * Sets the model associated to this form
	 *
	 * @param Atomik_Model $model
	 */
	public function setModel(Atomik_Model $model)
	{
		if ($model->getBuilder() !== $this->getBuilder()) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Only models using builder ' . $this->getBuilder()->name . ' can be used with the form');
		}
		
		$this->_model = $model;
	}
	
	/**
	 * Checks if a model is associated to this form
	 *
	 * @return bool
	 */
	public function hasModel()
	{
		return $this->_model !== null;
	}
	
	/**
	 * Gets the model associated to this form.
	 * If there is data, the model will be filled with it. 
	 *
	 * @return Atomik_Model
	 */
	public function getModel()
	{
		if (!$this->hasModel()) {
			$this->setModel($this->getBuilder()->createInstance());
		}
		
		if (!$this->_modelUpdated && $this->hasData()) {
			$this->_model->populate($this->getData());
			$this->_modelUpdated = true;
		}
		
		return $this->_model;
	}
	
	/**
	 * Unsets the model associated to this form
	 */
	public function unsetModel()
	{
		$this->_model = null;
	}
	
	/**
	 * Sets the data to filled the model with.
	 * If null is used, data will be retreived from $_POST and $_FILES
	 *
	 * @param array $data OPTIONAL
	 */
	public function setData($data = null)
	{
		if ($data === null) {
			$data = array_merge($_POST, $_FILES);
		}
		
		$this->_data = array();
		foreach ($data as $key => $value) {
			if ($this->getBuilder()->hasField($key)) {
				$this->_data[$key] = $this->getBuilder()->getField($key)->getValue($value);
			}
		}
	}
	
	/**
	 * Checks if there is data available
	 *
	 * @return bool
	 */
	public function hasData()
	{
		if ($this->_data === null) {
			$this->setData();
		}
		return count($this->_data);
	}
	
	/**
	 * Gets the data
	 *
	 * @return array
	 */
	public function getData()
	{
		if ($this->_data === null) {
			$this->setData();
		}
		return $this->_data;
	}
	
	/**
	 * Validates the data
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return $this->getBuilder()->isValid($this->getData());
	}
	
	/**
	 * Returns the messages generated during the last validation
	 *
	 * @return array
	 */
	public function getValidationMessages()
	{
		return $this->getBuilder()->getValidationMessages();
	}
	
	/**
	 * Renders the form
	 *
	 * @return string
	 */
	public function render()
	{
		/* getting fields which should not be ignored */
		$fields = array();
		foreach ($this->getBuilder()->getFields() as $field) {
			if (!$field->getOption('form-ignore', false)) {
				$fields[] = $field;
			}
		}
		unset($field);
		
		ob_start();
		include $this->getTemplate();
		return ob_get_clean();
	}
	
	/**
	 * @see Atomik_Model_Form::render()
	 */
	public function __toString()
	{
		return $this->render();
	}
}