<?php

class Atomik_Model_Association_OneToMany extends Atomik_Model_Association
{
    protected function _setup()
    {
		$this->setSourceFieldName($this->_source->getPrimaryKeyField()->getName());
		$sourceName = str_replace('_', '', $this->_source->getName());
		$sourceName{0} = strtolower($sourceName{0});
		$this->setTargetFieldName($sourceName . ucfirst($this->_sourceFieldName));
    }
    
    public function load(Atomik_Model $model)
    {
        $collection = $this->_createQuery($model)->execute();
		$model->_set($this->_name, $collection);
    }
}