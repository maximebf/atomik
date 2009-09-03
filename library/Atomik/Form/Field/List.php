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
class Atomik_Form_Field_List extends Atomik_Form_Field_Abstract
{
	/**
	 * @var array
	 */
	protected $_data;
	
	/**
	 * Sets the available data for the list
	 * 
	 * @param array $data
	 */
	public function setData($data)
	{
		$this->_data = $data;
	}
	
	/**
	 * Gets the available data for the list
	 * 
	 * @return array
	 */
	public function getData()
	{
		if ($this->_data === null) {
			$this->_initData();
		}
		return $this->_data;
	}
	
	/**
	 * Initializes available data
	 */
	protected function _initData()
	{
		parse_str($this->getOption('values', ''), $this->_data);
	}
	
	/**
	 * Renders the field
	 * 
	 * @return string
	 */
	public function render()
	{
		$options = '';
		foreach ($this->getData() as $key => $value) {
			$selected = $this->getValue() == $key ? 'selected="selected"' : '';
			$options .= sprintf('<option value="%s"%s>%s</option>', $key, $selected, $value);
		}
		
		return sprintf('<select name="%s" %s>%s</select>',
			$this->getFullname(),
			$this->getAttributesAsString(),
			$options
		);
	}
}