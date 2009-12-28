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

/** Atomik_Model_Field_Abstract */
require_once 'Atomik/Model/Field/Abstract.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Field_Enum extends Atomik_Model_Field_Abstract
{
	protected function _getDataMap()
	{
		return array_flip(array_map('trim', explode(',', $this->getOption('options'))));
	}
	
	/**
	 * @return array
	 */
	public function getSqlType()
	{
		return array('int', 2);
	}
	
	/**
	 * @return Atomik_Form_Field_Interface
	 */
	public function getDefaultFormField()
	{
		$options = $this->getOptions('form-');
		$options['values'] = http_build_query($this->_getDataMap());
		return Atomik_Form_Field_Factory::factory('List', $this->name, $options);
	}
	
	public function render($value)
	{
		$map = $this->_getDataMap();
		$intValue = (int) $value;
		
	    if (((string) $intValue) == $value) {
	        $map = array_flip($map);
		    return $map[$value];
	    }
	    
	    return $value;
	}
}
