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

/** Atomik_Model_Descriptor_Property */
require_once 'Atomik/Model/Descriptor/Property.php';

/** Atomik_Db_Query */
require_once 'Atomik/Db/Query.php';

/**
 * @package Atomik
 * @subpackage Model
 */
abstract class Atomik_Model_Association extends Atomik_Model_Descriptor_Property
{
	/** @var Atomik_Model_Descriptor */
	protected $_source;
	
	/** @var Atomik_Model_Descriptor */
	protected $_target;
	
	/** @var string */
	protected $_sourceField;
	
	/** @var string */
	protected $_targetField;
	
	/**
	 * @param string $name
	 * @param string $type
	 */
	public function __construct($name, Atomik_Model_Descriptor $source, Atomik_Model_Descriptor $target = null)
	{
		$this->_source = $source;
		$this->setName($name);
		$this->setTarget($target);
	}
	
	/**
	 * @return bool
	 */
	public function isMany()
	{
	    return false;
	}
	
    /**
     * @return Atomik_Model_Descriptor
     */
	public function getSource()
	{
	    return $this->_source;
	}
    
	/**
	 * @param Atomik_Model_Descriptor $target
	 */
    public function setTarget(Atomik_Model_Descriptor $target)
    {
        $this->_target = $target;
        $this->_setup();
    }
    
    /**
     * Setups this relation with default values
     */
    abstract protected function _setup();
    
    /**
     * @return Atomik_Model_Descriptor
     */
    public function getTarget()
    {
        return $this->_target;
    }
    
	/**
	 * @param string $name
	 */
    public function setSourceField($name)
    {
        $this->_sourceField = $name;
    }
    
    /**
     * @return string
     */
    public function getSourceField()
    {
        return $this->_sourceField;
    }
    
	/**
	 * @param string $name
	 */
    public function setTargetField($name)
    {
        $this->_targetField = $name;
    }
    
    /**
     * @return string
     */
    public function getTargetField()
    {
        return $this->_targetField;
    }
    
    /**
     * Returns the opposite association
     * 
     * @return Atomik_Model_Association
     */
    public function getReverse()
    {
        $className = get_class($this);
        $assoc = new $className($this->_name . 'Reverse', $this->_target, $this->_source);
        $assoc->setSourceField($this->_targetField);
        $assoc->setTargetField($this->_sourceField);
        return $assoc;
    }
    
    /**
     * When building a query and a joint has been added using this relation,
     * apply to the query the needed params
     * 
     * @param Atomik_Db_Query $query
     * @param string $joinType
     */
    public function apply(Atomik_Db_Query $query, $joinType = 'INNER')
    {
        $on = sprintf('%s.%s = %s.%s',
            $this->_target->getTableName(), $this->_targetField,
            $this->_source->getTableName(), $this->_sourceField);
            
        $query->join($this->_target->getTableName(), $on, null, $joinType);
    }
    
    /**
     * Loads this association and sets the model's property
     * 
     * @param Atomik_Model $model
     * @param mixed $orderBy
     * @param mixed $limit
     */
    abstract public function load(Atomik_Model $model, $orderBy = null, $limit = null);
    
    /**
     * Saves this association for the specified model
     * 
     * @param Atomik_Model $model
     */
    abstract public function save(Atomik_Model $model);
	
	/**
	 * Utility method which returns the query object to query the target model
	 * 
	 * @param Atomik_Model $model
     * @param mixed $orderBy
     * @param mixed $limit
	 * @return Atomik_Model_Query
	 */
	protected function _createQuery(Atomik_Model $model, $orderBy = null, $limit = null)
	{
	    $value = $model->getProperty($this->_sourceField);
		$query = Atomik_Model_Query::from($this->_target)
		            ->filterEqual($this->_targetField, $value);

        if ($orderBy !== null) {
            $query->orderBy($orderBy);
        }
        if ($limit !== null) {
            $query->limit($limit);
        }
		            
		return $query;
	}
}