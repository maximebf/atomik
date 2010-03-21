<?php

class Atomik_Model_Association_ManyToMany extends Atomik_Model_Association
{
    /** @var string */
    protected $_viaTable;
    
    /** @var string */
    protected $_viaSourceColumn;
    
    /** @var string */
    protected $_viaTargetColumn;
    
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
    public function setViaTargetColumn($name)
    {
        $this->_viaTargetColumn = $name;
    }
    
    /**
     * @return string
     */
    public function getViaTargetColumn()
    {
        return $this->_viaTargetColumn;
    }

	/**
	 * @param string $name
	 */
    public function setViaSourceColumn($name)
    {
        $this->_viaSourceColumn = $name;
    }
    
    /**
     * @return string
     */
    public function getViaSourceColumn()
    {
        return $this->_viaSourceColumn;
    }
    
    protected function _setup()
    {
		$this->setSourceFieldName($this->_source->getPrimaryKeyField()->getName());
		$this->setTargetFieldName($this->_target->getPrimaryKeyField()->getName());
		
		$this->setViaSourceColumn(strtolower($this->_source->getName() . '_' . $this->_sourceFieldName));
		$this->setViaTargetColumn(strtolower($this->_target->getName() . '_' . $this->_targetFieldName));
    }
    
    /**
     * @see Atomik_Model_Association::getInvert()
     * @return Atomik_Model_Association
     */
    public function getInvert()
    {
        $assoc = parent::getInvert();
        $assoc->setViaTable($this->_viaTable);
        $assoc->setViaSourceColumn($this->_viaTargetColumn);
        $assoc->setViaTargetColumn($this->_viaSourceColumn);
        return $assoc;
    }
    
    public function apply(Atomik_Db_Query $query)
    {
        $onVia = sprintf('%s.%s = %s.%s',
            $this->_viaTable, $this->_viaSourceColumn,
            $this->getSource()->getTableName(), $this->getSourceField()->getColumnName());
            
        $onTarget = sprintf('%s.%s = %s.%s',
            $this->getTarget()->getTableName(), $this->getTargetField()->getColumnName(),
            $this->_viaTable, $this->_viaTargetColumn);
            
        $query->join($this->_viaTable, $onVia)
              ->join($this->getTarget()->getTableName(), $onTarget);
    }
    
    public function load(Atomik_Model $model)
    {
        $value = $model->_get($this->_sourceFieldName);
        
        $query = Atomik_Model_Query::from($this->_target)
              ->join($this->_source, $this->getInvert())
              ->filterEqual(array($this->_source, $this->_sourceFieldName), $value);
        
        $collection = $query->execute();
		$model->_set($this->_name, $collection);
    }
}