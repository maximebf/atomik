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

/** Atomik_Db_Query_Expr */
require_once 'Atomik/Db/Query/Expr.php';

/** Atomik_Db_Query */
require_once 'Atomik/Db/Query.php';

/** Atomik_Model_Query_Filter */
require_once 'Atomik/Model/Query/Filter.php';

/** Atomik_Model_Query_FilterGroup */
require_once 'Atomik/Model/Query/FilterGroup.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Query extends Atomik_Db_Query_Expr
{
    /** @var Atomik_Model_Descriptor */
    protected $_from;
    
    /** @var Atomik_Db_Query */
    protected $_query;
    
    /** @var array of Atomik_Model_Descriptor */
    protected $_joins = array();
    
    /** @var Atomik_Model_Query_FilterGroup */
    protected $_filters = array();
    
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
	 * Creates a filter of type Atomik_Model_Query_Filter_Expr
	 * 
	 * @param string $expr
	 * @return Atomik_Model_Query_Filter_Expr
	 */
	public static function expr($expr)
	{
	    require_once 'Atomik/Model/Query/Filter/Expr.php';
	    return new Atomik_Model_Query_Filter_Expr($expr);
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
     * @param mixed $from
     */
    public function __construct($from)
    {
        $this->_filters = new Atomik_Model_Query_FilterGroup($this, 'and');
        
        $this->_from = $this->_parseDescriptor($from);
        $this->_query = $this->_from->getDb()->q()
                            ->select($this->_from->getTableName() . '.*')
                            ->from($this->_from->getTableName());
                            
        $this->_from->getHydrator()->prepareQuery($this);
		$this->_from->notify('PrepareQuery', $this);
    }
    
    /**
     * Specifies which fields to retreive
     * 
     * @param string $field
     * @param string ...
     * @return Atomik_Model_Query
     */
    public function select($field)
    {
        $this->_query->clearSelect();
        $fields = func_get_args();
        $select = array();
        foreach ($fields as $field) {
            if ($field instanceof Atomik_Db_Query_Expr) {
                $select[] = (string) $field;
                continue;
            }
            list($descriptor, $field, $assoc) = $this->_parseField($field);
            $select[] = $descriptor->getTableName() . '.' . $field;
        }
        $this->_query->select($select);
        return $this;
    }
    
    /**
     * Joins another model to this query according to their association
     * 
     * If $association is null, will search for the first model (which
     * are queried) that is related to $descriptor.
     * 
     * If $association is a string, it must be of the form 
     * DescriptorName.associationName
     * 
     * @param mixed $descriptor
     * @param mixed $association
     * @return Atomik_Model_Query
     */
    public function join($descriptor, $association = null, $type = 'INNER')
    {
        if (is_string($descriptor)) {
            list($descriptor, $alias) = $this->_parseAlias($descriptor);
            if (strpos($descriptor, '.') !== false) {
                list($source, $field, $association) = $this->_parseField($descriptor, true);
                $descriptor = $association->getTarget();
            } else {
                $descriptor = Atomik_Model_Descriptor::factory($descriptor);
            }
            $this->_aliases[$alias] = $descriptor;
        }
        
        if ($association === null) {
            $source = $this->_from;
            
            // searches to which model the descriptor is associated
            if (!$this->_from->isModelAssociated($descriptor)) {
                $found = false;
                foreach ($this->_joins as $join) {
                    if ($join->isModelAssociated($descriptor)) {
                        $source = $join;
                        $found = true;
                    }
                }
                if (!$found) {
                    require_once 'Atomik/Model/Query/Exception.php';
                    throw new Atomik_Model_Query_Exception("Cannot create join with unassociated model '" 
                        . $descriptor->getName() . "'");
                }
            }
            
            // gets the association
            if (count($associations = $source->getAssociations($descriptor)) > 1) {
                require_once 'Atomik/Model/Query/Exception.php';
                throw new Atomik_Model_Query_Exception("Ambiguous join with '" . $descriptor->getName() . "'");
            }
            $association = $associations[0];
            
        } else if (is_string($association)) {
            list($source, $field, $association) = $this->_parseField($association, true);
            if (!$source->isModelRelated($descriptor)) {
                require_once 'Atomik/Model/Query/Exception.php';
                throw new Atomik_Model_Query_Exception("Cannot create join with unassociated model '" 
                    . $descriptor->getName() . "'");
            }
            $association = $source->getAssociation($association);
        }
        
        $this->_joins[] = $descriptor;
        $association->apply($this->_query, $type);
        return $this;
    }
    
    /**
     * Adds a filter to this query
     * 
     * @param mixed $filter
     * @return Atomik_Model_Query
     */
    public function filter($filter)
    {
        $this->_filters->filter($filter);
        return $this;
    }
    
    /**
     * Returns a new filter group
     * 
     * @param string $separator
     * @return Atomik_Model_Query_FilterGroup
     */
    public function filterGroup($separator)
    {
        $group = new Atomik_Model_Query_FilterGroup($this, $separator);
        $this->_filters->filter($group);
        return $group;
    }
    
    /**
     * Creates a new filter of type Atomik_Model_Query_Filter_Expr
     *  
     * @param string $expr
     * @return Atomik_Model_Query
     */
    public function filterExpr($expr)
    {
        $this->_filters->filterExpr($expr);
        return $this;
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
        call_user_func_array(array($this->_filters, $method), $args);
        return $this;
    }
    
    /**
     * Applies the filters to the query and removes them
     */
    public function applyFilters()
    {
		list($sql, $params) = $this->_filters->getSqlAndParams();
		if (!empty($sql)) {
    		$this->_query->where($sql, $params);
    		$this->_filters->clearFilters();
		}
		return $this;
    }
    
    /**
     * Adds a group by clause
     * 
     * @param mixed $field {@see _parseField()}
     * @return Atomik_Model_Query
     */
    public function groupBy($field)
    {
        list($desc, $fieldName, $assoc) = $this->_parseField($field);
        $this->_query->groupBy($desc->getTableName() . '.' . $fieldName);
        return $this;
    }
    
    /**
     * Orders model by a specific field
     * 
     * @param mixed $field {@see _parseField()}
     * @param string $direction
     * @return Atomik_Model_Query
     */
    public function orderBy($field, $direction = 'ASC')
    {
        if (strpos($field, ',')) {
            foreach (explode(',', $field) as $f) {
                $this->orderBy($f);
            }
            return $this;
        }
        
        if (is_string($field) && preg_match('/(.+)\s+(ASC|DESC)/', $field, $matches)) {
            $field = $matches[1];
            $direction = $matches[2];
        }
        
        list($desc, $fieldName, $assoc) = $this->_parseField($field);
        $column = $desc->getTableName() . '.' . $fieldName;
        
        $this->_query->orderBy(array($column => $direction));
        return $this;
    }
    
    /**
     * Limits the number of model to be retreived
     * 
     * @see Atomik_Db_Query::limit()
     * @param int $limit
     * @return Atomik_Model_Query
     */
    public function limit($limit)
    {
        $args = func_get_args();
        call_user_func_array(array($this->_query, 'limit'), $args);
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
        $this->applyFilters();
		$query = $this->_query;
		
		if (($result = $query->execute()) === false) {
			throw new Atomik_Model_Exception('Query failed: ' . $query->getInstance()->getErrorInfo(2));
		}
		
		$data = $result->fetchAll();
		$this->_from->notify('AfterQuery', $data);
		
		return $data;
    }
    
    /**
     * Executes the request as a count() statement
     * 
     * @return int
     */
    public function count($field = '*')
    {
        $this->applyFilters();
        $query = clone $this->_query;
        $query->count($field);
        
		if (($result = $query->execute()) === false) {
			throw new Atomik_Model_Exception('Query failed: ' . $query->getInstance()->getErrorInfo(2));
		}
		
		return (int) $result->fetchColumn();
    }
    
    /**
     * Returns the database query generated by this query
     * 
     * @return Atomik_Db_Query
     */
    public function getDbQuery()
    {
        $this->applyFilters();
        return $this->_query;
    }
    
    /**
     * (non-PHPdoc)
     * @see library/Atomik/Db/Query/Atomik_Db_Query_Expr#__toString()
     */
    public function __toString()
    {
        $this->applyFilters();
        return $this->_query->toSql();
    }
    
    /**
     * Returns the descriptor specified by $descriptor
     * which can either be an alias or a descriptor's name
     * 
     * @param mixed $descriptor
     * @return Atomik_Model_Descriptor
     */
    public function _getDescriptor($descriptor)
    {
        if (is_string($descriptor) && isset($this->_aliases[$descriptor])) {
            return $this->_aliases[$descriptor];
        }
        return Atomik_Model_Descriptor::factory($descriptor);
    }
    
    /**
     * Parses a descriptor string and returns the descriptor
     * and its alias if specified
     *  
     * @param mixed $descriptor
     * @param bool $setAlias
     * @return Atomik_Model_Descriptor
     */
    protected function _parseDescriptor($descriptor, $setAlias = true)
    {
        list($descriptor, $alias) = $this->_parseAlias($descriptor);
        $descriptor = Atomik_Model_Descriptor::factory($descriptor);
        
        if ($alias && $setAlias) {
            $this->_aliases[$alias] = $descriptor;
        }
        
        return $descriptor;
    }
    
    /**
     * Parses a string and returns the string and its alias (if specified)
     * 
     * @param string $string
     * @return array
     */
    protected function _parseAlias($string)
    {
        $alias = false;
        if (is_string($string) && strpos($string, ' ') !== false) {
            $parts = explode(' ', $string);
            $string = $parts[0];
            $alias = $parts[1]; 
        }
        return array($string, $alias);
    }
    
    /**
     * Parses a field and returns the designated descriptor and field
     * 
     * Possible forms:
     *  - $property
     *  - array($descriptor, $property)
     *  - "descriptorName.propertyName"
     * 
     * @param mixed $field
     * @return array
     */
    public function _parseField($field, $mustBeAssoc = false)
    {
        $descriptor = $this->_from;
        $assoc = false;
    
	    if (is_string($field) && strpos($field, '.')) {
	        $field = explode('.', $field);
	    }
	    
        if (is_array($field)) {
	        $descriptor = $this->_getDescriptor($field[0]);
	        $field = $field[1];
	    }
	    
	    if ($descriptor->hasParent() && $descriptor->isPropertyMapped($field) &&
	        $descriptor->getMappedProperty($field)->isInherited()) {
	            $descriptor = $descriptor->getParent();
	    }
	    
	    if (is_string($field) && $descriptor->hasAssociation($field)) {
	        $assoc = $descriptor->getAssociation($field);
	        $field = $assoc->getSourceField(); 
	    }
	    
	    if ($mustBeAssoc && $assoc === false) {
            require_once 'Atomik/Model/Query/Exception.php';
            throw new Atomik_Model_Query_Exception("$field must targets an association");
	    }
	    
	    return array($descriptor, (string) $field, $assoc);
    }
}