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

/** Atomik_Db_Instance */
require_once 'Atomik/Db/Instance.php';

/** Atomik_Db_Query */
require_once 'Atomik/Db/Query.php';

/** Atomik_Model_Query_Filter */
require_once 'Atomik/Model/Query/Filter.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Query
{
    protected $_from;
    
    protected $_jointDescriptors = array();
    
    protected $_jointAssociations = array();
    
    protected $_filters = array();
    
    protected $_limit;
    
    protected $_orderBy;
    
    public static function find($descriptor, $id)
    {
        $descriptor = Atomik_Model_Descriptor::factory($descriptor);
        $where = $id;
        
        if (!is_array($where)) {
            $pk = $descriptor->getPrimaryKeyField()->getName();
            $where = array($pk => $id);
        }
        
        $query = self::findQuery($descriptor, $where, null, 1);
        return $query->execute()->getFirst();
    }
    
    public static function findAll($descriptor, $where = array(), $orderBy = null, $limit = null)
    {
        $query = self::findQuery($descriptor, $where, $orderBy, $limit);
        return $query->execute();
    }
    
    public static function findQuery($descriptor, $where = array(), $orderBy = null, $limit = null)
    {
        $query = Atomik_Model_Query::from($descriptor)->filter($where);
        
        if ($orderBy !== null) {
            $query->orderBy($orderBy);
        }
        
        if ($limit !== null) {
            $query->limit($limit);
        }
        
        return $query;
    }
    
    public static function from($descriptor)
    {
        return new Atomik_Model_Query($descriptor);
    }
    
    private function __construct($from)
    {
        $this->_from = Atomik_Model_Descriptor::factory($from);
    }
    
    public function join($descriptor, $association = null)
    {
        $descriptor = Atomik_Model_Descriptor::factory($descriptor);
        
        if ($association === null) {
            if (!$this->_from->isModelAssociated($descriptor)) {
                $found = false;
                foreach ($this->_jointDescriptors as $join) {
                    if ($join->isModelAssociated($descriptor)) {
                        $found = true;
                    }
                }
                if (!$found) {
                    require_once 'Atomik/Model/Query/Exception.php';
                    throw new Atomik_Model_Query_Exception("Cannot create join with unassociated model '" 
                        . $descriptor->getName() . "'");
                }
            }
            if (count($associations = $this->_from->getAssociations($descriptor)) > 1) {
                require_once 'Atomik/Model/Query/Exception.php';
                throw new Atomik_Model_Query_Exception("Ambiguous join with '" . $descriptor->getName() . "'");
            }
            $association = $associations[0];
        }
        
        $this->_jointDescriptors[] = $descriptor;
        $this->_jointAssociations[] = $association;
        return $this;
    }
    
    public function filter($filter)
    {
        if (is_array($filter)) {
            foreach ($filter as $key => $value) {
                if (is_string($key)) {
                    $this->filterEqual($key, $value);
                } else {
                    $this->filter($value);
                }
            }
            return $this;
        }
        
        if (!($filter instanceof Atomik_Model_Query_Filter_Abstract)) {
            require_once 'Atomik/Model/Query/Exception.php';
            throw new Atomik_Model_Query_Exception("Filters must be of type Atomik_Model_Query_Filter_Abstract");
        }
        
        if (($filterDescriptor = $filter->getDescriptor()) === null) {
            $filterDescriptor = $this->_from;
        }
        
        $descriptors = array_merge(array($this->_from), $this->_jointDescriptors);
        
        foreach ($descriptors as $descriptor) {
            if ($descriptor == $filterDescriptor) {
                if (!$descriptor->hasField($filter->getField())) {
                    require_once 'Atomik/Model/Query/Exception.php';
                    throw new Atomik_Model_Query_Exception("Field '" . $filter->getField() 
                        . "' not part of descriptor '" . $descriptor->getName . "'");
                }
                $this->_filters[] = $filter;
                return $this;
            }
        }
        
        require_once 'Atomik/Model/Query/Exception.php';
        throw new Atomik_Model_Query_Exception("Filter's descriptor '" 
            . $filterDescriptor->getName() . "' not part of the query");
    }
    
    public function filterPk($pk)
    {
        $pkField = $descriptor->getPrimaryKeyField()->getName();
        return $this->filterEqual($pkField, $pk);
    }
    
    public function __call($method, $args)
    {
        if (substr($method, 0, 6) == 'filter') {
            $filterName = substr($method, 6);
            $descriptor = $this->_from;
            $field = $args[0];
            $value = isset($args[1]) ? $args[1] : null;
            if (is_array($field)) {
                $descriptor = $field[0];
                $field = $field[1];
            }
            
            $filter = Atomik_Model_Query_Filter::factory($filterName, $descriptor, $field, $value);
            return $this->filter($filter);
        }
    }
    
    public function limit($limit, $offset = 0)
    {
        $this->_limit = array($limit, $offset);
        return $this;
    }
    
    public function orderBy($fieldName)
    {
        if (!$this->_from->hasField($fieldName)) {
            require_once 'Atomik/Model/Query/Exception.php';
            throw new Atomik_Model_Query_Exception("Field '$fieldName' not part of '" 
                . $this->_from->getName() . "'");
        }
        $this->_orderBy = $fieldName;
        return $this;
    }
    
    public function execute()
    {
        return $this->_from->getSession()->executeQuery($this);
    }
    
    public function getDbQuery(Atomik_Db_Instance $dbInstance)
    {
        $field = sprintf('%s.*', $this->_from->getTableName());
        $query = new Atomik_Db_Query($dbInstance);
        $query->select($field)->from($this->_from->getTableName());
        
        foreach ($this->_jointAssociations as $assoc) {
            $assoc->apply($query);
        }
        
        foreach ($this->_filters as $filter) {
            $filter->apply($query);
        }
        
        return $query;
    }
    
    public function toArray()
    {
        $joins = array();
        for ($i = 0, $c = count($this->_jointAssociations); $i < $c; $i++) {
            $joins[] = array($this->_jointDescriptors[$i], $this->_jointAssociations[$i]);
        }
        
        return array(
            'from' => $this->_from,
            'joins' => $joins,
            'filters' => $this->_filters,
            'orderBy' => $this->_orderBy,
            'limit' => $this->_limit
        );
    }
}