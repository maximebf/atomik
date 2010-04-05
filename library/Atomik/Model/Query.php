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
    /** @var Atomik_Model_Descriptor */
    protected $_from;
    
    /** @var array of Atomik_Model_Descriptor */
    protected $_jointDescriptors = array();
    
    /** @var array of Atomik_Model_Association */
    protected $_jointAssociations = array();
    
    /** @var array of Atomik_Model_Query_Filter_Interface */
    protected $_filters = array();
    
    /** @var array */
    protected $_limit;
    
    /** @var array */
    protected $_orderBy;
    
    /**
     * Returns the model with the specified id.
     * 
     * If $id is an array, it will return the first model matching
     * the criterias
     * 
     * @param mixed $descriptor
     * @param mixed $id
     * @return Atomik_Model
     */
    public static function find($descriptor, $id)
    {
        $descriptor = Atomik_Model_Descriptor::factory($descriptor);
        $where = $id;
        
        if (!is_array($where)) {
            $pk = $descriptor->getIdentifierField()->getName();
            $where = array($pk => $id);
        }
        
        $query = self::factory($descriptor, $where, null, 1);
        return $query->execute()->getFirst();
    }
    
    /**
     * Returns a collection of model matching the criterias
     * 
     * @param mixed $descriptor
     * @param array $where
     * @param mixed $orderBy
     * @param mixed $limit
     * @return Atomik_Model_Collection
     */
    public static function findAll($descriptor, $where = array(), $orderBy = null, $limit = null)
    {
        $query = self::factory($descriptor, $where, $orderBy, $limit);
        return $query->execute();
    }
    
    /**
     * Builds a query according to the parameters
     * 
     * @param mixed $descriptor
     * @param array $where
     * @param mixed $orderBy
     * @param mixed $limit
     * @return Atomik_Model_Query
     */
    public static function factory($descriptor, $where = array(), $orderBy = null, $limit = null)
    {
        $query = Atomik_Model_Query::from($descriptor);
        
        if (!empty($where)) {
            $query->filter($where);
        }
        
        if ($orderBy !== null) {
            $query->orderBy($orderBy);
        }
        
        if ($limit !== null) {
            $query->limit($limit);
        }
        
        return $query;
    }
    
    /**
     * Creates a query for the specified descriptor
     * 
     * @param mixed $descriptor
     * @return Atomik_Model_Query
     */
    public static function from($descriptor)
    {
        return new Atomik_Model_Query($descriptor);
    }
    
    /**
     * from() must be used instead of the constructor
     * 
     * @param mixed $from
     */
    private function __construct($from)
    {
        $this->_from = Atomik_Model_Descriptor::factory($from);
    }
    
    /**
     * Joins another model to this query according to their association
     * 
     * @param mixed $descriptor
     * @param Atomik_Model_Association $association
     * @return Atomik_Model_Query
     */
    public function join($descriptor, Atomik_Model_Association $association = null)
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
    
    /**
     * Adds a filter to this query
     * 
     * Can either be ab object of type Atomik_Model_Query_Filter_Interface,
     * an array of filters or an array of key/value pairs.
     * 
     * @param mixed $filter
     * @return Atomik_Model_Query
     */
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
        
        if (!($filter instanceof Atomik_Model_Query_Filter_Interface)) {
            require_once 'Atomik/Model/Query/Exception.php';
            throw new Atomik_Model_Query_Exception("Filters must be of type Atomik_Model_Query_Filter_Interface");
        }
        
        $this->_filters[] = $filter;
        return $this;
    }
    
    /**
     * Shortcut to filter models according to their identifier field
     * 
     * @param int $id
     * @return Atomik_Model_Query
     */
    public function filterPk($id)
    {
        $idField = $this->_from->getIdentifierField()->getName();
        return $this->filterEqual($idField, $id);
    }
    
    /**
     * Magic method to quicly add filters using 
     * {@see Atomik_Model_Query_Filter::factory()}
     * 
     * @param string $method
     * @param array $args
     * @return Atomik_Model_Query
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, 6) !== 'filter') {
            return;
        }
        
        $filterName = substr($method, 6);
        list($descriptor, $field, $assoc) = $this->_parseField($args[0]);
        $value = isset($args[1]) ? $args[1] : null;
        
        if ($value instanceof Atomik_Model && $assoc !== false) {
            $value = $value->getProperty($assoc->getTargetField());
        }
        
        $filter = Atomik_Model_Query_Filter::factory($filterName, $descriptor, $field, $value);
        return $this->filter($filter);
    }
    
    /**
     * Orders model by a specific field
     * 
     * @param mixed $field
     * @param string $direction
     * @return Atomik_Model_Query
     */
    public function orderBy($field, $direction = 'ASC')
    {
        if (is_string($field) && preg_match('/(.+)\s+(ASC|DESC)/', $field, $matches)) {
            $field = $matches[1];
            $direction = $matches[2];
        }
        
        list($desc, $fieldName, $assoc) = $this->_parseField($field);
        $column = $desc->getTableName() . '.' . $fieldName;
        
        $this->_orderBy = array($column => $direction);
        return $this;
    }
    
    /**
     * Limits the number of model to be retreived
     * 
     * @param int $limit
     * @param int $offset
     * @return Atomik_Model_Query
     */
    public function limit($limit, $offset = 0)
    {
        $this->_limit = array($offset, $limit);
        return $this;
    }
    
    /**
     * Executes the query and returns a collection of models
     * 
     * @return Atomik_Model_Collection
     */
    public function execute()
    {
		$data = $this->executeData();
		$collection = new Atomik_Model_Collection($this->_from, $data);
		
		return $collection;
    }
    
    /**
     * Executes the query and returns an array of data
     * 
     * @return array
     */
    public function executeData()
    {
		$query = $this->getDbQuery();
		
		if (($result = $query->execute()) === false) {
		    var_dump($query->toSql());
			throw new Atomik_Model_Exception('Query failed: ' . $query->getInstance()->getErrorInfo(2));
		}
		
		$data = $result->fetchAll();
		$this->_from->notify('AfterQuery', $data);
		
		return $data;
    }
    
    /**
     * Returns the database query generated by this query
     * 
     * @return Atomik_Db_Query
     */
    public function getDbQuery()
    {
        $query = $this->_from->getDb()->q()
              ->select($this->_from->getTableName() . '.*')
              ->from($this->_from->getTableName());
        
        foreach ($this->_jointAssociations as $assoc) {
            $assoc->apply($query);
        }
        
        foreach ($this->_filters as $filter) {
            $filter->apply($query);
        }
        
        if ($this->_orderBy !== null) {
            $query->orderBy($this->_orderBy);
        }
        
        if ($this->_limit !== null) {
            $query->limit($this->_limit);
        }
        
		$this->_from->notify('PrepareQuery', $query);
        
        return $query;
    }
    
    /**
     * Returns this query as an array
     * 
     * @return array
     */
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
    
    /**
     * Parses a field and returns the designated descriptor and field
     * 
     * Possible forms:
     *  - $field
     *  - array($descriptor, $field)
     *  - "descriptorName.fieldName"
     * 
     * @param mixed $field
     * @return array
     */
    protected function _parseField($field)
    {
        $descriptor = $this->_from;
        $assoc = false;
    
	    if (is_string($field) && strpos($field, '.')) {
	        $field = explode('.', $field);
	    }
	    
        if (is_array($field)) {
	        $descriptor = Atomik_Model_Descriptor::factory($field[0]);
	        $field = $field[1];
	    }
	    
	    if (is_string($field) && $descriptor->hasAssociation($field)) {
	        $field = $descriptor->getAssociation($field);
	    }
	    
	    if ($field instanceof Atomik_Model_Association) {
	        $assoc = $field;
	        $field = $field->getSourceField();
	    }
	    
	    return array($descriptor, (string) $field, $assoc);
    }
}