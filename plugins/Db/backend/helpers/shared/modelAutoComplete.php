<?php

Atomik::loadHelper('autoComplete');

class ModelAutoCompleteHelper extends AutoCompleteHelper
{
	protected $field;
	
	public function modelAutoComplete($id, $model, $field)
	{
		$this->field = $field;
		$query = Atomik_Model_Query::create()->from($model)->where('LOWER(' . $field . ') LIKE ?')->limit(10);
		return $this->autoComplete($id, $query);
	}
	
	public function getFilteredData($query, $value)
	{
		$query->setParam(0, $value . '%');
		$rows = Atomik_Model::query($query);
		
		$data = array();
		foreach ($rows as $row) {
			$data[$row->getPrimaryKey()] = $row->{$this->field};
		}
		
		return $data;
	}
}