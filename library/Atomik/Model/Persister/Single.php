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

/** Atomik_Model_Persister_Standard */
require_once 'Atomik/Model/Persister/Standard.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Persister_Single extends Atomik_Model_Persister_Standard
{
    public function insert(Atomik_Model $model)
    {
		$data = $this->_prepareData($model);
		
		$parent = $this->_descriptor->getParent();
		$className = $this->_descriptor->getName();
		$data[$parent->getDescriminatorField()->getName()] = $className;
		
		if (($id = $this->_db->insert($this->_descriptor->getTableName(), $data)) === false) {
			throw new Atomik_Model_Exception("Failed inserting model of type '" 
			    . get_class($model) . "': " . $this->_db->getErrorInfo(2));
		}
		
		$model->setProperty($this->_descriptor->getIdentifierField()->getName(), $id);
    }
}