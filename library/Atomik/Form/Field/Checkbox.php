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
class Atomik_Form_Field_Checkbox extends Atomik_Form_Field_Abstract
{
	protected $_defaultValue = 0;
	
	public function isChecked()
	{
		return $this->getValue() == $this->getOption('checked-value', 1);
	}
	
	public function render()
	{
		$html = sprintf('<input type="hidden" name="%s" value="%s" />',
			$this->getFullname(),
			$this->getOption('unchecked-value', 0)
		);
		
		$html .= sprintf('<input type="checkbox" name="%s" value="%s" %s %s/>',
			$this->getFullname(),
			$this->getOption('checked-value', 1),
			$this->getAttributesAsString(array('checked', 'value', 'type')),
			$this->isChecked() ? 'checked="checked"' : ''
		);
		
		return $html;
	}
}