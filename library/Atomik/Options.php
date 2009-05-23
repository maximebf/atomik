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
 * @author Maxime Bouroumeau-Fuseau
 * @copyright 2008-2009 (c) Maxime Bouroumeau-Fuseau
 * @license http://www.opensource.org/licenses/mit-license.php
 * @link http://www.atomikframework.com
 */

/**
 * @package Atomik
 */
class Atomik_Options
{
	/**
	 * @var array
	 */
	protected $_options = array();
	
	/**
	 * Constructor
	 * 
	 * @param array $options
	 */
	public function __construct($options = array())
	{
		$this->setOptions($options);
	}
	
	/**
	 * Sets all options
	 *
	 * @param array $options
	 */
	public function setOptions($options)
	{
		if ($options instanceof Atomik_Options) {
			$options = $options->getOptions();
		}
		
		$this->_options = array();
		foreach ($options as $key => $value) {
			$this->setOption($key, $value);
		}
	}
	
	/**
	 * Sets an option
	 *
	 * @param 	string 	$name
	 * @param 	mixed 	$value
	 */
	public function setOption($name, $value)
	{
		$this->_options[$name] = $value;
	}
	
	/**
	 * Checks if an option exists
	 *
	 * @param 	string $name
	 * @param 	string $prefix
	 * @return 	bool
	 */
	public function hasOption($name, $prefix = '')
	{
		return array_key_exists($prefix . $name, $this->_options);
	}
	
	/**
	 * Checks if an option exists
	 *
	 * @param 	string $name
	 * @param 	string $prefix
	 */
	public function removeOption($name, $prefix = '')
	{
		if (array_key_exists($prefix . $name, $this->_options)) {
			unset($this->_options[$prefix . $name]);
		}
	}
	
	/**
	 * Returns an option
	 *
	 * @param string $name
	 * @param mixed $default OPTIONAL Default value if the key is not found
	 * @return mixed
	 */
	public function getOption($name, $default = null, $prefix = '')
	{
		if (!array_key_exists($prefix . $name, $this->_options)) {
			return $default;
		}
		return $this->_options[$prefix . $name];
	}
	
	/**
	 * Returns all options
	 * 
	 * @return array
	 */
	public function getOptions($prefix = null, $keepPrefixInResult = false)
	{
		if (empty($prefix)) {
			return $this->_options;
		}
		
		$options = array();
		foreach ((array) $prefix as $px) {
			$options = array_merge($options, $this->_getOptionsWithPrefix($px));
		}
		return $options;
	}
	
	protected function _getOptionsWithPrefix($prefix, $keepPrefixInResult = false)
	{
		$options = array();
		foreach ($this->_options as $key => $value) {
			if (substr($key, 0, strlen($prefix)) == $prefix) {
				if (!$keepPrefixInResult) {
					$key = substr($key, strlen($prefix));
				}
				$options[$key] = $value;
			}
		}
		return $options;
	}
}