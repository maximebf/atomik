<?php

class Atomik_Model_Association_ManyToOne extends Atomik_Model_Association
{
    protected function _setup()
    {
		$this->setTargetFieldName($this->_target->getPrimaryKeyField()->getName());
		$this->setSourceFieldName(strtolower($this->_target->getName()) . '_' . $this->_targetFieldName);
    }
    
    public function load(Atomik_Model $model)
    {
        $collection = $this->_createQuery($model)->limit(1)->execute();
		$model->_set($this->_name, $collection->getFirst());
    }
}