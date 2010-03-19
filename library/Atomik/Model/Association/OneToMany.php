<?php

class Atomik_Model_Association_OneToMany extends Atomik_Model_Association
{
    protected function _setup()
    {
		$this->setSourceFieldName($this->_source->getPrimaryKeyField()->getName());
		$this->setTargetFieldName(strtolower($this->_source->getName()) . '_' . $this->_sourceFieldName);
    }
    
    public function load(Atomik_Model $model)
    {
        $collection = $this->_createQuery($model)->execute();
		$model->_set($this->_name, $collection);
    }
}