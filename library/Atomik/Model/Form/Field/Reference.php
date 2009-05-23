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

/** Atomik_Form_Field_List */
require_once 'Atomik/Form/Field/List.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Form_Field_Reference extends Atomik_Form_Field_List
{
	protected function _initData()
	{
		$reference = $this->getOption('reference');
		$builder = Atomik_Model_Builder_Factory::get($reference->target);
		
		$rows = Atomik_Model::findAll($builder);
		$this->_data = array();
		foreach ($rows as $row) {
			$this->_data[$row->getPrimaryKey()] = (string) $row;
		}
	}
}