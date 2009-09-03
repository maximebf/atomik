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
class Atomik_Form_Field_File extends Atomik_Form_Field_Abstract
{
	public function setParent(Atomik_Form_Field_Interface $parent)
	{
		$parent->setEnctype(Atomik_Form::FORM_DATA);
		parent::setParent($parent);
	}
	
	public function setValue($value)
	{
		if (is_array($value)) {
			if ($value['error'] == UPLOAD_ERR_NO_FILE) {
				return;
			}
			$value = $this->_uploadFile($value);
		}
		
		parent::setValue($value);
	}
	
	protected function _uploadFile($info)
	{
		$targetDir = rtrim($this->getOption('upload-dir', '.'), '/') . '/';
		$targetName = uniqid() . substr($info['name'], strrpos($info['name'], '.'));
		$target = $targetDir . $targetName;
		
		if (move_uploaded_file($info['tmp_name'], $target)) {
			return $target;
		}
		
		return null;
	}
	
	public function render()
	{
		$html = '';
		
		if (!empty($this->_value)) {
			$html = 'Currently: ' . $this->_value . '<br />';
		}
		
		$html .= sprintf('<input type="file" name="%s" %s/>',
			$this->getFullname(),
			$this->getAttributesAsString('type')
		);
		
		return $html;
	}
}