<?php

class IndexController
{
	public function index()
	{
		$this->messages = db_select_all('messages');
	}
	
	public function _test()
	{
		print 'private';
	}
}
