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

/** Atomik_Db_Query */
require_once 'Atomik/Db/Query.php';

/**
 * @package Atomik
 * @subpackage Model
 */
abstract class Atomik_Model_Association
{
	/** @var string */
	protected $_name;
	
	/** @var Atomik_Model_Descriptor */
	protected $_source;
	
	/** @var Atomik_Model_Descriptor */
	protected $_target;
	
	/** @var string */
	protected $_sourceFieldName;
	
	/** @var Atomik_Model_Field */
	protected $_sourceField;
	
	/** @var string */
	protected $_targetFieldName;
	
	/** @var Atomik_Model_Field */
	protected $_targetField;
	
	/** @var bool */
	protected $_eagerLoading = false;
	
	/**
	 * @param string $name
	 * @param string $type
	 */
	public function __construct(Atomik_Model_Descriptor $source, $name, Atomik_Model_Descriptor $target = null)
	{
		$this->_source = $source;
		$this->_name = $name;
		$this->setTarget($target);
	}
    
	/**
	 * @param string $name
	 */
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
	
	/**
	 * @return bool
	 */
	public function isManyToOne()
	{
		return $this instanceof Atomik_Model_Association_ManyToOne;
	}
	
	/**
	 * @return bool
	 */
	public function isOneToMany()
	{
		return $this instanceof Atomik_Model_Association_OneToMany;
	}
	
	/**
	 * @return bool
	 */
	public function isManyToMany()
	{
		return $this instanceof Atomik_Model_Association_ManyToMany;
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
    public function setSourceFieldName($name)
    {
        $this->_sourceFieldName = $name;
        $this->_sourceField = null;
    }
    
    /**
     * @return string
     */
    public function getSourceFieldName()
    {
        return $this->_sourceFieldName;
    }
    
	/**
	 * @param Atomik_Model_Field $field
	 */
    public function setSourceField(Atomik_Model_Field $field)
    {
        $this->_sourceField = $field;
        $this->_sourceFieldName = $field->getName();
    }
    
    /**
     * @return string
     */
    public function getSourceField()
    {
        if ($this->_sourceField === null) {
            $this->_sourceField = $this->getSource()->getField($this->_sourceFieldName);
        }
        return $this->_sourceField;
    }
    
	/**
	 * @param string $name
	 */
    public function setTargetFieldName($name)
    {
        $this->_targetFieldName = $name;
        $this->_targetField = null;
    }
    
    /**
     * @return string
     */
    public function getTargetFieldName()
    {
        return $this->_targetFieldName;
    }
    
	/**
	 * @param Atomik_Model_Field $field
	 */
    public function setTargetField(Atomik_Model_Field $field)
    {
        $this->_targetField = $field;
        $this->_targetFieldName = $field->getName();
    }
    
    /**
     * @return Atomik_Model_Field
     */
    public function getTargetField()
    {
        if ($this->_targetField === null) {
            $this->_targetField = $this->getTarget()->getField($this->_targetFieldName);
        }
        return $this->_targetField;
    }
    
    /**
     * @param bool $enable
     */
    public function enabledEagerLoading($enable = true)
    {
        $this->_eadgerLoading = $enable;
    }
    
    /**
     * @return bool
     */
    public function isEagerLoaded()
    {
        return $this->_eagerLoading;
    }
    
    /**
     * Returns the opposite association
     * 
     * @return Atomik_Model_Association
     */
    public function getInvert()
    {
        $className = get_class($this);
        $assoc = new $className($this->_target, $this->_name . 'Invert', $this->_source);
        $assoc->setSourceFieldName($this->_targetFieldName);
        $assoc->setTargetFieldName($this->_sourceFieldName);
        return $assoc;
    }
    
    public function apply(Atomik_Db_Query $query)
    {
        $on = sprintf('%s.%s = %s.%s',
            $this->getTarget()->getTableName(), $this->getTargetField()->getColumnName(),
            $this->getSource()->getTableName(), $this->getSourceField()->getColumnName());
            
        $query->join($this->getTarget()->getTableName(), $on);
    }
    
    abstract public function load(Atomik_Model $model);
	
	/**
	 * Returns the query object to query the target model
	 * 
	 * @param Atomik_Model $model
	 * @return Atomik_Model_Query
	 */
	protected function _createQuery(Atomik_Model $model)
	{
	    $value = $model->_get($this->_sourceFieldName);
		$query = Atomik_Model_Query::from($this->_target)
		            ->filterEqual($this->_targetFieldName, $value);
		return $query;
	}
	
	protected function _executeQuery(Atomik_Model_Query $query)
	{
	    return $this->_target->getSession()->executeQuery($query);
	}
}