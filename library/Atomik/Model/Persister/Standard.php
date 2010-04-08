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

/** Atomik_Model_Persister */
require_once 'Atomik/Model/Persister.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Persister_Standard extends Atomik_Model_Persister
{
    public function insert(Atomik_Model $model)
    {
		$data = $this->_prepareData($model);
		
		if (($id = $this->_db->insert($this->_descriptor->getTableName(), $data)) === false) {
			throw new Atomik_Model_Exception("Failed inserting model of type '" . get_class($model) . "'");
		}
		
		$model->setProperty($this->_descriptor->getIdentifierField()->getName(), $id);
    }
    
    public function update(Atomik_Model $model)
    {
		$data = $this->_prepareData($model);
	    $where = $this->_getWhereId($model);
	    
		if (!$this->_db->update($this->_descriptor->getTableName(), $data, $where)) {
			throw new Atomik_Model_Exception("Failed updating model of type '" . get_class($model) . "'");
		}
    }
    
    public function delete(Atomik_Model $model)
    {
		$where = $this->_getWhereId($model);
		
		if (!$this->_db->delete($this->_descriptor->getTableName(), $where)) {
			throw new Atomik_Model_Exception("Failed deleting model of type '" . get_class($model) . "'");
		}
    }
    
    protected function _prepareData(Atomik_Model $model)
    {
		$data = array();
		
		foreach ($this->_descriptor->getFields() as $field) {
			$data[$field->getName()] = 
			    $field->getType()->filterOutput($model->getProperty($field->getName()));
		}
		
		return $data;
    }
    
    protected function _computeChangeset($originalData, $data)
    {
		$changeset = array();
		foreach ($data as $key => $value) {
		    if (!array_key_exists($key, $originalData) || $originalData[$key] != $value) {
	            $changeset[$key] = $value;
		    }
		}
		return $changeset;
    }
    
    protected function _getWhereId(Atomik_Model $model)
    {
        $id = $this->_descriptor->getIdentifierField()->getName();
        return array($id => $model->getProperty($id));
    }
}