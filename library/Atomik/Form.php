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
	protected $_template;

	/**
	 * @var string
	 */
	protected static $_defaultTemplate = 'Atomik/Form/Template/Dl.php';
	
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
	 * @param	array	$fields
	 * @param 	string	$action
	 * @param 	string	$method
	 * @param 	string	$enctype
	 */
	public function __construct($fields = array(), $action = '', $method = 'POST', $enctype = self::URL_ENCODED)
	{
		$this->setFields($fields);
		$this->setAction($action);
		$this->setMethod($method);
		$this->setEnctype($enctype);
		
		$this->setData(array_merge($_POST, $_FILES));
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
	 * Returns the template filename
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
	 * @return string
	 */
	public function getAttribute($name)
	{
		if (!isset($this->_attributes[$name])) {
			return null;
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
	 * Renders the form
	 *
	 * @return string
	 */
	public function render()
	{
		ob_start();
		include $this->getTemplate();
		return ob_get_clean();
	}
	
	/**
	 * @see Atomik_Form::render()
	 */
	public function __toString()
	{
		return $this->render();
	}
}