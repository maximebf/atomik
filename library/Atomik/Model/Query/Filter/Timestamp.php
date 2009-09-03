<?php

class Atomik_Model_Query_Filter_Timestamp extends Atomik_Model_Query_Filter_Abstract
{
	public function getQueryCondition()
	{
		return '';
	}
	
	public function getPossibleValues()
	{
		return array(
			'Any date' => '',
			'Today' => 'today',
			'Past 7 days' => '7days',
			'This month' => 'month',
			'This year' => 'year'
		);
	}
}