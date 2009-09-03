<?php

class Atomik_Model_Query_Filter_Bool extends Atomik_Model_Query_Filter_Abstract
{
	public function getQueryCondition()
	{
		if ($this->_value == 'enabled') {
			return array($this->_field->name => 1);
		}
		return array($this->_field->name => 0);
	}
	
	public function getPossibleValues()
	{
		return array(
			'Both' => '',
			'Enabled' => 'enabled',
			'Disabled' => 'disabled'
		);
	}
}