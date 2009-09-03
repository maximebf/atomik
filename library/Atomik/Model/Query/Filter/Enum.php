<?php

class Atomik_Model_Query_Filter_Enum extends Atomik_Model_Query_Filter_Abstract
{
	public function getQueryCondition()
	{
		return '';
	}
	
	public function getPossibleValues()
	{
		return array_merge(
			array('All' => ''),
			array_flip(array_map('trim', explode(',', $this->_field->getOption('options'))))
		);
	}
}