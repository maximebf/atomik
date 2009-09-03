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
class Atomik_Form_Field_Timestamp extends Atomik_Form_Field_Abstract
{
	/**
	 * (non-PHPdoc)
	 * @see library/Atomik/Form/Field/Atomik_Form_Field_Abstract#setValue()
	 */
	public function setValue($value)
	{
		if (is_array($value)) {
			$parts = explode('/', $value['date']);
			$date = $parts[2] . '-' . $parts[0] . '-' . $parts[1];
			$value = $date . ' ' . implode(':', $value['time']) . ':00';
		}
		
		parent::setValue($value);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see library/Atomik/Form/Field/Atomik_Form_Field_Interface#render()
	 */
	public function render()
	{
		Atomik_Assets::addNamedAsset('jquery-ui');

		$value = $this->getValue();
		$date = date('m/d/Y');
		$time = date('H:i:s');
		
		if (!empty($value)) {
			$datetime = explode(' ', $this->getValue());
			$parts = explode('-', $datetime[0]);
			$date = $parts[1] . '/' . $parts[2] . '/' . $parts[0];
			$time = $datetime[1];
		}
		
		$html  = $this->_renderDate($date) . ' ';
		$html .= $this->_renderTime($time);
		return $html;
	}
	
	/**
	 * Renders the date input
	 * 
	 * @param string $date
	 * @return string
	 */
	protected function _renderDate($date)
	{
		$html = sprintf('<input type="text" name="%s[date]" id="%s_date" value="%s" class="%s" %s/>',
			$this->getFullname(),
			$this->getId(),
			$date,
			$this->_getCssClasses('timestamp date'),
			$this->getAttributesAsString(array('type', 'id', 'class'))
		);
		$html .= sprintf('<script type="text/javascript">jQuery(function($) { $("#%s_date").datepicker() });</script>', $this->getId());
		return $html;
	}
	
	/**
	 * Renders the time inputs
	 * 
	 * @param string $date
	 * @return string
	 */
	protected function _renderTime($time)
	{
		list($hour, $minute, $second) = explode(':', $time);
		$html  = $this->_renderTimeSelect('hour', $hour, 23) . ':';
		$html .= $this->_renderTimeSelect('minute', $minute, 59);
		return $html;
	}
	
	/**
	 * Renders a time select box
	 * 
	 * @param string $name
	 * @param string $value
	 * @param int $end
	 * @param int $start
	 * @return string
	 */
	protected function _renderTimeSelect($name, $value, $end, $start = 0)
	{
		$html = sprintf('<select name="%s[time][%s]" id="%s_time_%s" class="%s" %s>',
			$this->getFullname(), $name, 
			$this->getId(), $name,
			$this->_getCssClasses('timestamp time ' . $name),
			$this->getAttributesAsString(array('type', 'id', 'class'))
		);
					
		for ($i = $start; $i <= $end; $i++) {
			$html .= sprintf('<option value="%s"%s>%s</option>' . "\n",
				$i,
				$i == $value ? ' selected="selected"' : '', 
				str_pad($i, 2, '0', STR_PAD_LEFT)
			);
		}
		
		return $html . '</select>';
	}
}