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
class Atomik_Model_Persister_Joined extends Atomik_Model_Persister_Standard
{
    public function insert(Atomik_Model $model)
    {
		list($parentData, $data) = $this->_prepareData($model);
		
		$parent = $this->_descriptor->getParent();
		$parentData[$parent->getDescriminatorField()->getName()] = get_class($model);
    
		if (($id = $this->_db->insert($parent->getTableName(), $parentData)) === false) {
			throw new Atomik_Model_Session_Exception("Failed inserting model of type '" . get_class($model) . "'");
		}
		
		$data[$this->_descriptor->getIdentifierField()->getName()] = $id;
		
		if ($this->_db->insert($this->_descriptor->getTableName(), $data) === false) {
			throw new Atomik_Model_Session_Exception("Failed inserting model of type '" . get_class($model) . "'");
		}
		
		$model->setProperty($this->_descriptor->getIdentifierField()->getName(), $id);
    }
    
    public function update(Atomik_Model $model)
    {
		list($parentData, $data) = $this->_prepareData($model);
	    $where = $this->_getWhereId($model);
		$parent = $this->_descriptor->getParent();
		
		if (!$this->_db->update($parent->getTableName(), $parentData, $where)) {
			throw new Atomik_Model_Session_Exception("Failed updating model of type '" . get_class($model) . "'");
		}
		
		if (!$this->_db->update($this->_descriptor->getTableName(), $data, $where)) {
			throw new Atomik_Model_Session_Exception("Failed updating model of type '" . get_class($model) . "'");
		}
    }
    
    public function delete(Atomik_Model $model)
    {
		$where = $this->_getWhereId($model);
		
		if (!$this->_db->delete($this->_descriptor->getTableName(), $where)) {
			throw new Atomik_Model_Session_Exception("Failed deleting model of type '" . get_class($model) . "'");
		}
		
		if (!$this->_db->delete($this->_descriptor->getParent()->getTableName(), $where)) {
			throw new Atomik_Model_Session_Exception("Failed deleting model of type '" . get_class($model) . "'");
		}
    }
    
    protected function _prepareData(Atomik_Model $model)
    {
        $parentData = array();
		$data = array();
		
		foreach ($this->_descriptor->getFields() as $field) {
		    $value = $field->getType()->filterOutput($model->getProperty($field->getName()));
		    if ($field->isInherited()) {
		        $parentData[$field->getName()] = $value;
		    } else {
			    $data[$field->getName()] = $value;
		    }
		}
		
		return array($parentData, $data);
    }
}