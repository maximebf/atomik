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

/** Atomik_Form_Fieldset */
require_once 'Atomik/Form/Fieldset.php';

/** Atomik_Form_Field_Interface */
require_once 'Atomik/Form/Field/Interface.php';

/**
 * Creates a form
 * 
 * @package Atomik
 * @subpackage Form
 */
class Atomik_Form extends Atomik_Form_Fieldset
{
	const POST = 'POST';
	
	const GET = 'GET';
	
	const URL_ENCODED = 'application/x-www-form-urlencoded';
	
	const FORM_DATA = 'multipart/form-data';
	
	/**
	 * @var array
	 */
	protected $_attributes = array(
		'action' 	=> '',
		'enctype' 	=> 'application/x-www-form-urlencoded',
		'method'	=> 'POST'
	);
	
	/**
	 * @var string
	 */
	protected $_formTemplate;
	
	/**
	 * @var string
	 */
	protected $_fieldTemplate;

	/**
	 * @var string
	 */
	protected static $_defaultFormTemplate = 'Atomik/Form/Template/Dl.php';

	/**
	 * @var string
	 */
	protected static $_defaultFieldTemplate = 'Atomik/Form/Template/Field/Dtdd.php';
	
	/**
	 * Sets the default form template 
	 *
	 * @param string $filename
	 */
	public static function setDefaultFormTemplate($filename)
	{
		self::$_defaultFormTemplate = $filename;
	}
	
	/**
	 * Returns the default form template
	 *
	 * @return string
	 */
	public static function getDefaultFormTemplate()
	{
		return self::$_defaultFormTemplate;
	}
	
	/**
	 * Sets the default fields template 
	 *
	 * @param string $filename
	 */
	public static function setDefaultFieldTemplate($filename)
	{
		self::$_defaultFieldTemplate = $filename;
	}
	
	/**
	 * Returns the default fields template
	 *
	 * @return string
	 */
	public static function getDefaultFieldTemplate()
	{
		return self::$_defaultFieldTemplate;
	}
	
	/**
	 * Constructor
	 *
	 * @param	array	$name
	 * @param	array	$fields
	 * @param 	string	$action
	 * @param 	string	$method
	 * @param 	string	$enctype
	 */
	public function __construct($name = null, $fields = array(), $action = '', $method = 'POST', $enctype = self::URL_ENCODED)
	{
		$this->setName($name);
		$this->setFields($fields);
		$this->setAction($action);
		$this->setMethod($method);
		$this->setEnctype($enctype);
		$this->_setup();
		
		$this->setData(array_merge($_POST, $_FILES));
	}
	
	/**
	 * For subclass to initialize themselves
	 */
	protected function _setup() {}
	
	/**
	 * Sets the form template filename
	 *
	 * @param string $filename
	 */
	public function setFormTemplate($filename = null)
	{
		if ($filename === null) {
			$filename = self::getDefaultFormTemplate();
		}
		$this->_formTemplate = $filename;
	}
	
	/**
	 * Returns the form template filename
	 *
	 * @return string
	 */
	public function getFormTemplate()
	{
		if ($this->_formTemplate === null) {
			$this->setFormTemplate();
		}
		return $this->_formTemplate;
	}
	
	/**
	 * Sets the fields template filename
	 *
	 * @param string $filename
	 */
	public function setFieldTemplate($filename = null)
	{
		if ($filename === null) {
			$filename = self::getDefaultFieldTemplate();
		}
		$this->_fieldTemplate = $filename;
	}
	
	/**
	 * Returns the fields template filename
	 *
	 * @return string
	 */
	public function getFieldTemplate()
	{
		if ($this->_fieldTemplate === null) {
			$this->setFieldTemplate();
		}
		return $this->_fieldTemplate;
	}
	
	/**
	 * Sets the action attribute
	 * 
	 * @param	string	$value
	 */
	public function setAction($value)
	{
		$this->_attributes['action'] = $value;
	}
	
	/**
	 * Returns the action attribute
	 * 
	 * @return string
	 */
	public function getAction()
	{
		return $this->_attributes['action'];
	}
	
	/**
	 * Sets the method attribute
	 * 
	 * @param	string	$value
	 */
	public function setMethod($value)
	{
		$this->_attributes['method'] = $value;
	}
	
	/**
	 * Returns the method attribute
	 * 
	 * @return string
	 */
	public function getMethod()
	{
		return $this->_attributes['method'];
	}
	
	/**
	 * Sets the enctype attribute
	 * 
	 * @param	string	$value
	 */
	public function setEnctype($value)
	{
		$this->_attributes['enctype'] = $value;
	}
	
	/**
	 * Returns the enctype attribute
	 * 
	 * @return string
	 */
	public function getEnctype()
	{
		return $this->_attributes['enctype'];
	}

	/**
	 * Resets the form attributes
	 *
	 * @param array $attributes
	 */
	public function setAttributes($attributes)
	{
		$this->_attributes = array(
			'action' 	=> '',
			'enctype' 	=> self::URL_ENCODED,
			'method'	=> self::POST
		);
		
		foreach ($attributes as $name => $value) {
			$this->setAttribute($name, $value);
		}
	}
	
	/**
	 * Sets the data for the fields
	 * 
	 * @param array $data
	 */
	public function setData($data)
	{
		if ($this->_parent === null && $this->_name !== null) {
			if (!isset($data[$this->_name])) {
				$data = array();
			} else {
				$data = $data[$this->_name];
			}
		}
		
		parent::setData($data);
	}
	
	/**
	 * @see Atomik_Form::renderFields()
	 * @return string
	 */
	public function render()
	{
		return $this->renderFields();
	}
	
	/**
	 * Renders the form fields only
	 *
	 * @return string
	 */
	public function renderFields()
	{
		$output = '';
		foreach ($this->_fields as $field) {
			if ($field instanceof Atomik_Form) {
				$output .= $field->render();
				continue;
			}
			
			$label = $this->getLabel($field->getName());
			ob_start();
			include $this->getFieldTemplate();
			$output .= ob_get_clean();
		}
		return $output;
	}
	
	/**
	 * Renders the whole form (the form tag and all its fields)
	 * 
	 * @return string
	 */
	public function renderForm()
	{
		ob_start();
		include $this->getFormTemplate();
		return ob_get_clean();
	}
	
	/**
	 * @see Atomik_Form::renderForm()
	 */
	public function __toString()
	{
		return $this->renderForm();
	}
}