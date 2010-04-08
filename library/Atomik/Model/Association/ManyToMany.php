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

/** Atomik_Model_Association */
require_once 'Atomik/Model/Association.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Association_ManyToMany extends Atomik_Model_Association
{
    /** @var string */
    protected $_viaTable;
    
    /** @var string */
    protected $_viaSourceField;
    
    /** @var string */
    protected $_viaTargetField;
    
    public function isMany()
    {
        return true;
    }
    
    /**
     * @param string $tableName
     */
    public function setViaTable($tableName)
    {
        $this->_viaTable = $tableName;
    }
    
    /**
     * @return string
     */
    public function getViaTable()
    {
        return $this->_viaTable;
    }

	/**
	 * @param string $name
	 */
    public function setViaTargetField($name)
    {
        $this->_viaTargetField = $name;
    }
    
    /**
     * @return string
     */
    public function getViaTargetField()
    {
        return $this->_viaTargetField;
    }

	/**
	 * @param string $name
	 */
    public function setViaSourceField($name)
    {
        $this->_viaSourceField = $name;
    }
    
    /**
     * @return string
     */
    public function getViaSourceField()
    {
        return $this->_viaSourceField;
    }
    
    protected function _setup()
    {
		$this->_sourceField = $this->_source->getIdentifierField()->getName();
		$this->_targetField = $this->_target->getIdentifierField()->getName();
		
		$this->_viaSourceField = $this->_source->getNameAsProperty() . ucfirst($this->_sourceField);
		$this->_viaTargetField = $this->_target->getNameAsProperty() . ucfirst($this->_targetField);
    }
    
    /**
     * @see Atomik_Model_Association::getReverse()
     * @return Atomik_Model_Association
     */
    public function getReverse()
    {
        $assoc = parent::getReverse();
        $assoc->setViaTable($this->_viaTable);
        $assoc->setViaSourceField($this->_viaTargetField);
        $assoc->setViaTargetField($this->_viaSourceField);
        return $assoc;
    }
    
    public function apply(Atomik_Db_Query $query)
    {
        $onVia = sprintf('%s.%s = %s.%s',
            $this->_viaTable, $this->_viaSourceField,
            $this->_source->getTableName(), $this->_sourceField);
            
        $onTarget = sprintf('%s.%s = %s.%s',
            $this->_target->getTableName(), $this->_targetField,
            $this->_viaTable, $this->_viaTargetField);
            
        $query->join($this->_viaTable, $onVia)
              ->join($this->_target->getTableName(), $onTarget);
    }
    
    public function load(Atomik_Model $model)
    {
        $value = $model->getProperty($this->_sourceField);
        
        $query = Atomik_Model_Query::from($this->_target)
              ->join($this->_source, $this->getReverse())
              ->filterEqual(array($this->_source, $this->_sourceField), $value);
        
        $data = $query->executeData();
        $collection = new Atomik_Model_AssocCollection($model, $this, $data);
        $model->setProperty($this->_name, $collection);
    }
    
    public function save(Atomik_Model $model)
    {
        $coll = $model->getProperty($this->_name);
        $sourceValue = $model->getProperty($this->_sourceField);
        $db = $this->_source->getDb();
        $changeset = $coll->getChangeset();
        
        foreach ($changeset['added'] as $target) {
            $targetValue = $target->getProperty($this->_targetField);
            $data = array(
                $this->_viaSourceField => $sourceValue,
                $this->_viaTargetField => $targetValue
            );
            $db->insert($this->_viaTable, $data);
        }
        
        foreach ($changeset['removed'] as $target) {
            $targetValue = $target->getProperty($this->_targetField);
            $where = array(
                $this->_viaSourceField => $sourceValue,
                $this->_viaTargetField => $targetValue
            );
            $db->delete($this->_viaTable, $where);
        }
    }
}