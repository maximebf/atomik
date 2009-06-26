<?php

class ModelFilterTimestampHelper
{
	public function modelFilterTimestamp($column)
	{
		return '<ul>'
			. '<li><a class="current" href="' . $this->_getUrl($column, 'all') . '">Any date</a></li>'
			. '<li><a href="' . $this->_getUrl($column, 'today') . '">Today</a></li>'
			. '<li><a href="' . $this->_getUrl($column, '7days') . '">Past 7 days</a></li>'
			. '<li><a href="' . $this->_getUrl($column, 'month') . '">This month</a></li>'
			. '<li><a href="' . $this->_getUrl($column, 'year') . '">This year</a></li></ul>';
	}
	
	protected function _getUrl($column, $value)
	{
		return Atomik::pluginUrl(null, array('filterBy' => $column, 'filter' => $value));
	}
}