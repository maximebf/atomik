<?php

class Atomik_Model_Association_ManyToMany extends Atomik_Model_Association
{
    /** @var Atomik_Model_Descriptor */
    protected $_via;
    
    /** @var string */
    protected $_viaSourceFieldName;
    
    /** @var Atomik_Model_Field */
    protected $_viaSourceField;
    
    /** @var string */
    protected $_viaTargetFieldName;
    
    /** @var Atomik_Model_Field */
    protected $_viaTargetField;
    
    /**
     * @param Atomik_Model_Descriptor $descriptor
     */
    public function setVia(Atomik_Model_Descriptor $descriptor)
    {
        $this->_via = $descriptor;
    }
    
    /**
     * @return Atomik_Model_Descriptor
     */
    public function getVia()
    {
        return $this->_via;
    }

	/**
	 * @param string $name
	 */
    public function setViaTargetFieldName($name)
    {
        $this->_viaTargetFieldName = $name;
        $this->_viaTargetField = null;
    }
    
    /**
     * @return string
     */
    public function getViaTargetFieldName()
    {
        return $this->_viaTargetFieldName;
    }
    
	/**
	 * @param Atomik_Model_Field $field
	 */
    public function setViaTargetField(Atomik_Model_Field $field)
    {
        $this->_viaTargetField = $field;
        $this->_viaTargetFieldName = $field->getName();
    }
    
    /**
     * @return Atomik_Model_Field
     */
    public function getViaTargetField()
    {
        if ($this->_viaTargetField === null) {
            $this->_viaTargetField = $this->getVia()->getField($this->_viaTargetField);
        }
        return $this->_viaTargetField;
    }

	/**
	 * @param string $name
	 */
    public function setViaSourceFieldName($name)
    {
        $this->_viaSourceFieldName = $name;
        $this->_viaSourceField = null;
    }
    
    /**
     * @return string
     */
    public function getViaSourceFieldName()
    {
        return $this->_viaSourceFieldName;
    }
    
	/**
	 * @param Atomik_Model_Field $field
	 */
    public function setViaSourceField(Atomik_Model_Field $field)
    {
        $this->_viaSourceField = $field;
        $this->_viaSourceFieldName = $field->getName();
    }
    
    /**
     * @return Atomik_Model_Field
     */
    public function getViaSourceField()
    {
        if ($this->_viaSourceField === null) {
            $this->_viaSourceField = $this->getVia()->getField($this->_viaSourceField);
        }
        return $this->_viaSourceField;
    }
    
    protected function _setup()
    {
		$this->setSourceFieldName($this->_source->getPrimaryKeyField()->getName());
		$this->setTargetFieldName($this->_target->getPrimaryKeyField()->getName());
		
		$this->setViaSourceFieldName(strtolower($this->_source->getName()) . '_' . $this->_sourceFieldName);
		$this->setViaTargetFieldName(strtolower($this->_target->getName()) . '_' . $this->_targetFieldName);
    }
    
    public function load(Atomik_Model $model)
    {
        $value = $model->_get($this->_sourceFieldName);
        
        $query = $this->_createQuery($model);
        $query->join($this->_source)
              ->filterEqual(array($this->_source, $this->_sourceFieldName), $value);
        
		$collection = $this->getManager()->query($query);
		$model->_set($this->_name, $collection);
    }
}