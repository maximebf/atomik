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

/** Atomik_Model_Query_Filter_Interface */
require_once 'Atomik/Model/Query/Filter/Interface.php';

/**
 * @package Atomik
 * @subpackage Model
 */
class Atomik_Model_Query_FilterGroup implements Atomik_Model_Query_Filter_Interface
{
    /** @var Atomik_Model_Query */
	protected $_query;
	
    /** @var Atomik_Model_Query_FilterGroup */
	protected $_parentGroup;
	
    /** @var string */
	protected $_separator;
	
	/** @var array of Atomik_Model_Query_Filter_Interface */
	protected $_filters = array();
	
	/**
	 * @param Atomik_Model_Query $query
	 * @param string $separator
	 * @param Atomik_Model_Query_FilterGroup $parentGroup
	 */
	public function __construct(Atomik_Model_Query $query, $separator, 
	    Atomik_Model_Query_FilterGroup $parentGroup = null)
	{
	    $this->_query = $query;
	    $this->_parentGroup = $parentGroup;
		$this->setSeparator($separator);
	}
	
	/**
	 * @param string $separator
	 */
	public function setSeparator($separator)
	{
	    $separator = strtoupper(trim($separator));
		$this->_separator = " $separator ";
	}
	
	/**
	 * @return string
	 */
	public function getSeparator()
	{
		return $this->_separator;
	}
	
	/**
	 * @return Atomik_Model_Query
	 */
	public function end()
	{
	    if ($this->_parentGroup !== null) {
	        return $this->_parentGroup;
	    }
	    return $this->_query;
	}
    
    /**
     * Adds a filter to this group
     * 
     * Can either be an object of type Atomik_Model_Query_Filter_Interface,
     * an array of filters or an array of key/value pairs.
     * 
     * @param mixed $filter
     * @return Atomik_Model_Query_FilterGroup
     */
    public function filter($filter)
    {
        if (is_array($filter)) {
            foreach ($filter as $key => $value) {
                if (is_string($key)) {
                    if ($value === null) {
                        $this->filterIsNull($key);
                    } else {
                        $this->filterEqual($key, $value);
                    }
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
     * Returns a new filter group
     * 
     * @param string $separator
     * @return Atomik_Model_Query_FilterGroup
     */
    public function filterGroup($separator)
    {
        $group = new Atomik_Model_Query_FilterGroup($this->_query, $separator, $this);
        $this->filter($group);
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
        $this->filter(new Atomik_Model_Query_Filter_Expr($expr));
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
        if (substr($method, 0, 6) !== 'filter') {
            throw new Atomik_Model_Query_Exception("Atomik_Model_Query_FilterGroup::$method does not exists");
        }
        
        $filterName = substr($method, 6);
        list($descriptor, $field, $assoc) = $this->_query->_parseField($args[0]);
        $value = isset($args[1]) ? $args[1] : null;
        
        if ($value instanceof Atomik_Model) {
            if ($assoc !== false) {
                $value = $value->getProperty($assoc->getTargetField());
            } else {
                $modelDesc = Atomik_Model_Descriptor::factory($value);
                $value = $value->getProperty($modelDesc->getIdentifierField()->getName());
            }
        }
        
        $filter = Atomik_Model_Query_Filter::factory($filterName, $descriptor, $field, $value);
        return $this->filter($filter);
    }
    
    /**
     * Removes all filters from the group
     */
    public function clearFilters()
    {
        $this->_filters = array();
    }
    
    /**
     * @see Atomik_Model_Query_Filter_Interface::getSqlAndParams()
     */
    public function getSqlAndParams()
    {
        $conditions = array();
        $params = array();
        
        foreach ($this->_filters as $filter) {
            list($filterSql, $filterParams) = $filter->getSqlAndParams();
            $conditions[] = $filterSql;
            $params = array_merge($params, $filterParams);
        }
        
        $sql = '';
        if (count($conditions)) {
            $sql = '(' . implode($this->_separator, $conditions) . ')';
        }
        
        return array($sql, $params);
    }
}