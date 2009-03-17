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
	 * @var Atomik_Model_Builder
	 */
	protected $_builder;
	
	/**
	 * @var Atomik_Model
	 */
	protected $_model;
	
	/**
	 * @var string
	 */
	protected $_template;
	
	/**
	 * @var string
	 */
	protected $_action = '';
	
	/**
	 * @var string
	 */
	protected $_enctype = 'application/x-www-form-urlencoded';
	
	/**
	 * @var string
	 */
	protected $_method = 'post';
	
	/**
	 * @var array
	 */
	protected $_attributes = array();
	
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
	 * @param string|array|Atomik_Model_Builder|Atomik_Model $object
	 */
	public function __construct($object)
	{
		if (is_string($object)) {
			$this->setBuilder(Atomik_Model_Builder::createFromClass($object));
			
		} else if (is_array($object)) {
			$this->setBuilder(Atomik_Model_Builder::createFromMetadata($object));
			
		} else if ($object instanceof Atomik_Model_Builder) {
			$this->setBuilder($object);
			
		} else if ($object instanceof Atomik_Model) {
			$this->setBuilder($object->getBuilder());
			$this->setModel($object);
			
		} else {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Missing an Atomik_Model_Builder');
		}
	}
	
	/**
	 * Sets the builder for this form
	 *
	 * @param Atomik_Model_Builder $builder
	 */
	public function setBuilder(Atomik_Model_Builder $builder)
	{
		$this->_builder = $builder;
	}
	
	/**
	 * Returns the builder associated to this form
	 *
	 * @return unknown
	 */
	public function getBuilder()
	{
		return $this->_builder;
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
	 * Sets the form action
	 *
	 * @param string $action
	 */
	public function setAction($action)
	{
		$this->_action = $action;
	}
	
	/**
	 * Returns the form action
	 * 
	 * @return string
	 */
	public function getAction()
	{
		return $this->_action;
	}
	
	/**
	 * Sets the form encoding
	 *
	 * @param string $enctype
	 */
	public function setEnctype($enctype)
	{
		$this->_enctype = $enctype;
	}
	
	/**
	 * Returns the form encoding
	 *
	 * @return string
	 */
	public function getEnctype()
	{
		return $this->_enctype;
	}
	
	/**
	 * Sets the form method
	 *
	 * @param string $method
	 */
	public function setMethod($method)
	{
		$this->_method = $method;
	}
	
	/**
	 * Returns the form method
	 *
	 * @return string
	 */
	public function getMethod()
	{
		return $this->_method;
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
				$this->setEnctype($value);
				return;
			case 'action':
				$this->setAction($value);
				return;
			case 'method':
				$this->setMethod($value);
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
					return $this->getEnctype();
					break;
				case 'action':
					return $this->getAction();
					break;
				case 'method':
					return $this->getMethod();
					break;
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
		return $this->_attributes;
	}
	
	/**
	 * Returns all the attributes as an html attributes formatted string
	 *
	 * @return string
	 */
	public function getAttributesAsString()
	{
		$string = '';
		foreach ($this->_attributes as $name => $value) {
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
		if ($model->getBuilder() !== $this->_builder) {
			require_once 'Atomik/Model/Exception.php';
			throw new Atomik_Model_Exception('Only model using builder ' . 
				$this->_builder->getName() . ' can be used with this form');
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
			$this->_model->setData($this->getData());
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